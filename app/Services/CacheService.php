<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class CacheService
{
    private const int CACHE_TTL = 1440; // 24 hours in minutes

    public function remember(string $key, \Closure $callback)
    {
        return Cache::remember($key, self::CACHE_TTL, $callback);
    }

    public function forget(string|array $keys): void
    {
        if (is_array($keys)) {
            foreach ($keys as $key) {
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

    public function generateWordsKey(int $perPage, int $page, ?string $search = null, ?string $category = null): string
    {
        $searchPart   = $search !== null ? ':search:' . md5($search) : '';
        $categoryPart = $category !== null ? ':category:' . $category : '';

        return "words:perPage:$perPage:page:$page$searchPart$categoryPart";
    }

    public function generateTranslationKey(int $id): string
    {
        return "translation:$id";
    }

    public function generateTranslationsKey(int $perPage, int $page): string
    {
        return "translations:perPage:$perPage:page:$page";
    }
}
