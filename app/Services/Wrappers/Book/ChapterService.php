<?php

namespace App\Services\Wrappers\Book;

use App\DTO\Book\ChapterDTO;
use App\Models\Book;
use App\Models\Chapter;
use App\Repositories\Api\V1\Book\ChapterRepository;
use Exception;
use Illuminate\Support\Facades\Gate;

class ChapterService
{
    protected ChapterRepository $chapterRepository;

    public function __construct(ChapterRepository $chapterRepository)
    {
        $this->chapterRepository = $chapterRepository;
    }

    public function getChapters(int $bookId)
    {
        $book = Book::find($bookId);

        if (!$book) {
            throw new Exception('Книга не найдена');
        }

        $chapters = $this->chapterRepository->getChaptersByBook($bookId);
        if (!$chapters) {
            throw new Exception('У книги нет глав');
        }

        return $chapters;
    }

    public function getChapter(int $bookId, int $chapterId)
    {
        $chapter = $this->chapterRepository->findChapterByBook($bookId, $chapterId);

        if (!$chapter) {
            throw new Exception('Глава не найдена');
        }

        return $chapter;
    }

    public function createChapter(int $bookId, ChapterDTO $chapterDTO)
    {
        $book = Book::find($bookId);

        if (!$book) {
            throw new Exception('Книга не найдена');
        }

        Gate::authorize('create', Chapter::class);

        $chapter = new Chapter((array)$chapterDTO);
        $this->chapterRepository->createChapter($book, $chapter);

        return $chapter;
    }

    public function updateChapter(int $bookId, int $chapterId, ChapterDTO $chapterDTO)
    {
        $chapter = $this->getChapter($bookId, $chapterId);

        Gate::authorize('update', $chapter);

        $this->chapterRepository->updateChapter($chapter, (array)$chapterDTO);

        return $chapter;
    }

    public function deleteChapter(int $bookId, int $chapterId)
    {
        $chapter = $this->getChapter($bookId, $chapterId);

        Gate::authorize('delete', $chapter);

        $this->chapterRepository->deleteChapter($chapter);
    }
}