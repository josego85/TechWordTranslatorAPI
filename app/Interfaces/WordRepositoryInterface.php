<?php

namespace App\Interfaces;

use Illuminate\Pagination\CursorPaginator;
use App\Models\Word;

interface WordRepositoryInterface
{
    public function getAll(int $perPage, ?string $cursor): CursorPaginator;
    public function get(int $id): ?Word;
    public function create(array $data): Word;
    public function update(Word $word, string $englishWord): ?Word;
    public function delete(Word $word): bool;
}