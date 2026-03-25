<?php

declare(strict_types=1);

namespace Tests\Feature\GraphQL;

use App\Models\Translation;
use App\Models\User;
use App\Models\Word;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Nuwave\Lighthouse\Testing\MakesGraphQLRequests;
use Tests\TestCase;

class MutationGraphQLTest extends TestCase
{
    use MakesGraphQLRequests;
    use RefreshDatabase;

    private User $user;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    // -------------------------------------------------------------------------
    // Word — createWord
    // -------------------------------------------------------------------------

    public function test_authenticated_user_can_create_word(): void
    {
        $this->actingAs($this->user, 'api');

        $response = $this->graphQL(/** @lang GraphQL */ '
            mutation {
                createWord(english_word: "Algorithm") {
                    id
                    english_word
                }
            }
        ');

        $response->assertJsonPath('data.createWord.english_word', 'Algorithm');
        $this->assertDatabaseHas('words', ['english_word' => 'Algorithm']);
    }

    public function test_unauthenticated_user_cannot_create_word(): void
    {
        $response = $this->graphQL(/** @lang GraphQL */ '
            mutation {
                createWord(english_word: "Algorithm") {
                    id
                }
            }
        ');

        $response->assertGraphQLErrorMessage('Unauthenticated.');
        $this->assertDatabaseMissing('words', ['english_word' => 'Algorithm']);
    }

    public function test_create_word_validates_required_english_word(): void
    {
        $this->actingAs($this->user, 'api');

        $response = $this->graphQL(/** @lang GraphQL */ '
            mutation {
                createWord(english_word: "") {
                    id
                }
            }
        ');

        $response->assertGraphQLValidationError('english_word', 'The english word field is required.');
    }

    public function test_create_word_validates_max_length(): void
    {
        $this->actingAs($this->user, 'api');

        $response = $this->graphQL(/** @lang GraphQL */ '
            mutation {
                createWord(english_word: "' . str_repeat('a', 256) . '") {
                    id
                }
            }
        ');

        $response->assertGraphQLValidationError('english_word', 'The english word field must not be greater than 255 characters.');
    }

    // -------------------------------------------------------------------------
    // Word — updateWord
    // -------------------------------------------------------------------------

    public function test_authenticated_user_can_update_word(): void
    {
        $this->actingAs($this->user, 'api');
        $word = Word::factory()->create(['english_word' => 'Old']);

        $response = $this->graphQL(/** @lang GraphQL */ '
            mutation UpdateWord($id: ID!, $english_word: String) {
                updateWord(id: $id, english_word: $english_word) {
                    id
                    english_word
                }
            }
        ', ['id' => $word->id, 'english_word' => 'Updated']);

        $response->assertJsonPath('data.updateWord.english_word', 'Updated');
        $this->assertDatabaseHas('words', ['id' => $word->id, 'english_word' => 'Updated']);
    }

    public function test_unauthenticated_user_cannot_update_word(): void
    {
        $word = Word::factory()->create(['english_word' => 'Original']);

        $response = $this->graphQL(/** @lang GraphQL */ '
            mutation UpdateWord($id: ID!) {
                updateWord(id: $id, english_word: "Hacked") {
                    id
                }
            }
        ', ['id' => $word->id]);

        $response->assertGraphQLErrorMessage('Unauthenticated.');
        $this->assertDatabaseHas('words', ['id' => $word->id, 'english_word' => 'Original']);
    }

    // -------------------------------------------------------------------------
    // Word — deleteWord
    // -------------------------------------------------------------------------

    public function test_authenticated_user_can_delete_word(): void
    {
        $this->actingAs($this->user, 'api');
        $word = Word::factory()->create();

        $response = $this->graphQL(/** @lang GraphQL */ '
            mutation DeleteWord($id: ID!) {
                deleteWord(id: $id) {
                    id
                    english_word
                }
            }
        ', ['id' => $word->id]);

        $response->assertJsonPath('data.deleteWord.id', (string) $word->id);
        $this->assertDatabaseMissing('words', ['id' => $word->id]);
    }

    public function test_unauthenticated_user_cannot_delete_word(): void
    {
        $word = Word::factory()->create();

        $response = $this->graphQL(/** @lang GraphQL */ '
            mutation DeleteWord($id: ID!) {
                deleteWord(id: $id) {
                    id
                }
            }
        ', ['id' => $word->id]);

        $response->assertGraphQLErrorMessage('Unauthenticated.');
        $this->assertDatabaseHas('words', ['id' => $word->id]);
    }

    // -------------------------------------------------------------------------
    // Translation — createTranslation
    // -------------------------------------------------------------------------

    public function test_authenticated_user_can_create_translation(): void
    {
        $this->actingAs($this->user, 'api');
        $word = Word::factory()->create();

        $response = $this->graphQL(/** @lang GraphQL */ '
            mutation CreateTranslation($word_id: ID!, $language: String!, $translation: String!) {
                createTranslation(word_id: $word_id, language: $language, translation: $translation) {
                    id
                    language
                    translation
                }
            }
        ', ['word_id' => $word->id, 'language' => 'es', 'translation' => 'Algoritmo']);

        $response->assertJsonPath('data.createTranslation.language', 'es');
        $response->assertJsonPath('data.createTranslation.translation', 'Algoritmo');
        $this->assertDatabaseHas('translations', [
            'word_id' => $word->id,
            'language' => 'es',
            'translation' => 'Algoritmo',
        ]);
    }

    public function test_unauthenticated_user_cannot_create_translation(): void
    {
        $word = Word::factory()->create();

        $response = $this->graphQL(/** @lang GraphQL */ '
            mutation {
                createTranslation(word_id: "' . $word->id . '", language: "es", translation: "Algoritmo") {
                    id
                }
            }
        ');

        $response->assertGraphQLErrorMessage('Unauthenticated.');
    }

    public function test_create_translation_validates_word_exists(): void
    {
        $this->actingAs($this->user, 'api');

        $response = $this->graphQL(/** @lang GraphQL */ '
            mutation {
                createTranslation(word_id: "99999", language: "es", translation: "Test") {
                    id
                }
            }
        ');

        $response->assertGraphQLValidationError('word_id', 'The selected word id is invalid.');
    }

    // -------------------------------------------------------------------------
    // Translation — updateTranslation
    // -------------------------------------------------------------------------

    public function test_authenticated_user_can_update_translation(): void
    {
        $this->actingAs($this->user, 'api');
        $word        = Word::factory()->create();
        $translation = Translation::factory()->for($word)->create([
            'language' => 'es',
            'translation' => 'Old',
        ]);

        $response = $this->graphQL(/** @lang GraphQL */ '
            mutation UpdateTranslation($id: ID!, $translation: String) {
                updateTranslation(id: $id, translation: $translation) {
                    id
                    translation
                }
            }
        ', ['id' => $translation->id, 'translation' => 'Updated']);

        $response->assertJsonPath('data.updateTranslation.translation', 'Updated');
        $this->assertDatabaseHas('translations', ['id' => $translation->id, 'translation' => 'Updated']);
    }

    public function test_unauthenticated_user_cannot_update_translation(): void
    {
        $word        = Word::factory()->create();
        $translation = Translation::factory()->for($word)->create(['translation' => 'Original']);

        $response = $this->graphQL(/** @lang GraphQL */ '
            mutation UpdateTranslation($id: ID!) {
                updateTranslation(id: $id, translation: "Hacked") {
                    id
                }
            }
        ', ['id' => $translation->id]);

        $response->assertGraphQLErrorMessage('Unauthenticated.');
        $this->assertDatabaseHas('translations', ['id' => $translation->id, 'translation' => 'Original']);
    }

    // -------------------------------------------------------------------------
    // Translation — deleteTranslation
    // -------------------------------------------------------------------------

    public function test_authenticated_user_can_delete_translation(): void
    {
        $this->actingAs($this->user, 'api');
        $word        = Word::factory()->create();
        $translation = Translation::factory()->for($word)->create(['language' => 'es']);

        $response = $this->graphQL(/** @lang GraphQL */ '
            mutation DeleteTranslation($id: ID!) {
                deleteTranslation(id: $id) {
                    id
                    language
                }
            }
        ', ['id' => $translation->id]);

        $response->assertJsonPath('data.deleteTranslation.id', (string) $translation->id);
        $this->assertDatabaseMissing('translations', ['id' => $translation->id]);
    }

    public function test_unauthenticated_user_cannot_delete_translation(): void
    {
        $word        = Word::factory()->create();
        $translation = Translation::factory()->for($word)->create();

        $response = $this->graphQL(/** @lang GraphQL */ '
            mutation DeleteTranslation($id: ID!) {
                deleteTranslation(id: $id) {
                    id
                }
            }
        ', ['id' => $translation->id]);

        $response->assertGraphQLErrorMessage('Unauthenticated.');
        $this->assertDatabaseHas('translations', ['id' => $translation->id]);
    }

    // -------------------------------------------------------------------------
    // Cache invalidation
    // -------------------------------------------------------------------------

    public function test_create_word_invalidates_words_list_cache(): void
    {
        $this->actingAs($this->user, 'api');
        Word::factory()->count(2)->create();

        // Prime the words list cache
        $this->graphQL(/** @lang GraphQL */ '{ words(first: 10) { data { id } paginatorInfo { total } } }')
            ->assertJsonPath('data.words.paginatorInfo.total', 2);

        // Mutation invalidates cache
        $this->graphQL(/** @lang GraphQL */ '
            mutation { createWord(english_word: "NewWord") { id } }
        ');

        // Next query must hit DB and return 3
        $this->graphQL(/** @lang GraphQL */ '{ words(first: 10) { data { id } paginatorInfo { total } } }')
            ->assertJsonPath('data.words.paginatorInfo.total', 3);
    }

    public function test_delete_translation_invalidates_translations_cache(): void
    {
        $this->actingAs($this->user, 'api');
        $word        = Word::factory()->create();
        $translation = Translation::factory()->for($word)->create(['language' => 'es']);

        // Prime the translations cache
        $this->graphQL(/** @lang GraphQL */ '{ translations { id } }')
            ->assertJsonCount(1, 'data.translations');

        // Delete invalidates cache
        $this->graphQL(/** @lang GraphQL */ '
            mutation DeleteTranslation($id: ID!) {
                deleteTranslation(id: $id) { id }
            }
        ', ['id' => $translation->id]);

        // Next query must reflect deletion
        $this->graphQL(/** @lang GraphQL */ '{ translations { id } }')
            ->assertJsonCount(0, 'data.translations');
    }
}
