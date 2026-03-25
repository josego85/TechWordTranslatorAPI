<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Translation;
use Illuminate\Support\Facades\Log;

class TranslationObserver
{
    public function created(Translation $translation): void
    {
        Log::info('Translation created', [
            'translation_id' => $translation->id,
            'word_id' => $translation->word_id,
            'language' => $translation->language,
            'ip' => request()->ip() ?? 'cli',
        ]);
    }

    public function updated(Translation $translation): void
    {
        Log::info('Translation updated', [
            'translation_id' => $translation->id,
            'word_id' => $translation->word_id,
            'language' => $translation->language,
            'ip' => request()->ip() ?? 'cli',
        ]);
    }

    public function deleted(Translation $translation): void
    {
        Log::warning('Translation deleted', [
            'translation_id' => $translation->id,
            'ip' => request()->ip() ?? 'cli',
        ]);
    }
}
