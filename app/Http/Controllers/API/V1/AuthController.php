<?php

declare(strict_types=1);

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\V1\LoginRequest;
use App\Http\Requests\API\V1\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function sendResponse($data, $message, $status = 200)
    {
        $response = [
            'data' => $data,
            'message' => $message,
        ];

        return response()->json($response, $status);
    }

    public function sendError($errorData, $message, $status = 500)
    {
        $response            = [];
        $response['message'] = $message;
        if (! empty($errorData)) {
            $response['data'] = $errorData;
        }

        return response()->json($response, $status);
    }

    public function register(RegisterRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        Log::info('User registered successfully', [
            'email' => $user->email,
            'ip' => $request->ip(),
        ]);

        return $this->sendResponse(
            ['user' => $user],
            'User registered successfully',
            201
        );
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        try {
            if (! $token = JWTAuth::attempt($credentials)) {
                Log::warning('Failed login attempt', [
                    'email' => $request->email,
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);

                return $this->sendError([], 'Invalid credentials', 401);
            }
        } catch (JWTException $e) {
            Log::error('JWT Error during login', [
                'email' => $request->email ?? 'unknown',
                'ip' => $request->ip(),
                'error' => $e->getMessage(),
            ]);

            return $this->sendError([], 'Authentication failed', 401);
        }

        Log::info('Successful login', [
            'email' => $request->email,
            'ip' => $request->ip(),
        ]);

        return $this->sendResponse(
            ['token' => $token],
            'Successful login',
            200
        );
    }

    public function getUser()
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            if (! $user) {
                return $this->sendError([], 'User not found', 401);
            }
        } catch (JWTException $e) {
            // Log internal error but return generic message
            Log::warning('JWT Authentication failed', [
                'error' => $e->getMessage(),
                'ip' => request()->ip(),
            ]);

            return $this->sendError([], 'Invalid or expired token', 401);
        }

        return $this->sendResponse($user, 'User data retrieved', 200);
    }

    public function logout(Request $request)
    {
        try {
            JWTAuth::parseToken()->invalidate();

            Log::info('User logged out', [
                'ip' => $request->ip(),
            ]);

            return $this->sendResponse([], 'Successfully logged out', 200);
        } catch (JWTException $e) {
            Log::warning('Logout failed', [
                'error' => $e->getMessage(),
                'ip' => $request->ip(),
            ]);

            return $this->sendError([], 'Logout failed', 500);
        }
    }
}
