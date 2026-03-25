<?php

declare(strict_types=1);

namespace App\GraphQL\Mutations;

use App\Models\Translation;
use App\Services\TranslationService;

class TranslationMutations
{
    public function __construct(private readonly TranslationService $service) {}

    public function create(mixed $root, array $args): Translation
    {
        return $this->service->create($args);
    }

    public function update(mixed $root, array $args): Translation
    {
        $id   = (int) $args['id'];
        $data = array_filter(
            $args,
            static fn (string $key): bool => $key !== 'id',
            ARRAY_FILTER_USE_KEY,
        );

        return $this->service->update($id, $data);
    }

    public function delete(mixed $root, array $args): Translation
    {
        $translation = $this->service->get((int) $args['id']);

        $this->service->delete((int) $args['id']);

        return $translation;
    }
}
