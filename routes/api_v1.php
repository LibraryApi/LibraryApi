<?php

use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\ExportController;
use App\Http\Controllers\Api\V1\FeedController;
use App\Http\Controllers\Api\V1\Book\BookController;
use App\Http\Controllers\Api\V1\PaymentController;
use App\Http\Controllers\Api\V1\PostController;
use App\Http\Controllers\Api\V1\CommentController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\Book\ChapterController;
use App\Http\Controllers\Api\V1\Book\PageController;
use App\Http\Controllers\Api\V1\SubscriptionController;
use App\Http\Controllers\Api\V1\AuthorSubscriptionController;
use App\Http\Controllers\Api\V1\InteractionController;
use App\Http\Controllers\Api\V1\WishlistController;
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
Route::get('books/authors', [BookController::class, 'authors']);
Route::get('books/{book}', [BookController::class, 'show']);
Route::get('categories', [CategoryController::class, 'index']);
Route::get('authors', [AuthorSubscriptionController::class, 'index']);
Route::get('posts/authors', [PostController::class, 'authors'])->middleware('posts.enabled');
Route::get('posts', [PostController::class, 'index'])->middleware('posts.enabled');
Route::get('comments', [CommentController::class, 'index']);
Route::get('users', [UserController::class, 'index']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('author-subscriptions', [AuthorSubscriptionController::class, 'subscriptions']);
    Route::post('authors/{id}/subscribe', [AuthorSubscriptionController::class, 'subscribe']);
    Route::delete('authors/{id}/subscribe', [AuthorSubscriptionController::class, 'unsubscribe']);
    Route::get('feed', [FeedController::class, 'index']);
    Route::post('{type}/{id}/like', [InteractionController::class, 'like'])->whereIn('type', ['book', 'books', 'chapter', 'chapters', 'post', 'posts', 'comment', 'comments']);
    Route::delete('{type}/{id}/like', [InteractionController::class, 'unlike'])->whereIn('type', ['book', 'books', 'chapter', 'chapters', 'post', 'posts', 'comment', 'comments']);
    Route::post('{type}/{id}/rating', [InteractionController::class, 'rate'])->whereIn('type', ['book', 'books', 'chapter', 'chapters', 'post', 'posts', 'comment', 'comments']);
    Route::delete('{type}/{id}/rating', [InteractionController::class, 'unrated'])->whereIn('type', ['book', 'books', 'chapter', 'chapters', 'post', 'posts', 'comment', 'comments']);
    Route::apiResource('comments', CommentController::class)->except(['index']);
    Route::post('users/{id}/follow', [UserController::class, 'follow']);
    Route::delete('users/{id}/follow', [UserController::class, 'unfollow']);
    Route::apiResource('users', UserController::class)->except(['index']);
    Route::apiResource('posts', PostController::class)->except(['index'])->middleware('posts.enabled');

    Route::apiResource('books', BookController::class)->except(['index', 'show']);

    // Список желаемого — отдельная сущность, не лайки
    Route::get('wishlist', [WishlistController::class, 'index']);
    Route::post('books/{book}/wishlist', [WishlistController::class, 'store']);
    Route::delete('books/{book}/wishlist', [WishlistController::class, 'destroy']);

    Route::put('books/{bookId}/chapters/order', [ChapterController::class, 'reorder']);
    Route::apiResource('books.chapters', ChapterController::class);

    // Страницы внутри главы — единица чтения и нумерации.
    Route::get('books/{bookId}/chapters/{chapterId}/pages', [PageController::class, 'index']);
    Route::post('books/{bookId}/chapters/{chapterId}/pages', [PageController::class, 'store']);
    Route::post('books/{bookId}/chapters/{chapterId}/pages/bulk', [PageController::class, 'bulk']);
    Route::put('books/{bookId}/chapters/{chapterId}/pages/order', [PageController::class, 'reorder']);
    Route::get('books/{bookId}/chapters/{chapterId}/pages/{pageId}', [PageController::class, 'show']);
    Route::match(['put', 'patch'], 'books/{bookId}/chapters/{chapterId}/pages/{pageId}', [PageController::class, 'update']);
    Route::delete('books/{bookId}/chapters/{chapterId}/pages/{pageId}', [PageController::class, 'destroy']);

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
