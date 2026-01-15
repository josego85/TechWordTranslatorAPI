<?php

declare(strict_types=1);

namespace Tests\Unit\Resources;

use App\Http\Resources\TranslationCollection;
use App\Models\Translation;
use App\Models\Word;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Tests\TestCase;

class TranslationCollectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_to_array_returns_data_key_with_collection(): void
    {
        $word = Word::factory()->create();

        // Create unique translations to avoid constraint violations
        $translation1 = Translation::factory()->for($word)->create(['language' => 'es']);
        $translation2 = Translation::factory()->for($word)->create(['language' => 'de']);
        $translation3 = Translation::factory()->for($word)->create(['language' => 'fr']);

        $paginator = new LengthAwarePaginator(
            collect([$translation1, $translation2, $translation3]),
            3,
            10,
            1
        );

        $collection = new TranslationCollection($paginator);
        $request    = Request::create('/test', 'GET');

        $array = $collection->toArray($request);

        $this->assertArrayHasKey('data', $array);
        $this->assertCount(3, $array['data']);
    }

    public function test_with_returns_cursor_links_when_using_cursor_paginator(): void
    {
        $word = Word::factory()->create();

        // Create unique translations to avoid constraint violations
        $translation1 = Translation::factory()->for($word)->create(['language' => 'es']);
        $translation2 = Translation::factory()->for($word)->create(['language' => 'de']);
        $translation3 = Translation::factory()->for($word)->create(['language' => 'fr']);

        $cursorPaginator = new CursorPaginator(
            collect([$translation1, $translation2, $translation3]),
            3,
            null,
            ['path' => 'http://localhost/api/v1/translations']
        );

        $collection = new TranslationCollection($cursorPaginator);
        $request    = Request::create('/api/v1/translations', 'GET');

        $with = $collection->with($request);

        $this->assertArrayHasKey('links', $with);
        $this->assertIsArray($with['links']);
    }

    public function test_with_returns_empty_array_when_not_using_cursor_paginator(): void
    {
        $word = Word::factory()->create();

        // Create unique translations to avoid constraint violations
        $translation1 = Translation::factory()->for($word)->create(['language' => 'es']);
        $translation2 = Translation::factory()->for($word)->create(['language' => 'de']);
        $translation3 = Translation::factory()->for($word)->create(['language' => 'fr']);

        $paginator = new LengthAwarePaginator(
            collect([$translation1, $translation2, $translation3]),
            3,
            10,
            1
        );

        $collection = new TranslationCollection($paginator);
        $request    = Request::create('/api/v1/translations', 'GET');

        $with = $collection->with($request);

        $this->assertEmpty($with);
        $this->assertIsArray($with);
    }

    public function test_collection_uses_translation_resource_class(): void
    {
        $word         = Word::factory()->create();
        $translations = Translation::factory()->for($word)->count(1)->create();

        $paginator = new LengthAwarePaginator(
            $translations,
            1,
            10,
            1
        );

        $collection = new TranslationCollection($paginator);

        $this->assertEquals(\App\Http\Resources\TranslationResource::class, $collection->collects);
    }

    public function test_to_array_handles_empty_collection(): void
    {
        $paginator = new LengthAwarePaginator(
            collect([]),
            0,
            10,
            1
        );

        $collection = new TranslationCollection($paginator);
        $request    = Request::create('/test', 'GET');

        $array = $collection->toArray($request);

        $this->assertArrayHasKey('data', $array);
        $this->assertEmpty($array['data']);
    }

    public function test_cursor_paginator_builds_correct_links_structure(): void
    {
        $word = Word::factory()->create();

        // Create unique translations to avoid constraint violations
        $translation1 = Translation::factory()->for($word)->create(['language' => 'es']);
        $translation2 = Translation::factory()->for($word)->create(['language' => 'de']);

        // Create cursor paginator with next/prev
        $cursorPaginator = new CursorPaginator(
            collect([$translation1, $translation2]),
            2,
            null,
            ['path' => 'http://localhost/api/v1/translations']
        );

        $collection = new TranslationCollection($cursorPaginator);
        $request    = Request::create('/api/v1/translations', 'GET');

        $response = $collection->toResponse($request);
        $data     = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('links', $data);
        $this->assertIsArray($data['links']);
    }
}
