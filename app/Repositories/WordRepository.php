<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Interfaces\WordRepositoryInterface;
use App\Models\Word;
use Illuminate\Pagination\LengthAwarePaginator;

class WordRepository implements WordRepositoryInterface
{
    public function __construct(protected Word $model) {}

    public function getAll(int $perPage, int $page, ?string $search = null): LengthAwarePaginator
    {
        $query = $this->model
            ->select(['id', 'english_word', 'created_at', 'updated_at'])
            ->with('translations');

        // Apply search filter if provided
        if ($search !== null && $search !== '') {
            $query->where(function ($q) use ($search) {
                // Search in English word
                $q->where('english_word', 'LIKE', "%{$search}%")
                  // OR search in any translation
                    ->orWhereHas('translations', function ($translationQuery) use ($search) {
                        $translationQuery->where('translation', 'LIKE', "%{$search}%");
                    });
            });
        }

        return $query->orderBy('id')->paginate(perPage: $perPage, page: $page);
    }

    public function get(int $id): ?Word
    {
        return $this->model
            ->select(['id', 'english_word', 'created_at', 'updated_at'])
            ->with('translations')
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
