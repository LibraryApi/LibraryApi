<?php

namespace App\Http\Controllers\Api\V1\Book;

use App\Http\Controllers\Controller;
use App\Http\Requests\Book\IndexBookRequest;
use App\Http\Requests\Book\StoreBookRequest;
use App\Http\Requests\Book\UpdateBookRequest;
use App\Http\Resources\BookAuthorCollection;
use App\Http\Resources\BookCollection;
use App\Http\Resources\Books\BookResource;
use App\Http\Resources\ErrorResource;
use App\Http\Resources\MessageResource;
use App\Models\Book;
use App\Services\Application\Book\BookService;
use Illuminate\Http\JsonResponse;

class BookController extends Controller
{
    public function __construct(private readonly BookService $bookService)
    {
    }

    public function index(IndexBookRequest $request): JsonResponse
    {
        $books = $this->bookService->paginate($request->filters(), $request->perPage());

        return response()->json(new BookCollection($books));
    }

    public function authors(): JsonResponse
    {
        return response()->json(new BookAuthorCollection($this->bookService->authors()));
    }

    public function store(StoreBookRequest $request): JsonResponse
    {
        $this->authorize('create', Book::class);

        $book = $this->bookService->create($request->validated(), $request->file('cover_image'));

        return response()->json(new BookResource($book), 201);
    }

    public function show(int $bookId): JsonResponse
    {
        $book = $this->bookService->find($bookId);

        if (!$book) {
            return response()->json(new ErrorResource('Книга не найдена'), 404);
        }

        return response()->json(new BookResource($book));
    }

    public function update(UpdateBookRequest $request, int $bookId): JsonResponse
    {
        $book = $this->bookService->findBasic($bookId);

        if (!$book) {
            return response()->json(new ErrorResource('Книга не найдена'), 404);
        }

        $this->authorize('update', $book);

        return response()->json(new BookResource(
            $this->bookService->update($book, $request->validated(), $request->file('cover_image'))
        ));
    }

    public function destroy(int $bookId): JsonResponse
    {
        $book = $this->bookService->findBasic($bookId);

        if (!$book) {
            return response()->json(new ErrorResource('Книга не найдена'), 404);
        }

        $this->authorize('delete', $book);
        $this->bookService->delete($book);

        return response()->json(new MessageResource('Книга успешно удалена!'));
    }
}
