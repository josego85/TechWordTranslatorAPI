<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Models\Word;
use App\Exceptions\TranslationException;
use App\Exceptions\WordNotFoundException;

class WordService
{
     /**
     * Get all words with their translations.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllWordsWithTranslations(): \Illuminate\Database\Eloquent\Collection
    {
        return Word::with(['translations' => function ($query) {
            $query->select(['word_id', 'spanish_word', 'german_word']);
        }])->select(['id', 'english_word'])->get();
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
            $word = Word::create([
                'english_word' => $data['english_word']
            ]);
            $word->translations()->create([
                'spanish_word' => $data['translations']['spanish_word'],
                'german_word' => $data['translations']['german_word']
            ]);

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
        $word = Word::with(['translations' => function ($query) {
            $query->select(['word_id', 'spanish_word', 'german_word']);
        }])->select(['id', 'english_word'])->find($id);

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

            $word = Word::with(['translations'])->find($id);

            if (!$word) {
                return null;
            }

            $word->updateAttributes(['english_word' => $englishWord]);
            $word->updateTranslations($translations);

            $word->load('translations');
            $word->makeHidden(['created_at', 'updated_at']);
            $word->translations->makeHidden(['id', 'created_at', 'updated_at']);

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
     * @throws WordNotFoundException
     */
    public function destroyWordWithTranslations(int $id): bool
    {
        DB::beginTransaction();

        try {
            $word = Word::find($id);

            if (!$word) {
                throw new WordNotFoundException("Word with id $id not found");
            }

            $word->translations()->delete();
            $word->delete();

            DB::commit();

            return true;
        } catch (\Exception $e) {
            DB::rollBack();

            throw new TranslationException('Error deleting word and translations', 0, $e);
        }
    }
}
