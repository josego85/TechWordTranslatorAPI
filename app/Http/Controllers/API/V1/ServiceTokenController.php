<?php

declare(strict_types=1);

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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
        /** @var User $user */
        $user  = Auth::guard('api')->user();
        $token = $user->createToken(
            name: 'mcp-server',
            abilities: ['words:write', 'translations:write'],
        );

        Log::info('Service token created', ['user_id' => $user->id, 'ip' => $request->ip()]);

        return response()->json(['token' => $token->plainTextToken], 201);
    }

    public function destroy(Request $request, int $tokenId): JsonResponse
    {
        /** @var User $user */
        $user = Auth::guard('api')->user();
        $user->tokens()->where('id', $tokenId)->delete();

        Log::warning('Service token revoked', ['user_id' => $user->id, 'token_id' => $tokenId, 'ip' => $request->ip()]);

        return response()->json(null, 204);
    }
}
