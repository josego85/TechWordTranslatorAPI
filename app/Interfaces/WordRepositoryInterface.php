<?php

namespace App\Interfaces;

use Illuminate\Database\Eloquent\Collection;
use App\Models\Word;

interface WordRepositoryInterface
{
    public function getAllWordsWithTranslations(): Collection;
    public function findWithTranslations(int $id): ?Word;
    public function create(array $data): Word;
    public function update(Word $word, string $englishWord, array $translations): ?Word;
    public function delete(Word $word): bool;
}