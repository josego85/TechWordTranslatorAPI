<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * ResourceCollection for Word models.
 */
class WordCollection extends ResourceCollection
{
    /** @var class-string<JsonResource> */
    public $collects = WordResource::class;

    /**
     * Transform the resource collection into an array.
     *
     * @param  Request              $request
     * @return array{data: mixed[]}
     */
    #[\Override]
    public function toArray($request): array
    {
        return [
            'data' => $this->collection->all(),
        ];
    }
}
