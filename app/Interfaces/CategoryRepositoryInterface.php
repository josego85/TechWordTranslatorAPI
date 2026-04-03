<?php

declare(strict_types=1);

namespace App\Interfaces;

use App\Models\Category;
use Illuminate\Support\Collection;

interface CategoryRepositoryInterface
{
    /**
     * Find categories by their slugs.
     *
     * @param  list<string>              $slugs
     * @return Collection<int, Category>
     */
    public function findBySlugs(array $slugs): Collection;
}
