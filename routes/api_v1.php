<?php

use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Telegram\WebhookController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\Book\BookController;
/* use Laravel\Sanctum\Http\Controllers\CsrfCookieController;
use Laravel\Sanctum\Http\Controllers\AccessTokenController; */
use App\Http\Controllers\Api\V1\PostController;
use App\Http\Controllers\Api\V1\CommentController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\Book\ChapterController;


Route::prefix('auth')->group(function () {
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user', [AuthController::class, 'getUser']);
        Route::post('/refresh-token', [AuthController::class, 'refreshToken']);
    });

    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::apiResource('comments', CommentController::class);
    Route::apiResource('users', UserController::class);
    Route::apiResource('posts', PostController::class);
    Route::apiResource('books', BookController::class);
    Route::apiResource('books.chapters', ChapterController::class);
    Route::apiResource('categories', CategoryController::class);
});

Route::prefix('/bot')->group(function () {
    Route::post('/webhook', [WebhookController::class, 'setWebhook']);
    Route::delete('/webhook', [WebhookController::class, 'deleteWebhook']);
    Route::get('/webhook', [WebhookController::class, 'getWebhookInfo']);
    
    Route::post('/library_api', [WebhookController::class, 'LibraryApiHandler']);
    Route::post('/library_news', [WebhookController::class, 'LibraryNewsHandler']);
    Route::post('/library_admin', [WebhookController::class, 'LibraryAdminHandler']);
});
