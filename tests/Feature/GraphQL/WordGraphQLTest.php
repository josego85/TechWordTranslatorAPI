<?php

declare(strict_types=1);

namespace Tests\Feature\GraphQL;

use App\Models\Translation;
use App\Models\Word;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Nuwave\Lighthouse\Testing\MakesGraphQLRequests;
use Tests\TestCase;

class WordGraphQLTest extends TestCase
{
    use MakesGraphQLRequests;
    use RefreshDatabase;

    public function test_can_query_paginated_words(): void
    {
        Word::factory()->count(3)->create();

        $response = $this->graphQL(/** @lang GraphQL */ '
            {
                words(first: 10) {
                    data {
                        id
                        english_word
                    }
                    paginatorInfo {
                        total
                        currentPage
                    }
                }
            }
        ');

        $response->assertJsonPath('data.words.paginatorInfo.total', 3);
        $response->assertJsonStructure([
            'data' => [
                'words' => [
                    'data'          => [['id', 'english_word']],
                    'paginatorInfo' => ['total', 'currentPage'],
                ],
            ],
        ]);
    }

    public function test_can_query_words_with_search(): void
    {
        Word::factory()->create(['english_word' => 'Algorithm']);
        Word::factory()->create(['english_word' => 'Database']);

        $response = $this->graphQL(/** @lang GraphQL */ '
            {
                words(first: 10, search: "Algorithm") {
                    data {
                        id
                        english_word
                    }
                    paginatorInfo {
                        total
                    }
                }
            }
        ');

        $response->assertJsonPath('data.words.paginatorInfo.total', 1);
        $response->assertJsonPath('data.words.data.0.english_word', 'Algorithm');
    }

    public function test_can_query_words_with_first_and_page(): void
    {
        Word::factory()->count(5)->create();

        $response = $this->graphQL(/** @lang GraphQL */ '
            {
                words(first: 2, page: 1) {
                    data {
                        id
                    }
                    paginatorInfo {
                        total
                        perPage
                        currentPage
                        hasMorePages
                    }
                }
            }
        ');

        $response->assertJsonPath('data.words.paginatorInfo.perPage', 2);
        $response->assertJsonPath('data.words.paginatorInfo.currentPage', 1);
        $response->assertJsonPath('data.words.paginatorInfo.hasMorePages', true);
        $this->assertCount(2, $response->json('data.words.data'));
    }

    public function test_words_first_zero_returns_empty_result(): void
    {
        // Lighthouse @paginate treats first:0 as a valid request returning 0 items.
        // The @rules(min:1) directive is not enforced by Lighthouse on paginator args.
        Word::factory()->count(3)->create();

        $response = $this->graphQL(/** @lang GraphQL */ '
            {
                words(first: 0) {
                    data { id }
                    paginatorInfo { total }
                }
            }
        ');

        $response->assertJsonPath('data.words.data', []);
        $response->assertJsonPath('data.words.paginatorInfo.total', 3);
    }

    public function test_words_exceeds_complexity_limit_with_large_first(): void
    {
        // first:101 × ~2 fields per item ≈ 203 complexity > max_query_complexity:200.
        // The complexity limit fires before @rules(max:100) is checked.
        $response = $this->graphQL(/** @lang GraphQL */ '
            {
                words(first: 101) {
                    data { id english_word }
                }
            }
        ');

        $response->assertJsonStructure(['errors']);
    }

    public function test_can_query_single_word_by_id(): void
    {
        $word = Word::factory()->create(['english_word' => 'Microservice']);

        $response = $this->graphQL(/** @lang GraphQL */ '
            query GetWord($id: ID!) {
                word(id: $id) {
                    id
                    english_word
                }
            }
        ', ['id' => $word->id]);

        $response->assertJsonPath('data.word.id', (string) $word->id);
        $response->assertJsonPath('data.word.english_word', 'Microservice');
    }

    public function test_word_returns_null_for_nonexistent_id(): void
    {
        $response = $this->graphQL(/** @lang GraphQL */ '
            {
                word(id: 99999) {
                    id
                    english_word
                }
            }
        ');

        $response->assertJsonPath('data.word', null);
    }

    public function test_word_includes_translations_relationship(): void
    {
        $word = Word::factory()->create(['english_word' => 'Cache']);
        Translation::factory()->for($word)->create(['language' => 'es', 'translation' => 'Caché']);
        Translation::factory()->for($word)->create(['language' => 'de', 'translation' => 'Zwischenspeicher']);

        $response = $this->graphQL(/** @lang GraphQL */ '
            query GetWord($id: ID!) {
                word(id: $id) {
                    id
                    english_word
                    translations {
                        language
                        translation
                    }
                }
            }
        ', ['id' => $word->id]);

        $response->assertJsonPath('data.word.english_word', 'Cache');
        $this->assertCount(2, $response->json('data.word.translations'));
        $response->assertJsonStructure([
            'data' => ['word' => ['translations' => [['language', 'translation']]]],
        ]);
    }

    public function test_query_exceeding_depth_limit_is_rejected(): void
    {
        // graphql-php counts depth by fields-with-children only.
        // word(0)→translations(1)→word(2)→translations(3)→word(4)→translations(5)→word(6) = depth 6 > max_depth:5
        $response = $this->graphQL(/** @lang GraphQL */ '
            {
                word(id: "1") {
                    translations {
                        word {
                            translations {
                                word {
                                    translations {
                                        word {
                                            english_word
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        ');

        $response->assertJsonStructure(['errors']);
    }

    public function test_word_query_result_is_served_from_cache_on_second_call(): void
    {
        $word = Word::factory()->create(['english_word' => 'Firewall']);

        $query = /** @lang GraphQL */ '
            query GetWord($id: ID!) {
                word(id: $id) { id english_word }
            }
        ';

        // First call — populates cache
        $this->graphQL($query, ['id' => $word->id])
            ->assertJsonPath('data.word.english_word', 'Firewall');

        // Second call — served from cache, zero DB queries issued
        DB::enableQueryLog();
        $this->graphQL($query, ['id' => $word->id])
            ->assertJsonPath('data.word.english_word', 'Firewall');

        $this->assertCount(0, DB::getQueryLog());
        DB::disableQueryLog();
    }

    public function test_words_list_query_result_is_served_from_cache_on_second_call(): void
    {
        Word::factory()->count(3)->create();

        $query = /** @lang GraphQL */ '
            {
                words(first: 10) {
                    data { id english_word }
                    paginatorInfo { total }
                }
            }
        ';

        // First call — populates cache
        $firstResponse = $this->graphQL($query);
        $firstResponse->assertJsonPath('data.words.paginatorInfo.total', 3);

        // Second call — served from cache, zero DB queries issued
        DB::enableQueryLog();
        $this->graphQL($query)
            ->assertJsonPath('data.words.paginatorInfo.total', 3);

        $this->assertCount(0, DB::getQueryLog());
        DB::disableQueryLog();
    }
}
