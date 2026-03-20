<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Http\Resources\Traits\CursorPaginationLinks;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Pagination\CursorPaginator;

/**
 * ResourceCollection for Translation models, includes cursor pagination links.
 */
class TranslationCollection extends ResourceCollection
{
    use CursorPaginationLinks;

    /** @var class-string<JsonResource> */
    public $collects = TranslationResource::class;

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

    /**
     * Add pagination links to the response.
     *
     * @param  Request                                                           $request
     * @return array{links: array{next: string|null, prev: string|null}}|array{}
     */
    #[\Override]
    public function with($request): array
    {
        // Only add cursor links if using CursorPaginator
        if ($this->resource instanceof CursorPaginator) {
            return [
                'links' => $this->buildCursorLinks($request, $this->resource),
            ];
        }

        return [];
    }
}
