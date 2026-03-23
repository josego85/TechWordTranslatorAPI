<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Interfaces\TranslationRepositoryInterface;
use App\Models\Translation;
use App\Services\CacheService;
use Illuminate\Pagination\LengthAwarePaginator;

class CacheableTranslationRepository implements TranslationRepositoryInterface
{
    public function __construct(
        private readonly TranslationRepositoryInterface $repository,
        private readonly CacheService $cache
    ) {}

    public function getAll(int $perPage, int $page): LengthAwarePaginator
    {
        $key = $this->cache->generateTranslationsKey($perPage, $page);

        return $this->cache->remember(
            $key,
            fn () => $this->repository->getAll($perPage, $page)
        );
    }

    public function get(int $id): ?Translation
    {
        $key = $this->cache->generateTranslationKey($id);

        return $this->cache->remember(
            $key,
            fn () => $this->repository->get($id)
        );
    }

    public function create(array $data): Translation
    {
        $translation = $this->repository->create($data);
        $this->cache->forget('translations:*');

        return $translation;
    }

    public function update(Translation $translation, array $data): ?Translation
    {
        $updated = $this->repository->update($translation, $data);
        if ($updated instanceof Translation) {
            $this->cache->forget([
                $this->cache->generateTranslationKey($translation->id),
                'translations:*',
            ]);
        }

        return $updated;
    }

    public function delete(Translation $translation): bool
    {
        $deleted = $this->repository->delete($translation);
        if ($deleted) {
            $this->cache->forget([
                $this->cache->generateTranslationKey($translation->id),
                'translations:*',
            ]);
        }

        return $deleted;
    }
}
