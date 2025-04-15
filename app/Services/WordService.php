<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Exceptions\TranslationException;
use App\Exceptions\WordNotFoundException;
use App\Interfaces\WordRepositoryInterface;
use App\Models\Word;

class WordService
{
    public function __construct(private WordRepositoryInterface $wordRepository)
    {}

     /**
     * Get all words with their translations.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllWordsWithTranslations(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->wordRepository->getAllWordsWithTranslations();
    }

    /**
     * Create a new word with translations.
     *
     * @param array $data
     * @return bool
     * @throws TranslationException
     */
    public function createWordWithTranslations(array $data): bool
    {
        DB::beginTransaction();

        try {
            $this->wordRepository->create($data);
            DB::commit();

            return true;
        } catch (\Exception $e) {
            DB::rollBack();

            throw new TranslationException('Error creating word and translations', 0, $e);
        }
    }

    /**
     * Show a word with its translations.
     *
     * @param int $id
     * @return Word|null
     * @throws WordNotFoundException
     */
    public function showWordWithTranslations(int $id): ?Word
    {
        $word = $this->wordRepository->findWithTranslations($id);

        if (!$word) {
            throw new WordNotFoundException("Word with id $id not found");
        }
        return $word;
    }

    /**
     * Update a word with its translations.
     *
     * @param int $id
     * @param string $englishWord
     * @param array $translations
     * @return Word|null
     * @throws TranslationException
     */
    public function updateWordWithTranslations(int $id, string $englishWord, array $translations): ?Word
    {
        try {
            DB::beginTransaction();

            $word = $this->wordRepository->findWithTranslations($id);

            if (!$word) {
                return null;
            }

            $this->wordRepository->update($word, $englishWord, $translations);

            DB::commit();

            return $word;
        } catch (\Exception $e) {
            DB::rollback();
            throw new TranslationException('Error updating word and translations', 0, $e);
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
        $word = $this->wordRepository->findWithTranslations($id);

        if (!$word) {
            throw new WordNotFoundException("Word with id $id not found");
        }

        DB::beginTransaction();
        try {
            $this->wordRepository->delete($word);
            DB::commit();

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new TranslationException('Error deleting word and translations', 0, $e);
        }
    }
}
