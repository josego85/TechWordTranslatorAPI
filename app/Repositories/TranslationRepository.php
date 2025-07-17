<?php

namespace App\Repositories;

use App\Interfaces\TranslationRepositoryInterface;
use App\Models\Translation;

class TranslationRepository implements TranslationRepositoryInterface
{
    public function __construct(private Translation $model) {}

    public function getAll() {
        return $this->model->get();
    }

    public function get(int $id): ?Translation
    {
        return $this->model
          ->select(['id', 'word_id', 'spanish_word', 'german_word'])
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

    public function delete($id): bool {
        $translation = $this->get($id);

        return $translation->delete();
    }
}