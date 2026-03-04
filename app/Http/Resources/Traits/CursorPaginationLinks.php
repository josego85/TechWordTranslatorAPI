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
        $nextCursor = $paginator->nextCursor()?->encode();
        $prevCursor = $paginator->previousCursor()?->encode();

        return [
            'next' => $nextCursor ? $request->fullUrlWithQuery(['cursor' => $nextCursor]) : null,
            'prev' => $prevCursor ? $request->fullUrlWithQuery(['cursor' => $prevCursor]) : null,
        ];
    }
}
