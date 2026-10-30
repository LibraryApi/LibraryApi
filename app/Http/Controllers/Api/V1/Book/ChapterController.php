<?php

namespace App\Http\Controllers\Api\V1\Book;

use App\Http\Controllers\Controller;
use App\Http\Requests\Book\IndexChapterRequest;
use App\Http\Requests\Book\StoreChapterRequest;
use App\Http\Requests\Book\UpdateChapterRequest;
use App\Http\Resources\Books\ChapterResource;
use App\Http\Resources\ChapterCollection;
use App\Http\Resources\ErrorResource;
use App\Http\Resources\MessageResource;
use App\Models\Chapter;
use App\Services\Application\Chapter\ChapterService;
use Illuminate\Http\JsonResponse;

class ChapterController extends Controller
{
    public function __construct(private readonly ChapterService $chapterService)
    {
    }

    public function index(IndexChapterRequest $request, int $bookId): JsonResponse
    {
        $book = $this->chapterService->findBook($bookId);

        if (!$book) {
            return response()->json(new ErrorResource('Книга не найдена'), 404);
        }

        return response()->json(new ChapterCollection(
            $this->chapterService->paginate($book, $request->perPage())
        ));
    }

    public function show(int $bookId, int $chapterId): JsonResponse
    {
        $book = $this->chapterService->findBook($bookId);

        if (!$book) {
            return response()->json(new ErrorResource('Книга не найдена'), 404);
        }

        $chapter = $this->chapterService->findChapter($book, $chapterId);

        if (!$chapter) {
            return response()->json(new ErrorResource('Глава не найдена'), 404);
        }

        return response()->json(new ChapterResource($chapter));
    }

    public function store(StoreChapterRequest $request, int $bookId): JsonResponse
    {
        $book = $this->chapterService->findBook($bookId);

        if (!$book) {
            return response()->json(new ErrorResource('Книга не найдена'), 404);
        }

        $this->authorize('create', Chapter::class);

        return response()->json(
            new ChapterResource($this->chapterService->create($book, $request->validated())),
            201
        );
    }

    public function update(UpdateChapterRequest $request, int $bookId, int $chapterId): JsonResponse
    {
        $book = $this->chapterService->findBook($bookId);

        if (!$book) {
            return response()->json(new ErrorResource('Книга не найдена'), 404);
        }

        $chapter = $this->chapterService->findChapter($book, $chapterId);

        if (!$chapter) {
            return response()->json(new ErrorResource('Глава не найдена'), 404);
        }

        $this->authorize('update', $chapter);

        return response()->json(new ChapterResource(
            $this->chapterService->update($chapter, $request->validated())
        ));
    }

    public function destroy(int $bookId, int $chapterId): JsonResponse
    {
        $book = $this->chapterService->findBook($bookId);

        if (!$book) {
            return response()->json(new ErrorResource('Книга не найдена'), 404);
        }

        $chapter = $this->chapterService->findChapter($book, $chapterId);

        if (!$chapter) {
            return response()->json(new ErrorResource('Глава не найдена'), 404);
        }

        $this->authorize('delete', $chapter);
        $this->chapterService->delete($chapter);

        return response()->json(new MessageResource('Глава успешно удалена!'));
    }
}
