<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\BookController;
/* use Laravel\Sanctum\Http\Controllers\CsrfCookieController;
use Laravel\Sanctum\Http\Controllers\AccessTokenController; */
use App\Http\Controllers\Api\V1\PostController;
use App\Http\Controllers\Api\V1\CommentController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\AuthController;


Route::prefix('auth')->group(function () {
    Route::group(['middleware' => 'auth:sanctum'], function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/user', [AuthController::class, 'user']);
        Route::post('/refresh-token', [AuthController::class, 'refreshToken']);
    });

    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});


Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/comments', [CommentController::class, 'index']);
    Route::get('/comments/{id}', [CommentController::class, 'show']);


    Route::get('/users', [UserController::class, 'index']);
    Route::get('/users/{id}', [UserController::class, 'show']);
});

Route::prefix('/books')->group(function () {
    Route::get('/', [BookController::class, 'index']);
    Route::get('/{id}', [BookController::class, 'show']);
    Route::post('/', [BookController::class, 'store']);
    Route::patch('/{id}', [BookController::class, 'update']);
    Route::delete('/{id}', [BookController::class, 'destroy']);
});

Route::prefix('/posts')->group(function () {
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::post('/', [PostController::class, 'store']);
        Route::patch('/{id}', [PostController::class, 'update']);
        Route::delete('/{id}', [PostController::class, 'destroy']);
    });
    Route::get('/', [PostController::class, 'index']);
    Route::get('/{id}', [PostController::class, 'show']);
});
