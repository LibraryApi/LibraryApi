<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Book\StoreBookRequest;
use App\Http\Requests\Book\UpdateBookRequest;
use App\Http\Resources\BookResource;
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

    /**
     * Display a listing of the resource.
     */
    public function index(): \Illuminate\Http\JsonResponse
    {
        $books = BookResource::collection(Book::all());
        return  response()->json($books);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBookRequest $request): \Illuminate\Http\JsonResponse
    {
        $book = new Book([
            'title' => $request->input('title'),
            'author' => $request->input('author'),
            'description' => $request->input('description'),
            'user_id' => auth()->user()->id,
        ]);

        $this->roleService->assignRoleToUser(auth()->user(), USER::ROLE_AUTHOR);

        $this->authorize('create', $book);
        $book->save();
        $book = new BookResource($book);
        return response()->json($book, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): \Illuminate\Http\JsonResponse
    {
        $book = new BookResource(Book::findOrFail($id));
        return response()->json($book);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBookRequest $request, $id): \Illuminate\Http\JsonResponse
    {

        $request->validated();
        
        $book = Book::find($id);

        if (!$book) {
            return response()->json(['error' => 'Книга не найдена'], 404);
        }
        $this->authorize('update', $book);
        $book->update($request->all());
        $book = new BookResource($book);

        return response()->json($book, 200);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): \Illuminate\Http\JsonResponse
    {
        $book = Book::find($id);

        if (!$book) {
            return response()->json(['error' => 'Книга не найдена'], 404);
        }

        $this->authorize('delete', $book);
        $book->delete();

        return response()->json(['message' => 'Книга успешно удалена!'], 200);
    }
}
