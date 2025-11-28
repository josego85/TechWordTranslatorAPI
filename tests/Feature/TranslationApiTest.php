<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Translation;
use App\Models\Word;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TranslationApiTest extends TestCase
{
    use RefreshDatabase;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware();
    }

    public function test_get_translations_returns_paginated_list(): void
    {
        $word1 = Word::factory()->create();
        $word2 = Word::factory()->create();
        Translation::factory()->for($word1)->create(['language' => 'es']);
        Translation::factory()->for($word1)->create(['language' => 'de']);
        Translation::factory()->for($word2)->create(['language' => 'fr']);
        Translation::factory()->for($word2)->create(['language' => 'it']);
        Translation::factory()->for($word2)->create(['language' => 'pt']);

        $response = $this->getJson('/api/v1/translations?per_page=10&page=1');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'word_id',
                        'language',
                        'translation',
                        'created_at',
                        'updated_at',
                    ],
                ],
                'links',
                'meta',
            ]);
    }

    public function test_get_single_translation_returns_translation(): void
    {
        $word = Word::factory()->create();
        $translation = Translation::factory()->for($word)->create([
            'language' => 'es',
            'translation' => 'Prueba',
        ]);

        $response = $this->getJson("/api/v1/translations/{$translation->id}");

        $response->assertStatus(200)
            ->assertJson([
                'id' => $translation->id,
                'word_id' => $word->id,
                'language' => 'es',
                'translation' => 'Prueba',
            ]);
    }

    public function test_get_single_translation_returns_422_when_not_found(): void
    {
        $response = $this->getJson('/api/v1/translations/9999');

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['translation']);
    }

    public function test_create_translation_successfully(): void
    {
        $word = Word::factory()->create();

        $data = [
            'word_id' => $word->id,
            'language' => 'fr',
            'translation' => 'Test',
        ];

        $response = $this->postJson('/api/v1/translations', $data);

        $response->assertStatus(201)
            ->assertJson([
                'word_id' => $word->id,
                'language' => 'fr',
                'translation' => 'Test',
            ]);

        $this->assertDatabaseHas('translations', [
            'word_id' => $word->id,
            'language' => 'fr',
            'translation' => 'Test',
        ]);
    }

    public function test_create_translation_fails_without_required_fields(): void
    {
        $response = $this->postJson('/api/v1/translations', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['word_id', 'language', 'translation']);
    }

    public function test_update_translation_successfully(): void
    {
        $word = Word::factory()->create();
        $translation = Translation::factory()->for($word)->create([
            'language' => 'es',
            'translation' => 'Old Translation',
        ]);

        $data = [
            'word_id' => $word->id,
            'language' => 'es',
            'translation' => 'New Translation',
        ];

        $response = $this->putJson("/api/v1/translations/{$translation->id}", $data);

        $response->assertStatus(200)
            ->assertJson([
                'id' => $translation->id,
                'translation' => 'New Translation',
            ]);

        $this->assertDatabaseHas('translations', [
            'id' => $translation->id,
            'translation' => 'New Translation',
        ]);
    }

    public function test_update_translation_returns_422_when_not_found(): void
    {
        $word = Word::factory()->create();

        $response = $this->putJson('/api/v1/translations/9999', [
            'word_id' => $word->id,
            'language' => 'es',
            'translation' => 'Test',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['translation_id']);
    }

    public function test_delete_translation_successfully(): void
    {
        $word = Word::factory()->create();
        $translation = Translation::factory()->for($word)->create();

        $response = $this->deleteJson("/api/v1/translations/{$translation->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('translations', [
            'id' => $translation->id,
        ]);
    }

    public function test_delete_translation_returns_404_when_not_found(): void
    {
        $response = $this->deleteJson('/api/v1/translations/9999');

        $response->assertStatus(404);
    }
}
