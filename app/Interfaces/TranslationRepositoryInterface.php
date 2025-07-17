<?php

namespace App\Interfaces;

use App\Models\Translation;

interface TranslationRepositoryInterface
{
    public function getAll();
    public function get(int $id): ?Translation;
    public function create(array $data): Translation;
    public function update(Translation $translation, array $data): ?Translation;
    public function delete(Translation $translation): bool;
}