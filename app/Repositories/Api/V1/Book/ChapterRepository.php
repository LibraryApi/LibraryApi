<?php

namespace App\Repositories\Api\V1\Book;

use App\Models\Book;
use App\Models\Chapter;

class ChapterRepository
{
    public function getChaptersByBook(int $bookId)
    {
        return Book::find($bookId)->chapters;
    }

    public function findChapterByBook(int $bookId, int $chapterId): ?Chapter
    {
        return Book::find($bookId)?->chapters()->find($chapterId);
    }

    public function createChapter(Book $book, Chapter $chapter)
    {
        $book->chapters()->save($chapter);
    }

    public function updateChapter(Chapter $chapter, array $data)
    {
        $chapter->update($data);
    }

    public function deleteChapter(Chapter $chapter)
    {
        $chapter->delete();
    }
}