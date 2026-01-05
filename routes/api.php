<?php

declare(strict_types=1);

use App\Http\Controllers\API\V1\AuthController;
use App\Http\Controllers\API\V1\TranslationController;
use App\Http\Controllers\API\V1\WordController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')
    ->name('api.v1.')
    ->middleware('api')
    ->group(function() {
        Route::prefix('/user')
            ->name('user.')
            ->group(function() {
                // Rate limiting: 3 registrations per hour per IP
                Route::post('register', [AuthController::class, 'register'])
                    ->middleware('throttle:3,60')
                    ->name('register');

                // Rate limiting: 5 login attempts per minute per IP
                Route::post('login', [AuthController::class, 'login'])
                    ->middleware('throttle:5,1')
                    ->name('login');

                // Logout endpoint (requires JWT)
                Route::post('logout', [AuthController::class, 'logout'])
                    ->middleware('jwt.verify')
                    ->name('logout');

                // Route::get('/',         [AuthController::class, 'getUser'])->name('getUser');
            });

        Route::middleware('jwt.verify')->group(function() {
            Route::apiResource('words', WordController::class)
                ->only('store', 'update', 'destroy');

            Route::apiResource('translations', TranslationController::class)
                ->only('store', 'update', 'destroy');
        });

        Route::apiResource('translations', TranslationController::class)
            ->only('index', 'show');

        Route::apiResource('words', WordController::class)
            ->only('index', 'show');
    });
