<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WordController;
use App\Http\Controllers\API\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('/user')->name('user.')->group(function () {
    Route::post('register', [AuthController::class, 'register'])->name('register');
    Route::post('login', [AuthController::class, 'login'])->name('login');
    Route::get('/', [AuthController::class, 'getUser'])->name('getUser');
});

Route::middleware('jwt.verify')->group(function () {
    Route::apiResource('words', WordController::class)->only('store', 'update', 'destroy');
});

Route::apiResource('words', WordController::class)->only('index', 'show');
