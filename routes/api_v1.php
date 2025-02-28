<?php

use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\ExportController;
use App\Http\Controllers\Api\V1\Book\BookController;
use App\Http\Controllers\Api\V1\PaymentController;
use App\Http\Controllers\Api\V1\PostController;
use App\Http\Controllers\Api\V1\CommentController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\Book\ChapterController;
use App\Http\Controllers\Api\V1\SubscriptionController;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Telegram\WebhookController;

Route::prefix('auth')->group(function () {
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user', [AuthController::class, 'getUser']);
        Route::post('/refresh-token', [AuthController::class, 'refreshToken']);
    });

    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

Route::get('books', [BookController::class, 'index']);
Route::get('categories', [CategoryController::class, 'index']);
Route::get('posts', [PostController::class, 'index']);
Route::get('comments', [CommentController::class, 'index']);
Route::get('users', [UserController::class, 'index']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::apiResource('comments', CommentController::class)->except(['index']);
    Route::apiResource('users', UserController::class)->except(['index']);
    Route::apiResource('posts', PostController::class)->except(['index']);

    Route::apiResource('books', BookController::class)->except(['index']);

    Route::apiResource('books.chapters', ChapterController::class);
    Route::apiResource('categories', CategoryController::class)->except(['index']);
});

Route::get('export', [ExportController::class, 'export']);

Route::prefix('/bot')->group(function () {
    Route::post('/webhook', [WebhookController::class, 'setWebhook']);
    Route::delete('/webhook', [WebhookController::class, 'deleteWebhook']);
    Route::get('/webhook', [WebhookController::class, 'getWebhookInfo']);

    Route::post('/api', [WebhookController::class, 'LibraryApiHandler']);
    Route::post('/news', [WebhookController::class, 'LibraryNewsHandler']);
    Route::post('/admin', [WebhookController::class, 'LibraryAdminHandler']);
});

Route::prefix('subscriptions')->group(function () {
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/user', [SubscriptionController::class, 'userSubscriptions']);
        Route::get('/', [SubscriptionController::class, 'getAllSubscriptions']); //  все подписки
        Route::post('/', [SubscriptionController::class, 'storeSubscription']); //  создание подписки
        Route::patch('/{id}', [SubscriptionController::class, 'updateSubscription']); // обновление подписки
        Route::delete('/{id}', [SubscriptionController::class, 'destroySubscription']); //  удаление подписки
        Route::post('/subscribe/{subscriptionId}', [SubscriptionController::class, 'subscribeToSubscription']); // оформление подписки
        Route::delete('/unsubscribe/{subscriptionId}', [SubscriptionController::class, 'unsubscribeFromSubscription']); // отмена подписки
    });
    Route::post('payments/webhook', [SubscriptionController::class, 'paymentsWebhook']); // получаем уведомление от платежной системы (успешно или нет)
});
