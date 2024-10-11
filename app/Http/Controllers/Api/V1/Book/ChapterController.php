<?php

namespace App\Http\Controllers\Api\V1\Book;

use App\DTO\Book\ChapterDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Book\StoreChapterRequest;
use App\Http\Requests\Book\UpdateChapterRequest;
use App\Http\Resources\Books\ChapterResource;
use App\Services\Wrappers\Book\ChapterService;
use Exception;

class ChapterController extends Controller
{
    protected ChapterService $chapterService;

    public function __construct(ChapterService $chapterService)
    {
        $this->chapterService = $chapterService;
    }

    public function index($bookId)
    {
        try {
            $chapters = $this->chapterService->getChapters($bookId);
            return response()->json(ChapterResource::collection($chapters));
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }

    public function show($bookId, $chapterId)
    {
        try {
            $chapter = $this->chapterService->getChapter($bookId, $chapterId);
            return response()->json(new ChapterResource($chapter));
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }

    public function store(StoreChapterRequest $request, $bookId)
    {
        try {
            $chapterDTO = new ChapterDTO($request->validated());
            $chapter = $this->chapterService->createChapter($bookId, $chapterDTO);
            return response()->json(new ChapterResource($chapter), 201);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }

    public function update(UpdateChapterRequest $request, $bookId, $chapterId)
    {
        try {
            $chapterDTO = new ChapterDTO($request->validated());
            $chapter = $this->chapterService->updateChapter($bookId, $chapterId, $chapterDTO);
            return response()->json(new ChapterResource($chapter), 200);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }

    public function destroy($bookId, $chapterId)
    {
        try {
            $this->chapterService->deleteChapter($bookId, $chapterId);
            return response()->json(['message' => 'Глава успешно удалена!'], 200);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }
}