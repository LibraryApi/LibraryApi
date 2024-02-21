<?php

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
    Route::group(['middleware' => 'auth:sanctum'], function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user', [AuthController::class, 'getUser']);
        Route::post('/refresh-token', [AuthController::class, 'refreshToken']);
    });

    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});


Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/comments', [CommentController::class, 'index']);
    Route::post('/comments', [CommentController::class, 'store']);
    Route::patch('/comments/{comment}', [CommentController::class, 'update']);
    Route::get('/comments/{comment}', [CommentController::class, 'show']);
    Route::delete('/comments/{id}', [CommentController::class, 'destroy']);

    Route::get('/users', [UserController::class, 'index']);
    Route::get('/users/{id}', [UserController::class, 'show']);
    Route::patch('/users/{id}', [UserController::class, 'update']);
    Route::delete('/users/{id}', [UserController::class, 'destroy']);
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

Route::prefix('/books')->group(function () {
    Route::group(['middleware' => 'auth:sanctum'], function () {
        Route::post('/', [BookController::class, 'store']);
        Route::patch('/{bookId}', [BookController::class, 'update']);
        Route::delete('/{bookId}', [BookController::class, 'destroy']);
        Route::post('/{bookId}/chapters', [ChapterController::class, 'store']); 
        Route::patch('/{bookId}/chapters/{chapterId}', [ChapterController::class, 'update']); 
        Route::delete('/{bookId}/chapters/{chapterId}', [ChapterController::class, 'destroy']); 
    });

    Route::get('/', [BookController::class, 'index']);
    Route::get('/{bookId}', [BookController::class, 'show']);
    Route::get('/{bookId}/chapters', [ChapterController::class, 'index']); 
    Route::get('/{bookId}/chapters/{chapterId}', [ChapterController::class, 'show']);
});

Route::prefix('/bot')->group(function () {
    Route::post('/webhook', [WebhookController::class, 'setWebhook']);
    Route::delete('/webhook', [WebhookController::class, 'deleteWebhook']);
    Route::get('/webhook', [WebhookController::class, 'getWebhookInfo']);
    
    Route::post('/library_api', [WebhookController::class, 'LibraryApiHandler']);
    Route::post('/library_news', [WebhookController::class, 'LibraryNewsHandler']);
    Route::post('/library_admin', [WebhookController::class, 'LibraryAdminHandler']);
});
