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
    public function getAllWordsWithTranslations(int $perPage, ?string $cursor): CursorPaginator
    {
        return $this->repository->getAllWordsWithTranslations($perPage, $cursor);
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
     * Delete a word with its translations.
     *
     * @param int $id
     * @return bool
     * @throws WordNotFoundException|TranslationException
     */
    public function destroyWordWithTranslations(int $id): bool
    {
        $word = $this->repository->get($id);

        if (!$word) {
            throw new WordNotFoundException("Word with id $id not found");
        }

        DB::beginTransaction();
        try {
            $this->repository->delete($word);
            DB::commit();

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new TranslationException('Error deleting word and translations', 0, $e);
        }
    }
}
