<?php

declare(strict_types=1);

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Manages Sanctum service tokens for machine-to-machine clients (e.g. MCP server).
 *
 * Responsibility: create and revoke long-lived API tokens scoped to read-only access.
 * Authentication: requires an active JWT session (jwt.verify middleware on the route).
 */
class ServiceTokenController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $token = Auth::guard('api')->user()->createToken(
            name: 'mcp-server',
            abilities: ['words:write', 'translations:write'],
        );

        return response()->json(['token' => $token->plainTextToken], 201);
    }

    public function destroy(Request $request, int $tokenId): JsonResponse
    {
        Auth::guard('api')->user()->tokens()->where('id', $tokenId)->delete();

        return response()->json(null, 204);
    }
}
