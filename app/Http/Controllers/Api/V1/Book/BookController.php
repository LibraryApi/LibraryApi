<?php

namespace App\Http\Controllers\Api\V1\Book;

use App\Http\Controllers\Controller;
use App\Http\Requests\Book\StoreBookRequest;
use App\Http\Requests\Book\UpdateBookRequest;
use App\Http\Resources\Books\BookResource;
use App\Services\WrapperServices\RoleService;
use App\Models\Book;
use App\Models\User;
use App\Services\WrapperServices\Telegram\WebhookSenders\WebhookSender;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BookController extends Controller
{
    protected $roleService;
    protected $telegram;

    public function __construct(RoleService $roleService)
    {
        $this->roleService = $roleService;
        $this->telegram = new WebhookSender();
    }

    public function index(Request $request): \Illuminate\Http\JsonResponse
    {
        $query = Book::query();

        // Фильтр по автору
        if ($request->filled('author')) {
            $query->where('author', $request->input('author'));
        }

        // Фильтр по категории
        if ($request->filled('category')) {
            $query->whereHas('categories', function ($categoryQuery) use ($request) {
                $categoryQuery->where('name', $request->input('category'));
            });
        }

        // Фильтр по поисковому запросу
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->input('search') . '%')
                ->orWhere('description', 'like', '%' . $request->input('search') . '%');
        }

        // Сортировка
        if ($request->filled('sortDate')) {
            $query->orderBy('created_at', $request->input('sortDate') === 'asc' ? 'asc' : 'desc');
        }

        if ($request->filled('sortName')) {
            $query->orderBy('title', $request->input('sortName') === 'asc' ? 'asc' : 'desc');
        }

        $perPage = $request->input('per_page', 3);
        $books = $query->paginate($perPage);

        return response()->json([
            'total_books' => $books->total(),
            'per_page' => $books->perPage(),
            'current_page' => $books->currentPage(),
            'last_page' => $books->lastPage(),
            'books' => BookResource::collection($books->items()),
        ]);
    }


    public function store(StoreBookRequest $request): \Illuminate\Http\JsonResponse
    {
        $data = $request->validated();

        $book = new Book([
            'title' => $data['title'],
            'author' => $data['author'],
            'description' => $data['description'],
            'user_id' => auth()->user()->id,
            'cover_image' => $data['cover_image'] ?? null,
            'author_bio' => $data['author_bio'] ?? null,
            'language' => $data['language'] ?? 'ru',
            'rating' => $data['rating'] ?? null,
            'number_of_pages' => $data['number_of_pages'] ?? null,
            'is_published' => $data['is_published'] ?? false,
            'comments_count' => 0,
            'likes_count' => 0,
            'views_count' => 0,
        ]);

        $this->roleService->assignRoleToUser(auth()->user(), User::ROLE_AUTHOR);

        $this->authorize('create', $book);
        $book->save();

        if (isset($data['categories'])) {
            $book->categories()->attach($data['categories']);
        }

        $book = new BookResource($book);

        return response()->json($book, 201);
    }


    public function show(string $bookId): \Illuminate\Http\JsonResponse
    {
        $book = Book::find($bookId);
        if (!$book) {
            return response()->json(['error' => 'Книга не найдена'], 404);
        }
        $book = new BookResource($book);

        $user = auth()->user();

        /* $telegram = $this->telegram->createMessageSender();
        $telegram->message(['text' => "Пользователь {$user->name} открыл книгу \"{$book->title}\""])->sendMessage(); */

        return response()->json($book);
    }

    public function update(UpdateBookRequest $request, $bookId): \Illuminate\Http\JsonResponse
    {
        $data = $request->validated();

        $book = Book::find($bookId);

        if (!$book) {
            return response()->json(['error' => 'Книга не найдена'], 404);
        }

        $this->authorize('update', $book);
        $book->update($data);

        if (isset($data['categories'])) {
            $book->categories()->sync($data['categories']);
        }

        $book = new BookResource($book);

        return response()->json($book, 200);
    }

    public function destroy($bookId): \Illuminate\Http\JsonResponse
    {
        $book = Book::find($bookId);


        if (!$book) {
            return response()->json(['error' => 'Книга не найдена'], 404);
        }

        $this->authorize('delete', $book);
        $book->delete();

        return response()->json(['message' => 'Книга успешно удалена!'], 200);
    }
}
