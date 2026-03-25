<?php

declare(strict_types=1);

namespace App\GraphQL\Mutations;

use App\Models\Word;
use App\Services\WordService;

class WordMutations
{
    public function __construct(private readonly WordService $service) {}

    public function create(mixed $root, array $args): Word
    {
        return $this->service->create($args);
    }

    public function update(mixed $root, array $args): Word
    {
        return $this->service->update(
            (int) $args['id'],
            $args['english_word'],
        );
    }

    public function delete(mixed $root, array $args): Word
    {
        $word = $this->service->get((int) $args['id']);

        $this->service->delete((int) $args['id']);

        return $word;
    }
}
