<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class CacheService
{
    private const CACHE_TTL = 60; // minutes

    public function remember(string $key, callable $callback)
    {
        return Cache::remember($key, self::CACHE_TTL, $callback);
    }

    public function forget(string|array $keys): void
    {
        if (is_array($keys)) {
            foreach($keys as $key) {
                Cache::forget($key);
            }
            return;
        }
        Cache::forget($keys);
    }

    public function generateWordKey(int $id): string
    {
        return "word:$id";
    }

    public function generateWordsKey(int $perPage, ?string $cursor): string
    {
        return "words:perPage:$perPage:cursor:" . ($cursor ?? 'null');
    }
}