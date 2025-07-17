<?php

namespace App\Repositories;

use App\Interfaces\WordRepositoryInterface;
use Illuminate\Pagination\CursorPaginator;
use App\Models\Word;

class WordRepository implements WordRepositoryInterface
{
    public function __construct(protected Word $model){}

    public function getAllWordsWithTranslations(int $perPage, ?string $cursor): CursorPaginator
    {
        return $this->model
          ->with('translations:id,word_id,spanish_word,german_word')
          ->select(['id', 'english_word'])
          ->orderBy('id')
          ->cursorPaginate(perPage: $perPage, cursorName: 'cursor', cursor: $cursor);
    }

    public function get(int $id): ?Word
    {
        return $this->model
          ->select(['id', 'english_word'])
          ->where('id', $id)
          ->first();
    }

    public function create(array $data): Word
    {
       return $this->model->create($data);
    }

    public function update(Word $word, string $englishWord): ?Word
    {
        $word->updateAttributes(['english_word' => $englishWord]);

        return $word;
    }

    public function delete(Word $word): bool
    {
        $word->translations()->delete();
        return $word->delete();
    }
}