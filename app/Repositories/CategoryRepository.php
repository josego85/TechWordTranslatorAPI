<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Interfaces\CategoryRepositoryInterface;
use App\Models\Category;
use Illuminate\Support\Collection;

class CategoryRepository implements CategoryRepositoryInterface
{
    public function __construct(protected Category $model) {}

    /**
     * @param  list<string>              $slugs
     * @return Collection<int, Category>
     */
    public function findBySlugs(array $slugs): Collection
    {
        return $this->model->whereIn('slug', $slugs)->get();
    }
}
