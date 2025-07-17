<?php

namespace App\Interfaces;

use Illuminate\Pagination\CursorPaginator;
use App\Models\Translation;

interface TranslationRepositoryInterface
{
    public function getAll(int $perPage, ?string $cursor): CursorPaginator;
    public function get(int $id): ?Translation;
    public function create(array $data): Translation;
    public function update(Translation $translation, array $data): ?Translation;
    public function delete(Translation $translation): bool;
}