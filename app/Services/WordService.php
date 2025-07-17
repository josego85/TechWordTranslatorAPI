<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\CursorPaginator;
use App\Exceptions\TranslationException;
use App\Exceptions\WordNotFoundException;
use App\Interfaces\WordRepositoryInterface;
use App\Models\Word;

class WordService
{
    public function __construct(private WordRepositoryInterface $repository)
    {}

     /**
     * @param  int  $perPage
     * @param  string|null  $cursor
     * @return CursorPaginator
     */
    public function getAll(int $perPage, ?string $cursor): CursorPaginator
    {
        return $this->repository->getAll($perPage, $cursor);
    }

    /**
     * Create a new english word.
     *
     * @param array $data
     * @return Word
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
     * @param int $id
     * @return Word|null
     * @throws WordNotFoundException
     */
    public function get(int $id): ?Word
    {
        $word = $this->repository->get($id);

        if (!$word) {
            throw new WordNotFoundException("Word with id $id not found");
        }
        return $word;
    }

    /**
     * Update a english word.
     *
     * @param int $id
     * @param string $englishWord
     * @return Word|null
     * 
     * @throws WordNotFoundException
     */
    public function update(int $id, string $englishWord): ?Word
    {
        $word = $this->repository->get($id);

        if (!$word) {
            throw new WordNotFoundException("Word with id $id not found");
        }

        try {
            return $this->repository->update($word, $englishWord);
        } catch (\Throwable $e) {
            throw new WordNotFoundException("Failed to update word", 0, $e);
        }
    }

    /**
     * Delete a english word
     *
     * @param int $id
     * @return void
     * @throws WordNotFoundException
     */
    public function delete(int $id): void
    {
        $word = $this->repository->get($id);

        if (!$word) {
            throw new WordNotFoundException("Word with id $id not found");
        }

        try {
            $this->repository->delete($word);
        } catch (\Exception $e) {
            throw new WordNotFoundException('Error deleting word', 0, $e);
        }
    }
}
