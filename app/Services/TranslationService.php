<?php

namespace App\Services;

use App\Exceptions\TranslationException;
use App\Interfaces\TranslationRepositoryInterface;
use App\Models\Translation;
use App\Repositories\TranslationRepository;
use Symfony\Component\Translation\Reader\TranslationReader;

class TranslationService
{
    public function __construct(private TranslationRepositoryInterface $repository){}

    public function getAll()
    {
        return $this->repository->getAll();
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
     * @return Translation|null
     * 
     * @throws WordNotFoundException
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

    public function delete(int $id): bool
    {
        return $this->repository->delete($id);
    }
}