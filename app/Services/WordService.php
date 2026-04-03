<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\WordNotFoundException;
use App\Interfaces\CategoryRepositoryInterface;
use App\Interfaces\WordRepositoryInterface;
use App\Models\Word;
use Illuminate\Pagination\LengthAwarePaginator;

class WordService
{
    public function __construct(
        private readonly WordRepositoryInterface $repository,
        private readonly ClassificationService $classifier,
        private readonly CategoryRepositoryInterface $categoryRepository,
    ) {}

    public function getAll(int $perPage, int $page, ?string $search = null, ?string $category = null): LengthAwarePaginator
    {
        return $this->repository->getAll($perPage, $page, $search, $category);
    }

    /**
     * Create a new English word.
     *
     * @throws WordNotFoundException
     */
    public function create(array $data): Word
    {
        try {
            $word = $this->repository->create(['english_word' => $data['english_word']]);
        } catch (\Exception $e) {
            throw new WordNotFoundException('Error creating word and translations', 0, $e);
        }

        $this->syncCategories($word, $data['categories'] ?? null);

        return $word->load('categories');
    }

    /**
     * Show a word.
     *
     * @throws WordNotFoundException
     */
    public function get(int $id): ?Word
    {
        $word = $this->repository->get($id);

        if (! $word instanceof Word) {
            throw new WordNotFoundException("Word with id $id not found");
        }

        return $word;
    }

    /**
     * Update an English word.
     *
     * @param list<string>|null $categories Manual override; null = re-classify if word changed
     *
     * @throws WordNotFoundException
     */
    public function update(int $id, string $englishWord, ?array $categories = null): ?Word
    {
        $word = $this->repository->get($id);

        if (! $word instanceof Word) {
            throw new WordNotFoundException("Word with id $id not found");
        }

        $wordChanged = $word->english_word !== $englishWord;

        try {
            $updated = $this->repository->update($word, $englishWord);
        } catch (\Throwable $e) {
            throw new WordNotFoundException('Failed to update word', 0, $e);
        }

        if ($updated instanceof Word && ($wordChanged || $categories !== null)) {
            $this->syncCategories($updated, $categories);
        }

        return $updated?->load('categories');
    }

    /**
     * Classify and attach categories to a word.
     *
     * @param list<string>|null $overrideSlugs When provided, skips the LLM call
     */
    private function syncCategories(Word $word, ?array $overrideSlugs): void
    {
        $slugs = $overrideSlugs ?? $this->classifier->classify($word->english_word);

        if ($slugs === []) {
            return;
        }

        $categoryIds = $this->categoryRepository->findBySlugs($slugs)->pluck('id')->all();
        $word->categories()->sync($categoryIds);
    }

    /**
     * Delete an English word.
     *
     * @throws WordNotFoundException
     */
    public function delete(int $id): void
    {
        $word = $this->repository->get($id);

        if (! $word instanceof Word) {
            throw new WordNotFoundException("Word with id $id not found");
        }

        try {
            $this->repository->delete($word);
        } catch (\Exception $e) {
            throw new WordNotFoundException('Error deleting word', 0, $e);
        }
    }
}
