<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\TranslationException;
use App\Interfaces\TranslationRepositoryInterface;
use App\Models\Translation;
use Illuminate\Pagination\LengthAwarePaginator;

class TranslationService
{
    public function __construct(private readonly TranslationRepositoryInterface $repository) {}

    public function getAll(int $perPage, int $page): LengthAwarePaginator
    {
        return $this->repository->getAll($perPage, $page);
    }

    public function get(int $id): ?Translation
    {
        $translation = $this->repository->get($id);

        if (! $translation instanceof \App\Models\Translation) {
            throw new TranslationException("Translation with id $id not found");
        }

        return $translation;
    }

    public function create(array $data): Translation
    {
        try {
            $payload = [
                'word_id' => $data['word_id'],
                'language' => $data['language'],
                'translation' => $data['translation'],
            ];

            return $this->repository->create($payload);
        } catch (\Exception $e) {
            throw new TranslationException('Error creating translation', 0, $e);
        }
    }

    /**
     * Update a translation.
     *
     *
     * @throws TranslationException
     */
    public function update(int $id, array $data): Translation
    {
        $translation = $this->repository->get($id);

        if (! $translation instanceof \App\Models\Translation) {
            throw new TranslationException("Translation with id $id not found");
        }

        try {
            $payload = [
                'word_id' => $data['word_id'] ?? $translation->word_id,
                'language' => $data['language'] ?? $translation->language,
                'translation' => $data['translation'] ?? $translation->translation,
            ];

            return $this->repository->update($translation, $payload);
        } catch (\Exception $e) {
            throw new TranslationException('Failed to update translation', 0, $e);
        }
    }

    /**
     * Delete a translation.
     *
     * @throws TranslationException
     */
    public function delete(int $id): void
    {
        $translation = $this->repository->get($id);

        if (! $translation instanceof \App\Models\Translation) {
            throw new TranslationException("Translation with id $id not found");
        }

        try {
            $this->repository->delete($translation);
        } catch (\Exception $e) {
            throw new TranslationException('Error deleting translation', 0, $e);
        }
    }
}
