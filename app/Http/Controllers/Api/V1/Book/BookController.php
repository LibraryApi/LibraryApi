<?php

namespace App\Http\Controllers\Api\V1\Book;

use App\Http\Controllers\Controller;
use App\Http\Requests\Book\StoreBookRequest;
use App\Http\Requests\Book\UpdateBookRequest;
use App\Http\Resources\Books\BookResource;
use App\Services\RoleService;
use App\Models\Book;
use App\Models\User;
use App\Services\Telegram\WebhookSenders\WebhookSender;
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
        if ($request->has('author')) {
            $query->where('author', $request->input('author'));
        }

        if ($request->has('category')) {
            $query->whereHas('categories', function ($categoryQuery) use ($request) {
                $categoryQuery->where('name', $request->input('category'));
            });
        }

        $perPage = $request->input('per_page', 10);
        $books = $query->paginate($perPage);

        $books = BookResource::collection($books);

        $telegram = $this->telegram->createMessageSender('document');
        $telegram->message(["caption" => "отчет за апрель", "document" => Storage::get('/public/file.png'), "filename" => "отчет.doc"])->sendMessage();

        return response()->json($books);
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

        $telegram = $this->telegram->createMessageSender();
        $telegram->message(['text' => "Пользователь {$user->name} открыл книгу \"{$book->title}\""])->sendMessage();

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
