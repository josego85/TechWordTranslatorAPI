<?php

namespace App\Services;

use Illuminate\Pagination\CursorPaginator;
use App\Exceptions\TranslationException;
use App\Interfaces\TranslationRepositoryInterface;
use App\Models\Translation;

class TranslationService
{
    public function __construct(private TranslationRepositoryInterface $repository){}

    /**
     * @param  int  $perPage
     * @param  string|null  $cursor
     * @return CursorPaginator
     */
    public function getAll(int $perPage, ?string $cursor): CursorPaginator
    {
        return $this->repository->getAll($perPage, $cursor);
    }

    public function get(int $id): ?Translation
    {
        $translation = $this->repository->get($id);

        if(!$translation) {
            throw new TranslationException("Translation with id $id not found");
        }
        return $translation;
    }

    public function create(array $data): Translation
    {
        try {
            $payload = [
                'word_id'      => $data['word_id'],
                'spanish_word' => $data['spanish_word'] ?? null,
                'german_word'  => $data['german_word'] ?? null,
            ];
            return $this->repository->create($payload);
        } catch (\Exception $e) {
            throw new TranslationException('Error translations words', 0, $e);
        }
    }

    /**
     * Update a translations words.
     *
     * @param int $id
     * @param array $data
     * @return Translation
     * 
     * @throws TranslationException
     */
    public function update(int $id, array $data): Translation
    {
        $translation = $this->repository->get($id);

        if(!$translation) {
            throw new TranslationException("Translation with id $id not found");
        }

        try {
            $payload = [
                'word_id'      => $data['word_id'],
                'spanish_word' => $data['spanish_word'] ?? null,
                'german_word'  => $data['german_word'] ?? null,
            ];
            return $this->repository->update($translation, $payload);
        } catch(\Exception $e) {
            throw new TranslationException("Failed to update translation", 0, $e);
        }
    }

    /**
     * Delete a english word
     *
     * @param int $id
     * @return void
     * @throws TranslationException
     */
    public function delete(int $id): void
    {
        $translation = $this->repository->get($id);

        if (!$translation) {
            throw new TranslationException("Translation with id $id not found");
        }

        try {
            $this->repository->delete($translation);
        } catch (\Exception $e) {
            throw new TranslationException('Error deleting translation', 0, $e);
        }
    }
}