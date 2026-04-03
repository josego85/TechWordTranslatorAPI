<?php

declare(strict_types=1);

namespace Tests\Unit\Repositories;

use App\Models\Category;
use App\Repositories\CategoryRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private CategoryRepository $repository;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new CategoryRepository(new Category);
    }

    public function test_find_by_slugs_returns_matching_categories(): void
    {
        Category::create(['slug' => 'networking', 'name' => 'Networking']);
        Category::create(['slug' => 'security',   'name' => 'Security']);
        Category::create(['slug' => 'databases',  'name' => 'Databases']);

        $result = $this->repository->findBySlugs(['networking', 'security']);

        $this->assertCount(2, $result);
        $this->assertTrue($result->pluck('slug')->contains('networking'));
        $this->assertTrue($result->pluck('slug')->contains('security'));
    }

    public function test_find_by_slugs_ignores_unknown_slugs(): void
    {
        Category::create(['slug' => 'networking', 'name' => 'Networking']);

        $result = $this->repository->findBySlugs(['networking', 'does-not-exist']);

        $this->assertCount(1, $result);
        $this->assertSame('networking', $result->first()->slug);
    }

    public function test_find_by_slugs_returns_empty_collection_for_no_matches(): void
    {
        $result = $this->repository->findBySlugs(['does-not-exist']);

        $this->assertCount(0, $result);
    }
}
