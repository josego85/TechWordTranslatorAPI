<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Interfaces\WordRepositoryInterface;
use App\Models\Word;
use App\Services\CacheService;
use Illuminate\Pagination\CursorPaginator;

class CacheableWordRepository implements WordRepositoryInterface
{
    public function __construct(
        private WordRepositoryInterface $repository,
        private CacheService $cache
    ) {}

    public function getAll(int $perPage, ?string $cursor): CursorPaginator
    {
        $key = $this->cache->generateWordsKey($perPage, $cursor);

        return $this->cache->remember(
            $key,
            fn () => $this->repository->getAll($perPage, $cursor)
        );
    }

    public function get(int $id): ?Word
    {
        $key = $this->cache->generateWordKey($id);

        return $this->cache->remember(
            $key,
            fn () => $this->repository->get($id)
        );
    }

    public function create(array $data): Word
    {
        $word = $this->repository->create($data);
        $this->cache->forget('words:*');

        return $word;
    }

    public function update(Word $word, string $englishWord): ?Word
    {
        $updated = $this->repository->update($word, $englishWord);
        if ($updated) {
            $this->cache->forget([
                $this->cache->generateWordKey($word->id),
                'words:*',
            ]);
        }

        return $updated;
    }

    public function delete(Word $word): bool
    {
        $deleted = $this->repository->delete($word);
        if ($deleted) {
            $this->cache->forget([
                $this->cache->generateWordKey($word->id),
                'words:*',
            ]);
        }

        return $deleted;
    }
}
