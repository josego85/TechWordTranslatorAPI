<?php

declare(strict_types=1);

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
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

    public function register(Request $request)
    {
        $input = $request->only('name', 'email', 'password', 'c_password');

        $validator = Validator::make($input, [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
            'c_password' => 'required|same:password',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors(), 'Validation Error', 422);
        }

        $input['password'] = bcrypt($input['password']); // use bcrypt to hash the passwords
        $user              = User::create($input); // eloquent creation of data

        $success['user'] = $user;

        return $this->sendResponse($success, 'User registered successfully', 201);

    }

    public function login(Request $request)
    {
        $input = $request->only('email', 'password');

        $validator = Validator::make($input, [
            'email' => 'required',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors(), 'Validation Error', 422);
        }

        try {
            // this authenticates the user details with the database and generates a token
            if (! $token = JWTAuth::attempt($input)) {
                return $this->sendError([], 'Invalid login credentials', 400);
            }
        } catch (JWTException $e) {
            return $this->sendError([], $e->getMessage(), 401);
        }

        $success = [
            'token' => $token,
        ];

        return $this->sendResponse($success, 'Successful login', 200);
    }

    public function getUser()
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            if (! $user) {
                return $this->sendError([], 'User not found', 401);
            }
        } catch (JWTException $e) {
            return $this->sendError([], $e->getMessage(), 401);
        }

        return $this->sendResponse($user, 'User data retrieved', 200);
    }
}
