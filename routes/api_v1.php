<?php

use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\ExportController;
use App\Http\Controllers\Api\V1\Book\BookController;
use App\Http\Controllers\Api\V1\PostController;
use App\Http\Controllers\Api\V1\CommentController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\Book\ChapterController;
use App\Http\Controllers\Api\V1\ImageController;
use App\Http\Controllers\Api\V1\SubscriptionController;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Telegram\WebhookController;
use App\Http\Requests\Auth\RegisterRequest;
use App\Services\RoleService;



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

    Route::apiResource('images', ImageController::class)->except("index", "destroy", "update");

    Route::get('/user/getUserWithToken', [UserController::class, 'getUserWithToken']);
});

Route::get('export', [ExportController::class, 'export']);

Route::prefix('subscriptions')->group(function () {
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/', [SubscriptionController::class, 'index']);
        Route::post('/', [SubscriptionController::class, 'subscribe']);
        Route::delete('/{subscription}', [SubscriptionController::class, 'unsubscribe']);
    });
});

Route::prefix('/bot')->group(function () {
    Route::post('/webhook', [WebhookController::class, 'setWebhook']);
    Route::delete('/webhook', [WebhookController::class, 'deleteWebhook']);
    Route::get('/webhook', [WebhookController::class, 'getWebhookInfo']);

    Route::post('/library_api', [WebhookController::class, 'LibraryApiHandler']);
    Route::post('/library_news', [WebhookController::class, 'LibraryNewsHandler']);
    Route::post('/library_admin', [WebhookController::class, 'LibraryAdminHandler']);
});
