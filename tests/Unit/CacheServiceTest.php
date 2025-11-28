<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Services\CacheService;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class CacheServiceTest extends TestCase
{
    private CacheService $service;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new CacheService;
    }

    public function test_remember_stores_and_retrieves_value(): void
    {
        Cache::shouldReceive('remember')
            ->once()
            ->with('test_key', 1440, \Mockery::type('callable'))
            ->andReturnUsing(function ($key, $ttl, $callback) {
                return $callback();
            });

        $result = $this->service->remember('test_key', fn () => 'cached_value');

        $this->assertEquals('cached_value', $result);
    }

    public function test_forget_removes_single_key(): void
    {
        Cache::shouldReceive('forget')
            ->once()
            ->with('test_key');

        $this->service->forget('test_key');

        $this->assertTrue(true); // Assertion to confirm no exception was thrown
    }

    public function test_forget_removes_multiple_keys(): void
    {
        $keys = ['key1', 'key2', 'key3'];

        Cache::shouldReceive('forget')
            ->times(count($keys))
            ->withArgs(function ($key) use ($keys) {
                return in_array($key, $keys, true);
            });

        $this->service->forget($keys);

        $this->assertTrue(true); // Assertion to confirm no exception was thrown
    }

    public function test_generate_word_key_returns_correct_format(): void
    {
        $wordId = 123;
        $expectedKey = 'word:123';

        $result = $this->service->generateWordKey($wordId);

        $this->assertEquals($expectedKey, $result);
    }

    public function test_generate_words_key_returns_correct_format_without_search(): void
    {
        $perPage = 15;
        $page = 2;
        $expectedKey = 'words:perPage:15:page:2';

        $result = $this->service->generateWordsKey($perPage, $page);

        $this->assertEquals($expectedKey, $result);
    }

    public function test_generate_words_key_returns_correct_format_with_search(): void
    {
        $perPage = 10;
        $page = 1;
        $search = 'test query';
        $searchHash = md5($search);
        $expectedKey = "words:perPage:10:page:1:search:$searchHash";

        $result = $this->service->generateWordsKey($perPage, $page, $search);

        $this->assertEquals($expectedKey, $result);
    }

    public function test_generate_words_key_handles_different_search_terms(): void
    {
        $perPage = 20;
        $page = 3;
        $search1 = 'search one';
        $search2 = 'search two';

        $result1 = $this->service->generateWordsKey($perPage, $page, $search1);
        $result2 = $this->service->generateWordsKey($perPage, $page, $search2);

        $this->assertNotEquals($result1, $result2);
        $this->assertStringContainsString(':search:', $result1);
        $this->assertStringContainsString(':search:', $result2);
    }
}
