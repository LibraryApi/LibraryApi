<?php

namespace App\Http\Controllers\Api\V1\Book;

use App\DTO\Book\BookDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Book\StoreBookRequest;
use App\Http\Requests\Book\UpdateBookRequest;
use App\Http\Resources\Books\BookResource;
use App\Services\Wrappers\Book\BookService;
use Illuminate\Http\Request;
use Exception;

class BookController extends Controller
{
    protected BookService $bookService;

    public function __construct(BookService $bookService)
    {
        $this->bookService = $bookService;
    }

    public function index(Request $request): \Illuminate\Http\JsonResponse
    {
        $books = $this->bookService->getAllBooks($request->input('per_page', 10), $request->all());
        return response()->json(BookResource::collection($books));
    }

    public function store(StoreBookRequest $request): \Illuminate\Http\JsonResponse
    {
        $bookDTO = new BookDTO($request->validated());
        $book = $this->bookService->createBook($bookDTO, auth()->user());

        return response()->json(new BookResource($book), 201);
    }

    public function update(UpdateBookRequest $request, string $bookId): \Illuminate\Http\JsonResponse
    {
        $bookDTO = new BookDTO($request->validated());
        $book = $this->bookService->updateBook($bookId, $bookDTO);

        return response()->json(new BookResource($book), 200);
    }

    public function show(string $bookId): \Illuminate\Http\JsonResponse
    {
        $book = $this->bookService->getBook($bookId);
        return response()->json(new BookResource($book));
    }

    public function destroy(string $bookId): \Illuminate\Http\JsonResponse
    {
        $this->bookService->deleteBook($bookId);
        return response()->json(['message' => 'Книга успешно удалена!'], 200);
    }
}
