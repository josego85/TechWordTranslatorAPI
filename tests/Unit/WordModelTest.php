<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\Translation;
use App\Models\Word;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WordModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_word_has_translations_relationship(): void
    {
        $word = Word::factory()->create();
        Translation::factory()->for($word)->create(['language' => 'es', 'translation' => 'prueba']);
        Translation::factory()->for($word)->create(['language' => 'de', 'translation' => 'test']);

        $this->assertCount(2, $word->translations);
        $this->assertInstanceOf(Translation::class, $word->translations->first());
    }

    public function test_get_translation_returns_translation_for_specific_language(): void
    {
        $word = Word::factory()->create();
        Translation::factory()->for($word)->create(['language' => 'es', 'translation' => 'hola']);
        Translation::factory()->for($word)->create(['language' => 'de', 'translation' => 'hallo']);

        $translation = $word->getTranslation('es');

        $this->assertNotNull($translation);
        $this->assertEquals('hola', $translation->translation);
        $this->assertEquals('es', $translation->language);
    }

    public function test_get_translation_returns_null_when_language_not_found(): void
    {
        $word = Word::factory()->create();
        Translation::factory()->for($word)->create(['language' => 'es', 'translation' => 'hola']);

        $translation = $word->getTranslation('fr');

        $this->assertNull($translation);
    }

    public function test_set_translation_creates_new_translation(): void
    {
        $word = Word::factory()->create();

        $translation = $word->setTranslation('fr', 'bonjour');

        $this->assertInstanceOf(Translation::class, $translation);
        $this->assertEquals('fr', $translation->language);
        $this->assertEquals('bonjour', $translation->translation);
        $this->assertEquals($word->id, $translation->word_id);

        $this->assertDatabaseHas('translations', [
            'word_id' => $word->id,
            'language' => 'fr',
            'translation' => 'bonjour',
        ]);
    }

    public function test_set_translation_updates_existing_translation(): void
    {
        $word = Word::factory()->create();
        $existingTranslation = Translation::factory()->for($word)->create([
            'language' => 'es',
            'translation' => 'old',
        ]);

        $translation = $word->setTranslation('es', 'new');

        $this->assertEquals($existingTranslation->id, $translation->id);
        $this->assertEquals('new', $translation->translation);

        $this->assertDatabaseHas('translations', [
            'id' => $existingTranslation->id,
            'translation' => 'new',
        ]);
    }

    public function test_search_scope_finds_words_by_english_word(): void
    {
        Word::factory()->create(['english_word' => 'Computer']);
        Word::factory()->create(['english_word' => 'Keyboard']);
        Word::factory()->create(['english_word' => 'Mouse']);

        $results = Word::search('Computer')->get();

        $this->assertCount(1, $results);
        $this->assertEquals('Computer', $results->first()->english_word);
    }

    public function test_search_scope_finds_words_by_translation(): void
    {
        $word1 = Word::factory()->create(['english_word' => 'Computer']);
        $word2 = Word::factory()->create(['english_word' => 'Keyboard']);

        Translation::factory()->for($word1)->create(['language' => 'es', 'translation' => 'Computadora']);
        Translation::factory()->for($word2)->create(['language' => 'es', 'translation' => 'Teclado']);

        $results = Word::search('Computadora')->get();

        $this->assertCount(1, $results);
        $this->assertEquals('Computer', $results->first()->english_word);
    }

    public function test_search_scope_is_case_insensitive(): void
    {
        Word::factory()->create(['english_word' => 'Computer']);

        $results = Word::search('computer')->get();

        $this->assertCount(1, $results);
    }
}
