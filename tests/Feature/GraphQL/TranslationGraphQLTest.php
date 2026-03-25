<?php

declare(strict_types=1);

namespace Tests\Feature\GraphQL;

use App\Models\Translation;
use App\Models\Word;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Nuwave\Lighthouse\Testing\MakesGraphQLRequests;
use Tests\TestCase;

class TranslationGraphQLTest extends TestCase
{
    use MakesGraphQLRequests;
    use RefreshDatabase;

    public function test_can_query_all_translations(): void
    {
        $word = Word::factory()->create();
        Translation::factory()->for($word)->create(['language' => 'es']);
        Translation::factory()->for($word)->create(['language' => 'de']);

        $response = $this->graphQL(/** @lang GraphQL */ '
            {
                translations {
                    id
                    language
                    translation
                }
            }
        ');

        $this->assertCount(2, $response->json('data.translations'));
        $response->assertJsonStructure([
            'data' => ['translations' => [['id', 'language', 'translation']]],
        ]);
    }

    public function test_can_filter_translations_by_language(): void
    {
        $word = Word::factory()->create();
        Translation::factory()->for($word)->create(['language' => 'es', 'translation' => 'Caché']);
        Translation::factory()->for($word)->create(['language' => 'de', 'translation' => 'Zwischenspeicher']);

        $response = $this->graphQL(/** @lang GraphQL */ '
            {
                translations(language: "es") {
                    id
                    language
                    translation
                }
            }
        ');

        $this->assertCount(1, $response->json('data.translations'));
        $response->assertJsonPath('data.translations.0.language', 'es');
        $response->assertJsonPath('data.translations.0.translation', 'Caché');
    }

    public function test_can_filter_translations_by_word_id(): void
    {
        $wordA = Word::factory()->create();
        $wordB = Word::factory()->create();
        Translation::factory()->for($wordA)->create(['language' => 'es']);
        Translation::factory()->for($wordA)->create(['language' => 'de']);
        Translation::factory()->for($wordB)->create(['language' => 'es']);

        $response = $this->graphQL(/** @lang GraphQL */ '
            query GetTranslations($wordId: ID!) {
                translations(word_id: $wordId) {
                    id
                    word_id
                }
            }
        ', ['wordId' => $wordA->id]);

        $this->assertCount(2, $response->json('data.translations'));
        $response->assertJsonPath('data.translations.0.word_id', (string) $wordA->id);
    }

    public function test_can_query_single_translation_by_id(): void
    {
        $word        = Word::factory()->create();
        $translation = Translation::factory()->for($word)->create([
            'language' => 'es',
            'translation' => 'Servidor',
        ]);

        $response = $this->graphQL(/** @lang GraphQL */ '
            query GetTranslation($id: ID!) {
                translation(id: $id) {
                    id
                    language
                    translation
                }
            }
        ', ['id' => $translation->id]);

        $response->assertJsonPath('data.translation.id', (string) $translation->id);
        $response->assertJsonPath('data.translation.language', 'es');
        $response->assertJsonPath('data.translation.translation', 'Servidor');
    }

    public function test_translation_returns_null_for_nonexistent_id(): void
    {
        $response = $this->graphQL(/** @lang GraphQL */ '
            {
                translation(id: 99999) {
                    id
                    language
                }
            }
        ');

        $response->assertJsonPath('data.translation', null);
    }

    public function test_translation_includes_word_relationship(): void
    {
        $word        = Word::factory()->create(['english_word' => 'Router']);
        $translation = Translation::factory()->for($word)->create(['language' => 'es']);

        $response = $this->graphQL(/** @lang GraphQL */ '
            query GetTranslation($id: ID!) {
                translation(id: $id) {
                    id
                    language
                    word {
                        id
                        english_word
                    }
                }
            }
        ', ['id' => $translation->id]);

        $response->assertJsonPath('data.translation.word.english_word', 'Router');
        $response->assertJsonPath('data.translation.word.id', (string) $word->id);
    }

    public function test_can_query_translations_by_language(): void
    {
        $wordA = Word::factory()->create();
        $wordB = Word::factory()->create();
        Translation::factory()->for($wordA)->create(['language' => 'de', 'translation' => 'Netzwerk']);
        Translation::factory()->for($wordB)->create(['language' => 'de', 'translation' => 'Datenbank']);
        Translation::factory()->for($wordA)->create(['language' => 'es', 'translation' => 'Red']);

        $response = $this->graphQL(/** @lang GraphQL */ '
            {
                translationsByLanguage(language: "de") {
                    id
                    language
                    translation
                }
            }
        ');

        $this->assertCount(2, $response->json('data.translationsByLanguage'));
        $response->assertJsonPath('data.translationsByLanguage.0.language', 'de');
    }

    public function test_translations_by_language_returns_empty_for_unknown_language(): void
    {
        $word = Word::factory()->create();
        Translation::factory()->for($word)->create(['language' => 'es']);

        $response = $this->graphQL(/** @lang GraphQL */ '
            {
                translationsByLanguage(language: "zh") {
                    id
                }
            }
        ');

        $response->assertJsonPath('data.translationsByLanguage', []);
    }

    public function test_translation_query_result_is_served_from_cache_on_second_call(): void
    {
        $word        = Word::factory()->create();
        $translation = Translation::factory()->for($word)->create([
            'language' => 'es',
            'translation' => 'Cortafuegos',
        ]);

        $query = /** @lang GraphQL */ '
            query GetTranslation($id: ID!) {
                translation(id: $id) { id language translation }
            }
        ';

        // First call — populates cache
        $this->graphQL($query, ['id' => $translation->id])
            ->assertJsonPath('data.translation.translation', 'Cortafuegos');

        // Second call — served from cache, zero DB queries issued
        DB::enableQueryLog();
        $this->graphQL($query, ['id' => $translation->id])
            ->assertJsonPath('data.translation.translation', 'Cortafuegos');

        $this->assertCount(0, DB::getQueryLog());
        DB::disableQueryLog();
    }

    public function test_translations_list_query_result_is_served_from_cache_on_second_call(): void
    {
        $word = Word::factory()->create();
        Translation::factory()->for($word)->create(['language' => 'es']);
        Translation::factory()->for($word)->create(['language' => 'de']);

        $query = /** @lang GraphQL */ '
            {
                translations {
                    id language translation
                }
            }
        ';

        // First call — populates cache
        $firstResponse = $this->graphQL($query);
        $this->assertCount(2, $firstResponse->json('data.translations'));

        // Second call — served from cache, zero DB queries issued
        DB::enableQueryLog();
        $this->graphQL($query);
        $this->assertCount(0, DB::getQueryLog());
        DB::disableQueryLog();
    }
}
