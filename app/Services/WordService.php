<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Models\Word;

class WordService
{
    public function getAllWordsWithTranslations()
    {
        return Word::with(['translations' => function ($query) {
            $query->select(['word_id', 'spanish_word', 'german_word']);
        }])->select(['id', 'english_word'])->get();
    }

    public function createWordWithTranslations($data)
    {
        DB::beginTransaction();

        try {
            $word = Word::create($data['english_word']);
            $word->translations()->create($data['translations']);

            DB::commit();

            return true;
        } catch (\Exception $e) {
            DB::rollBack();

            return false;
        }
    }

    public function showWordWithTranslations($id)
    {
        $word = Word::with(['translations' => function ($query) {
            $query->select(['word_id', 'spanish_word', 'german_word']);
        }])->select(['id', 'english_word'])->find($id);

        if ($word) {
            return $word;
        }
        return false;
    }

    public function destroyWordWithTranslations($id)
    {
        DB::beginTransaction();

        try {
            $word = Word::find($id);

            if (!$word) {
                return null;
            }

            $word->translations()->delete();
            $word->delete();

            DB::commit();

            return true;
        } catch (\Exception $e) {
            DB::rollBack();

            return false;
        }
    }

    public function updateWordWithTranslations($id, $englishWord, $translations)
    {
        try {
            DB::beginTransaction();

            $word = Word::with(['translations'])->find($id);

            if (!$word) {
                return null;
            }

            $word->updateAttributes(['english_word' => $englishWord]);
            $word->updateTranslations($word, $translations);

            $word->load('translations');
            $word->makeHidden(['created_at', 'updated_at']);
            $word->translations->makeHidden(['id', 'created_at', 'updated_at']);

            DB::commit();

            return $word;
        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([
                'message' => 'Error update word and translations',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
