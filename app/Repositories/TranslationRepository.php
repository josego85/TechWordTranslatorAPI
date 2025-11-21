<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Interfaces\TranslationRepositoryInterface;
use App\Models\Translation;
use Illuminate\Pagination\LengthAwarePaginator;

class TranslationRepository implements TranslationRepositoryInterface
{
    public function __construct(private readonly Translation $model) {}

    public function getAll(int $perPage, int $page): LengthAwarePaginator
    {
        return $this->model
            ->select(['id', 'word_id', 'language', 'translation', 'created_at', 'updated_at'])
            ->orderBy('id')
            ->paginate(perPage: $perPage, page: $page);
    }

    public function get(int $id): ?Translation
    {
        return $this->model
            ->select(['id', 'word_id', 'language', 'translation', 'created_at', 'updated_at'])
            ->where('id', $id)
            ->first();
    }

    public function create(array $data): Translation
    {
        return $this->model->create($data);
    }

    public function update(Translation $translation, array $data): ?Translation
    {
        $translation->update($data);

        return $translation;
    }

    public function delete(Translation $translation): bool
    {
        return $translation->delete();
    }
}
