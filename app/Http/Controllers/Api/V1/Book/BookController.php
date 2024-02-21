<?php

namespace App\Http\Controllers\Api\V1\Book;

use App\Http\Controllers\Controller;
use App\Http\Requests\Book\StoreBookRequest;
use App\Http\Requests\Book\UpdateBookRequest;
use App\Http\Resources\Books\BookResource;
use App\Services\RoleService;
use App\Models\Book;
use App\Models\User;

class BookController extends Controller
{
    protected $roleService;

    public function __construct(RoleService $roleService)
    {
        $this->roleService = $roleService;
    }

    public function index(): \Illuminate\Http\JsonResponse
    {
        $books = BookResource::collection(Book::all());
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
