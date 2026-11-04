<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Books\BookResource;
use App\Models\Book;
use App\Models\Wishlist;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    /**
     * Книги из списка желаемого текущего пользователя.
     */
    public function index(Request $request)
    {
        $books = Book::query()
            ->whereIn('id', Wishlist::where('user_id', $request->user()->id)->select('book_id'))
            ->with('categories')
            ->withCount('chapters')
            ->latest()
            ->get();

        return BookResource::collection($books);
    }

    public function store(Request $request, Book $book): JsonResponse
    {
        Wishlist::firstOrCreate([
            'user_id' => $request->user()->id,
            'book_id' => $book->id,
        ]);

        return response()->json([
            'in_wishlist' => true,
            'wishlist_count' => Wishlist::where('user_id', $request->user()->id)->count(),
        ], 201);
    }

    public function destroy(Request $request, Book $book): JsonResponse
    {
        Wishlist::where([
            'user_id' => $request->user()->id,
            'book_id' => $book->id,
        ])->delete();

        return response()->json([
            'in_wishlist' => false,
            'wishlist_count' => Wishlist::where('user_id', $request->user()->id)->count(),
        ]);
    }
}
