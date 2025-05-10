<?php

namespace App\Http\Resources;

use App\Http\Resources\Traits\CursorPaginationLinks;
use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Http\Resources\WordResource;

/**
 * ResourceCollection for Word models, includes cursor pagination links.
 */
class WordCollection extends ResourceCollection
{
    use CursorPaginationLinks;

    /** @var class-string<\Illuminate\Http\Resources\Json\JsonResource> */
    public $collects = WordResource::class;

    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array{data: mixed[]}
     */
    public function toArray($request): array
    {
        return [
            'data' => $this->collection,
        ];
    }

    /**
     * Add pagination links to the response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array{links: array{next: string|null, prev: string|null}}
     */
    public function with($request): array
    {
        return [
            'links' => $this->buildCursorLinks($request, $this->resource),
        ];
    }
}