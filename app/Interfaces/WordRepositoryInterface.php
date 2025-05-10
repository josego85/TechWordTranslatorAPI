<?php

namespace App\Interfaces;

use Illuminate\Pagination\CursorPaginator;
use App\Models\Word;

interface WordRepositoryInterface
{
    public function getAllWordsWithTranslations(int $perPage, ?string $cursor): CursorPaginator;
    public function findWithTranslations(int $id): ?Word;
    public function create(array $data): Word;
    public function update(Word $word, string $englishWord, array $translations): ?Word;
    public function delete(Word $word): bool;
}