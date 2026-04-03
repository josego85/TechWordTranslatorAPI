<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Prism\Prism\Enums\Provider;
use Prism\Prism\Facades\Prism;
use Throwable;

class ClassificationService
{
    /** @var list<string> */
    private const array ALLOWED_SLUGS = [
        'networking',
        'databases',
        'security',
        'algorithms',
        'data-structures',
        'operating-systems',
        'programming-languages',
        'web',
        'cloud',
        'devops',
        'hardware',
        'artificial-intelligence',
        'other',
    ];

    /**
     * Classify a technical word into one or more categories.
     *
     * Returns a list of valid category slugs (1–3).
     * Returns ['other'] if the LLM response cannot be parsed.
     * Returns [] if the LLM is unreachable (word is saved without categories).
     *
     * @return list<string>
     */
    public function classify(string $englishWord): array
    {
        $prompt = $this->buildPrompt($englishWord);

        try {
            /** @var string $model */
            $model  = config('classification.model', 'llama3.2');
            $result = Prism::text()
                ->using(Provider::Ollama, $model)
                ->withPrompt($prompt)
                ->asText();

            return $this->parseSlugs($result->text);
        } catch (Throwable $e) {
            Log::warning('ClassificationService: LLM call failed', [
                'word' => $englishWord,
                'error' => $e->getMessage(),
            ]);

            return [];
        }
    }

    private function buildPrompt(string $englishWord): string
    {
        $allowed = implode(', ', self::ALLOWED_SLUGS);

        return <<<PROMPT
        You are a technical term classifier for an IT glossary.
        Given a technical word or acronym, respond with ONLY a comma-separated list of categories
        that apply, chosen exclusively from this list:
        {$allowed}

        Rules:
        - Choose between 1 and 3 categories maximum
        - Use only slugs from the list above, no variations
        - No explanation, no punctuation other than commas
        - If truly unknown, respond with: other

        Word: {$englishWord}
        Categories:
        PROMPT;
    }

    /**
     * @return list<string>
     */
    private function parseSlugs(string $raw): array
    {
        $parts = array_map('trim', explode(',', strtolower(trim($raw))));

        $valid = array_values(
            array_filter($parts, fn (string $s) => in_array($s, self::ALLOWED_SLUGS, true))
        );

        if ($valid === []) {
            Log::warning('ClassificationService: no valid slugs in LLM response', ['raw' => $raw]);

            return ['other'];
        }

        return array_slice($valid, 0, 3);
    }
}
