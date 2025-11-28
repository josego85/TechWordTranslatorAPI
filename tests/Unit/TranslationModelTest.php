<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\Translation;
use App\Models\Word;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TranslationModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_translation_belongs_to_word(): void
    {
        $word        = Word::factory()->create();
        $translation = Translation::factory()->for($word)->create();

        $this->assertInstanceOf(Word::class, $translation->word);
        $this->assertEquals($word->id, $translation->word->id);
    }

    public function test_language_scope_filters_by_language(): void
    {
        $word = Word::factory()->create();
        Translation::factory()->for($word)->create(['language' => 'es']);
        Translation::factory()->for($word)->create(['language' => 'de']);
        Translation::factory()->for($word)->create(['language' => 'fr']);

        $results = Translation::language('es')->get();

        $this->assertCount(1, $results);
        $this->assertEquals('es', $results->first()->language);
    }
}
