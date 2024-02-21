<?php

namespace App\Http\Controllers\Api\V1\Book;

use App\Http\Controllers\Controller;
use App\Http\Requests\Book\StoreChapterRequest;
use App\Http\Requests\Book\UpdateChapterRequest;
use App\Http\Resources\Books\ChapterResource;
use App\Models\Chapter;
use App\Models\Book;

class ChapterController extends Controller
{
    public function index($bookId)
    {
        $book = Book::find($bookId);

        if (!$book) {
            return response()->json(['error' => 'Книга не найдена'], 404);
        }
        if (!$book->chapters) {
            return response()->json(['error' => 'у книги еще нет глав'], 404);
        }

        $chapters = ChapterResource::collection($book->chapters);

        return response()->json($chapters);
    }

    public function show($bookId, $chapterId)
    {
        $book = Book::find($bookId);
        if (!$book) {
            return response()->json(['error' => 'Книга не найдена'], 404);
        }

        $chapter = $book->chapters()->find($chapterId);
        if (!$chapter) {
            return response()->json(['error' => 'Глава не найдена'], 404);
        }
        $chapter = new ChapterResource($chapter);
        return response()->json($chapter);
    }

    public function store(StoreChapterRequest $request, $bookId)
    {
        $data = $request->validated();

        $book = Book::find($bookId);

        if (!$book) {
            return response()->json(['error' => 'Книга не найдена'], 404);
        }

        $this->authorize('create', Chapter::class);

        $chapter = new Chapter([
            'title' => $data['title'],
            'content' => $data['content'],
            'number' => $data['number'] ?? null,
            'duration' => $data['duration'] ?? null,
            'characters' => $data['characters'] ?? null,
            'images' => $data['images'] ?? null,
            'comments_count' => 0,
            'likes_count' => 0,
            'views_count' => 0,
        ]);

        $book->chapters()->save($chapter);

        $chapter = new ChapterResource($chapter);

        return response()->json($chapter, 201);
    }

    public function update(UpdateChapterRequest $request, $bookId, $chapterId): \Illuminate\Http\JsonResponse
    {
        $data = $request->validated();

        $book = Book::find($bookId);

        if (!$book) {
            return response()->json(['error' => 'Книга не найдена'], 404);
        }

        $chapter = $book->chapters()->find($chapterId);

        if (!$chapter) {
            return response()->json(['error' => 'Глава не найдена'], 404);
        }

        $this->authorize('update', $chapter);

        $chapter->update($data);
        $chapter = new ChapterResource($chapter);

        return response()->json($chapter, 200);
    }

    public function destroy($bookId, $chapterId): \Illuminate\Http\JsonResponse
    {
        $book = Book::find($bookId);

        if (!$book) {
            return response()->json(['error' => 'Книга не найдена'], 404);
        }

        $chapter = $book->chapters()->find($chapterId);

        if (!$chapter) {
            return response()->json(['error' => 'Глава не найдена'], 404);
        }

        $this->authorize('delete', $chapter);
        $chapter->delete();

        return response()->json(['message' => 'Глава успешно удалена!'], 200);
    }
}
