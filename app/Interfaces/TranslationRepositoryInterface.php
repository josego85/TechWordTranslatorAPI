<?php

declare(strict_types=1);

namespace App\Interfaces;

use App\Models\Translation;
use Illuminate\Pagination\CursorPaginator;

interface TranslationRepositoryInterface
{
    public function getAll(int $perPage, ?string $cursor): CursorPaginator;

    public function get(int $id): ?Translation;

    public function create(array $data): Translation;

    public function update(Translation $translation, array $data): ?Translation;

    public function delete(Translation $translation): bool;
}
