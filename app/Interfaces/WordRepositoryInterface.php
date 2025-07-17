<?php

declare(strict_types=1);

namespace App\Interfaces;

use App\Models\Word;
use Illuminate\Pagination\CursorPaginator;

interface WordRepositoryInterface
{
    public function getAll(int $perPage, ?string $cursor): CursorPaginator;

    public function get(int $id): ?Word;

    public function create(array $data): Word;

    public function update(Word $word, string $englishWord): ?Word;

    public function delete(Word $word): bool;
}
