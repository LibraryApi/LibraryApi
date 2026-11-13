<?php

namespace App\Http\Controllers\Api\V1\Book;

use App\Http\Controllers\Controller;
use App\Http\Requests\Book\BulkStorePagesRequest;
use App\Http\Requests\Book\StorePageRequest;
use App\Http\Requests\Book\UpdatePageRequest;
use App\Http\Resources\Books\PageResource;
use App\Http\Resources\ErrorResource;
use App\Http\Resources\MessageResource;
use App\Services\Application\Chapter\ChapterService;
use App\Services\Application\Chapter\PageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function __construct(
        private readonly ChapterService $chapterService,
        private readonly PageService $pageService
    ) {
    }

    public function index(int $bookId, int $chapterId): JsonResponse
    {
        $chapter = $this->findChapter($bookId, $chapterId);

        if (!$chapter) {
            return response()->json(new ErrorResource('Глава не найдена'), 404);
        }

        $pages = PageResource::collection($this->pageService->all($chapter))->resolve();

        return response()->json([
            'data' => $pages,
            'total' => count($pages),
        ]);
    }

    public function show(int $bookId, int $chapterId, int $pageId): JsonResponse
    {
        $chapter = $this->findChapter($bookId, $chapterId);

        if (!$chapter) {
            return response()->json(new ErrorResource('Глава не найдена'), 404);
        }

        $page = $this->pageService->findPage($chapter, $pageId);

        if (!$page) {
            return response()->json(new ErrorResource('Страница не найдена'), 404);
        }

        return response()->json(new PageResource($page));
    }

    public function store(StorePageRequest $request, int $bookId, int $chapterId): JsonResponse
    {
        $chapter = $this->findChapter($bookId, $chapterId);

        if (!$chapter) {
            return response()->json(new ErrorResource('Глава не найдена'), 404);
        }

        $this->authorize('update', $chapter);

        return response()->json(
            new PageResource($this->pageService->create($chapter, $request->validated())),
            201
        );
    }

    public function bulk(BulkStorePagesRequest $request, int $bookId, int $chapterId): JsonResponse
    {
        $chapter = $this->findChapter($bookId, $chapterId);

        if (!$chapter) {
            return response()->json(new ErrorResource('Глава не найдена'), 404);
        }

        $this->authorize('update', $chapter);

        $pages = $this->pageService->createMany($chapter, $request->validated('pages'));

        return response()->json([
            'data' => PageResource::collection($pages)->resolve(),
            'total' => count($pages),
        ], 201);
    }

    public function reorder(Request $request, int $bookId, int $chapterId): JsonResponse
    {
        $chapter = $this->findChapter($bookId, $chapterId);

        if (!$chapter) {
            return response()->json(new ErrorResource('Глава не найдена'), 404);
        }

        $this->authorize('update', $chapter);

        $data = $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer',
        ]);

        $this->pageService->reorder($chapter, $data['ids']);

        return response()->json(new MessageResource('Порядок страниц обновлён'));
    }

    public function update(UpdatePageRequest $request, int $bookId, int $chapterId, int $pageId): JsonResponse
    {
        $chapter = $this->findChapter($bookId, $chapterId);

        if (!$chapter) {
            return response()->json(new ErrorResource('Глава не найдена'), 404);
        }

        $page = $this->pageService->findPage($chapter, $pageId);

        if (!$page) {
            return response()->json(new ErrorResource('Страница не найдена'), 404);
        }

        $this->authorize('update', $chapter);

        return response()->json(
            new PageResource($this->pageService->update($page, $request->validated()))
        );
    }

    public function destroy(int $bookId, int $chapterId, int $pageId): JsonResponse
    {
        $chapter = $this->findChapter($bookId, $chapterId);

        if (!$chapter) {
            return response()->json(new ErrorResource('Глава не найдена'), 404);
        }

        $page = $this->pageService->findPage($chapter, $pageId);

        if (!$page) {
            return response()->json(new ErrorResource('Страница не найдена'), 404);
        }

        $this->authorize('update', $chapter);
        $this->pageService->delete($page);

        return response()->json(new MessageResource('Страница успешно удалена!'));
    }

    private function findChapter(int $bookId, int $chapterId)
    {
        $book = $this->chapterService->findBook($bookId);

        if (!$book) {
            return null;
        }

        return $book->chapters()->find($chapterId);
    }
}
