<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\WordNotFoundException;
use App\Interfaces\WordRepositoryInterface;
use App\Models\Word;
use Illuminate\Pagination\LengthAwarePaginator;

class WordService
{
    public function __construct(private readonly WordRepositoryInterface $repository) {}

    public function getAll(int $perPage, int $page, ?string $search = null): LengthAwarePaginator
    {
        return $this->repository->getAll($perPage, $page, $search);
    }

    /**
     * Create a new English word.
     *
     * @throws WordNotFoundException
     */
    public function create(array $data): Word
    {
        try {
            $payload = [
                'english_word' => $data['english_word'],
            ];

            return $this->repository->create($payload);
        } catch (\Exception $e) {
            throw new WordNotFoundException('Error creating word and translations', 0, $e);
        }
    }

    /**
     * Show a word.
     *
     * @throws WordNotFoundException
     */
    public function get(int $id): ?Word
    {
        $word = $this->repository->get($id);

        if (! $word instanceof \App\Models\Word) {
            throw new WordNotFoundException("Word with id $id not found");
        }

        return $word;
    }

    /**
     * Update an English word.
     *
     * @throws WordNotFoundException
     */
    public function update(int $id, string $englishWord): ?Word
    {
        $word = $this->repository->get($id);

        if (! $word instanceof \App\Models\Word) {
            throw new WordNotFoundException("Word with id $id not found");
        }

        try {
            return $this->repository->update($word, $englishWord);
        } catch (\Throwable $e) {
            throw new WordNotFoundException('Failed to update word', 0, $e);
        }
    }

    /**
     * Delete an English word.
     *
     * @throws WordNotFoundException
     */
    public function delete(int $id): void
    {
        $word = $this->repository->get($id);

        if (! $word instanceof \App\Models\Word) {
            throw new WordNotFoundException("Word with id $id not found");
        }

        try {
            $this->repository->delete($word);
        } catch (\Exception $e) {
            throw new WordNotFoundException('Error deleting word', 0, $e);
        }
    }
}
