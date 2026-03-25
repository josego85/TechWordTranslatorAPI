<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Exceptions\WordNotFoundException;
use App\Models\Translation;
use App\Models\User;
use App\Models\Word;
use App\Services\WordService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class WordApiTest extends TestCase
{
    use RefreshDatabase;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware();
        $this->actingAs(User::factory()->create(), 'api');
        Log::spy();
    }

    public function test_get_words_returns_paginated_list(): void
    {
        // Create words with unique translations to avoid constraint violations
        for ($i = 0; $i < 5; $i++) {
            $word = Word::factory()->create();
            Translation::factory()->for($word)->create(['language' => 'es']);
            Translation::factory()->for($word)->create(['language' => 'de']);
        }

        $response = $this->getJson('/api/v1/words?per_page=10&page=1');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'word',
                        'created_at',
                        'updated_at',
                        'translations' => [
                            '*' => [
                                'language',
                                'translation',
                            ],
                        ],
                    ],
                ],
                'links',
                'meta',
            ]);
    }

    public function test_get_words_with_search_filters_results(): void
    {
        Word::factory()->create(['english_word' => 'Computer']);
        Word::factory()->create(['english_word' => 'Keyboard']);
        Word::factory()->create(['english_word' => 'Mouse']);

        $response = $this->getJson('/api/v1/words?search=Computer');

        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertCount(1, $data);
        $this->assertEquals('Computer', $data[0]['word']);
    }

    public function test_get_single_word_returns_word_with_translations(): void
    {
        $word = Word::factory()
            ->has(Translation::factory()->state(['language' => 'es', 'translation' => 'Computadora']))
            ->create(['english_word' => 'Computer']);

        $response = $this->getJson("/api/v1/words/{$word->id}");

        $response->assertStatus(200)
            ->assertJson([
                'id' => $word->id,
                'word' => 'Computer',
                'translations' => [
                    [
                        'language' => 'es',
                        'translation' => 'Computadora',
                    ],
                ],
            ]);
    }

    public function test_get_single_word_returns_422_when_not_found(): void
    {
        $response = $this->getJson('/api/v1/words/9999');

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['word']);
    }

    public function test_create_word_successfully(): void
    {
        $data = [
            'english_word' => 'Test',
        ];

        $response = $this->postJson('/api/v1/words', $data);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'id',
                'word',
                'created_at',
                'updated_at',
            ]);

        $this->assertDatabaseHas('words', [
            'english_word' => 'Test',
        ]);
    }

    public function test_create_word_fails_without_english_word(): void
    {
        $response = $this->postJson('/api/v1/words', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['english_word']);
    }

    public function test_update_word_successfully(): void
    {
        $word = Word::factory()->create(['english_word' => 'Old Name']);

        $data = [
            'english_word' => 'New Name',
        ];

        $response = $this->putJson("/api/v1/words/{$word->id}", $data);

        $response->assertStatus(200)
            ->assertJson([
                'id' => $word->id,
                'word' => 'New Name',
            ]);

        $this->assertDatabaseHas('words', [
            'id' => $word->id,
            'english_word' => 'New Name',
        ]);
    }

    public function test_update_word_returns_422_when_not_found(): void
    {
        $response = $this->putJson('/api/v1/words/9999', [
            'english_word' => 'Test',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['word']);
    }

    public function test_delete_word_successfully(): void
    {
        $word = Word::factory()->create();

        $response = $this->deleteJson("/api/v1/words/{$word->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('words', [
            'id' => $word->id,
        ]);
    }

    public function test_delete_word_returns_404_when_not_found(): void
    {
        $response = $this->deleteJson('/api/v1/words/9999');

        $response->assertStatus(404);
    }

    public function test_show_word_exception_returns_404(): void
    {
        $word = Word::factory()->create();

        // Mock the service to throw a not found exception
        $this->mock(WordService::class, function($mock) use ($word) {
            $mock->shouldReceive('get')
                ->with($word->id)
                ->once()
                ->andThrow(new WordNotFoundException('Word not found'));
        });

        $response = $this->getJson("/api/v1/words/{$word->id}");

        $response->assertStatus(404)
            ->assertJson([
                'message' => 'Word not found',
            ]);
    }

    public function test_create_word_logs_info(): void
    {
        $response = $this->postJson('/api/v1/words', ['english_word' => 'Refactor']);

        $response->assertStatus(201);

        Log::shouldHaveReceived('info')
            ->atLeast()->once()
            ->with('Word created', \Mockery::on(fn ($context) => $context['english_word'] === 'Refactor' && isset($context['word_id']) && isset($context['ip'])));
    }

    public function test_update_word_logs_info(): void
    {
        $word = Word::factory()->create(['english_word' => 'Old']);

        $this->putJson("/api/v1/words/{$word->id}", ['english_word' => 'New'])->assertStatus(200);

        Log::shouldHaveReceived('info')
            ->atLeast()->once()
            ->with('Word updated', \Mockery::on(fn ($context) => $context['word_id'] === $word->id && $context['english_word'] === 'New' && isset($context['ip'])));
    }

    public function test_delete_word_logs_warning(): void
    {
        $word = Word::factory()->create();

        $this->deleteJson("/api/v1/words/{$word->id}")->assertStatus(204);

        Log::shouldHaveReceived('warning')
            ->atLeast()->once()
            ->with('Word deleted', \Mockery::on(fn ($context) => $context['word_id'] === $word->id && isset($context['ip'])));
    }

    public function test_create_word_exception_returns_404(): void
    {
        // Mock the service to throw a not found exception
        $this->mock(WordService::class, function($mock) {
            $mock->shouldReceive('create')
                ->once()
                ->andThrow(new WordNotFoundException('Failed to create word'));
        });

        $data = [
            'english_word' => 'Test',
        ];

        $response = $this->postJson('/api/v1/words', $data);

        $response->assertStatus(404)
            ->assertJson([
                'message' => 'Failed to create word',
            ]);
    }

    public function test_update_word_exception_returns_404(): void
    {
        $word = Word::factory()->create();

        // Mock the service to throw a not found exception
        $this->mock(WordService::class, function($mock) use ($word) {
            $mock->shouldReceive('update')
                ->with($word->id, \Mockery::any())
                ->once()
                ->andThrow(new WordNotFoundException('Word not found'));
        });

        $data = [
            'english_word' => 'Test',
        ];

        $response = $this->putJson("/api/v1/words/{$word->id}", $data);

        $response->assertStatus(404)
            ->assertJson([
                'message' => 'Word not found',
            ]);
    }
}
