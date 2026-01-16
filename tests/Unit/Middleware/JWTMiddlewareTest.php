<?php

declare(strict_types=1);

namespace Tests\Unit\Middleware;

use App\Http\Middleware\JWTMiddleware;
use App\Models\User;
use Illuminate\Http\Request;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenExpiredException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenInvalidException;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Tests\TestCase;

class JWTMiddlewareTest extends TestCase
{
    protected JWTMiddleware $middleware;

    #[\Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->middleware = new JWTMiddleware;
    }

    public function test_handle_allows_request_with_valid_token(): void
    {
        $user = User::factory()->make(['id' => 1]);

        JWTAuth::shouldReceive('parseToken')
            ->once()
            ->andReturnSelf();

        JWTAuth::shouldReceive('authenticate')
            ->once()
            ->andReturn($user);

        $request = Request::create('/test', 'GET');

        $response = $this->middleware->handle($request, fn ($req) => response()->json(['success' => true]));

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(['success' => true], json_decode($response->getContent(), true));
    }

    public function test_handle_returns_401_when_user_not_found(): void
    {
        JWTAuth::shouldReceive('parseToken')
            ->once()
            ->andReturnSelf();

        JWTAuth::shouldReceive('authenticate')
            ->once()
            ->andReturn(null);

        $request = Request::create('/test', 'GET');

        $response = $this->middleware->handle($request, fn ($req) => response()->json(['success' => true]));

        $this->assertEquals(401, $response->getStatusCode());
        $this->assertEquals(['message' => 'User not found'], json_decode($response->getContent(), true));
    }

    public function test_handle_returns_401_when_token_expired(): void
    {
        JWTAuth::shouldReceive('parseToken')
            ->once()
            ->andThrow(new TokenExpiredException('Token has expired'));

        $request = Request::create('/test', 'GET');

        $response = $this->middleware->handle($request, fn ($req) => response()->json(['success' => true]));

        $this->assertEquals(401, $response->getStatusCode());
        $this->assertEquals(['message' => 'Token has expired'], json_decode($response->getContent(), true));
    }

    public function test_handle_returns_401_when_token_invalid(): void
    {
        JWTAuth::shouldReceive('parseToken')
            ->once()
            ->andThrow(new TokenInvalidException('Invalid token'));

        $request = Request::create('/test', 'GET');

        $response = $this->middleware->handle($request, fn ($req) => response()->json(['success' => true]));

        $this->assertEquals(401, $response->getStatusCode());
        $this->assertEquals(['message' => 'Invalid token'], json_decode($response->getContent(), true));
    }

    public function test_handle_returns_401_on_general_jwt_exception(): void
    {
        JWTAuth::shouldReceive('parseToken')
            ->once()
            ->andThrow(new JWTException('JWT Error'));

        $request = Request::create('/test', 'GET');

        $response = $this->middleware->handle($request, fn ($req) => response()->json(['success' => true]));

        $this->assertEquals(401, $response->getStatusCode());
        $this->assertEquals(['message' => 'Unauthorized'], json_decode($response->getContent(), true));
    }

    public function test_middleware_does_not_call_next_when_token_expired(): void
    {
        JWTAuth::shouldReceive('parseToken')
            ->once()
            ->andThrow(new TokenExpiredException('Token has expired'));

        $request     = Request::create('/test', 'GET');
        $nextCalled  = false;

        $response = $this->middleware->handle($request, function($req) use (&$nextCalled) {
            $nextCalled = true;

            return response()->json(['success' => true]);
        });

        $this->assertFalse($nextCalled, 'Next closure should not be called when token is expired');
        $this->assertEquals(401, $response->getStatusCode());
    }

    public function test_middleware_does_not_call_next_when_token_invalid(): void
    {
        JWTAuth::shouldReceive('parseToken')
            ->once()
            ->andThrow(new TokenInvalidException('Invalid token'));

        $request     = Request::create('/test', 'GET');
        $nextCalled  = false;

        $response = $this->middleware->handle($request, function($req) use (&$nextCalled) {
            $nextCalled = true;

            return response()->json(['success' => true]);
        });

        $this->assertFalse($nextCalled, 'Next closure should not be called when token is invalid');
        $this->assertEquals(401, $response->getStatusCode());
    }

    public function test_middleware_calls_next_when_authentication_succeeds(): void
    {
        $user = User::factory()->make(['id' => 1]);

        JWTAuth::shouldReceive('parseToken')
            ->once()
            ->andReturnSelf();

        JWTAuth::shouldReceive('authenticate')
            ->once()
            ->andReturn($user);

        $request     = Request::create('/test', 'GET');
        $nextCalled  = false;

        $response = $this->middleware->handle($request, function($req) use (&$nextCalled) {
            $nextCalled = true;

            return response()->json(['success' => true]);
        });

        $this->assertTrue($nextCalled, 'Next closure should be called when authentication succeeds');
        $this->assertEquals(200, $response->getStatusCode());
    }
}
