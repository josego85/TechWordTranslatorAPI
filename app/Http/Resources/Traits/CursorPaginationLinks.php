<?php

declare(strict_types=1);

namespace App\Http\Resources\Traits;

use Illuminate\Http\Request;
use Illuminate\Pagination\CursorPaginator;

/**
 * Build next/prev links for a CursorPaginator.
 */
trait CursorPaginationLinks
{
    /**
     * @return array{next: string|null, prev: string|null}
     */
    protected function buildCursorLinks(Request $request, CursorPaginator $paginator): array
    {
        $links = [];
        $map   = [
            'next' => 'nextCursor',
            'prev' => 'previousCursor',
        ];

        foreach ($map as $key => $method) {
            $cursor = $paginator->{$method}()?->encode();

            $links[$key] = $cursor
                ? $request->fullUrlWithQuery(['cursor' => $cursor])
                : null;
        }

        return $links;
    }
}
