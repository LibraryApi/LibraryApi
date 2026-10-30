<?php

namespace App\Services\Application\Chapter;

use App\Models\Book;
use App\Models\Chapter;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ChapterService
{
    public function findBook(int $bookId): ?Book
    {
        return Book::find($bookId);
    }

    public function paginate(Book $book, int $perPage): LengthAwarePaginator
    {
        return $book->chapters()
            ->orderBy('number')
            ->orderBy('id')
            ->paginate($perPage);
    }

    public function findChapter(Book $book, int $chapterId): ?Chapter
    {
        return $book->chapters()->find($chapterId);
    }

    public function create(Book $book, array $data): Chapter
    {
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

        return $chapter;
    }

    public function update(Chapter $chapter, array $data): Chapter
    {
        $chapter->update($data);

        return $chapter;
    }

    public function delete(Chapter $chapter): void
    {
        $chapter->delete();
    }
}
