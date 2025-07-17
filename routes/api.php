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
                Route::post('register', [AuthController::class, 'register'])->name('register');
                Route::post('login', [AuthController::class, 'login'])->name('login');
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
