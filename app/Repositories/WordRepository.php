<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Interfaces\WordRepositoryInterface;
use App\Models\Word;
use Illuminate\Pagination\LengthAwarePaginator;

class WordRepository implements WordRepositoryInterface
{
    public function __construct(protected Word $model) {}

    public function getAll(int $perPage, int $page, ?string $search = null, ?string $category = null, ?string $sort = null): LengthAwarePaginator
    {
        $query = $this->model
            ->select(['id', 'english_word', 'created_at', 'updated_at'])
            ->with(['translations', 'categories']);

        if ($search !== null && $search !== '') {
            $query->where(function($q) use ($search) {
                $q->where('english_word', 'LIKE', "%{$search}%")
                    ->orWhereHas('translations', function($translationQuery) use ($search) {
                        $translationQuery->where('translation', 'LIKE', "%{$search}%");
                    });
            });
        }

        if ($category !== null && $category !== '') {
            $query->whereHas('categories', fn ($q) => $q->where('slug', $category));
        }

        match ($sort) {
            'alpha-desc' => $query->orderBy('english_word', 'desc'),
            default => $query->orderBy('english_word', 'asc'),
        };

        return $query->paginate(perPage: $perPage, page: $page);
    }

    public function get(int $id): ?Word
    {
        return $this->model
            ->select(['id', 'english_word', 'created_at', 'updated_at'])
            ->with(['translations', 'categories'])
            ->where('id', $id)
            ->first();
    }

    public function create(array $data): Word
    {
        return $this->model->create($data);
    }

    public function update(Word $word, string $englishWord): ?Word
    {
        $word->update(['english_word' => $englishWord]);

        return $word->fresh();
    }

    public function delete(Word $word): bool
    {
        return $word->delete();
    }
}
