<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Http\Resources\Traits\CursorPaginationLinks;
use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * ResourceCollection for Translation models, includes cursor pagination links.
 */
class TranslationCollection extends ResourceCollection
{
    use CursorPaginationLinks;

    /** @var class-string<\Illuminate\Http\Resources\Json\JsonResource> */
    public $collects = TranslationResource::class;

    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array{data: mixed[]}
     */
    #[\Override]
    public function toArray($request): array
    {
        return [
            'data' => $this->collection->all(),
        ];
    }

    /**
     * Add pagination links to the response.
     *
     * @param  \Illuminate\Http\Request                                  $request
     * @return array{links: array{next: string|null, prev: string|null}}
     */
    #[\Override]
    public function with($request): array
    {
        // Only add cursor links if using CursorPaginator
        if ($this->resource instanceof \Illuminate\Pagination\CursorPaginator) {
            return [
                'links' => $this->buildCursorLinks($request, $this->resource),
            ];
        }

        return [];
    }
}
