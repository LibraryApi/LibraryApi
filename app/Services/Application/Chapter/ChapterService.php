<?php

namespace App\Services\Application\Chapter;

use App\Models\Book;
use App\Models\Chapter;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ChapterService
{
    public function findBook(int $bookId): ?Book
    {
        return Book::find($bookId);
    }

    public function paginate(Book $book, int $perPage): LengthAwarePaginator
    {
        return $book->chapters()
            ->with('pageList')
            ->withCount('pages')
            ->orderBy('number')
            ->orderBy('id')
            ->paginate($perPage);
    }

    public function findChapter(Book $book, int $chapterId): ?Chapter
    {
        return $book->chapters()->with('pages')->find($chapterId);
    }

    public function create(Book $book, array $data): Chapter
    {
        $content = $data['content'] ?? '';

        $chapter = new Chapter([
            'title' => $data['title'],
            'content' => $content,
            'number' => $data['number'] ?? null,
            'characters' => $data['characters'] ?? null,
            'images' => $data['images'] ?? null,
            'comments_count' => 0,
            'likes_count' => 0,
            'views_count' => 0,
        ]);

        $book->chapters()->save($chapter);

        // Глава без страниц нечитабельна, поэтому текст, переданный при
        // создании, сразу становится её первой страницей.
        if (trim((string) $content) !== '') {
            $chapter->pages()->create([
                'title' => null,
                'content' => $content,
                'number' => 1,
            ]);
        }

        return $chapter->load('pages');
    }

    public function update(Chapter $chapter, array $data): Chapter
    {
        $chapter->update($data);

        // Старый API (и бот) присылают текст прямо в главе — синхронизируем его
        // с первой страницей, чтобы контент не разъезжался.
        if (array_key_exists('content', $data)) {
            $firstPage = $chapter->pages()->first();

            if ($firstPage) {
                $firstPage->update(['content' => $data['content']]);
            } elseif (trim((string) $data['content']) !== '') {
                $chapter->pages()->create([
                    'title' => null,
                    'content' => $data['content'],
                    'number' => 1,
                ]);
            }
        }

        return $chapter->load('pages');
    }

    public function delete(Chapter $chapter): void
    {
        $book = $chapter->book;
        $chapter->delete();

        // После удаления главы пересобираем нумерацию, чтобы не было дыр.
        if ($book) {
            $this->reorder($book, []);
        }
    }

    /**
     * @param  array<int, int>  $ids
     */
    public function reorder(Book $book, array $ids): void
    {
        DB::transaction(function () use ($book, $ids) {
            $chapters = $book->chapters()->get();
            $ordered = collect($ids)
                ->map(fn ($id) => $chapters->firstWhere('id', (int) $id))
                ->filter()
                ->values();

            $chapters
                ->reject(fn (Chapter $chapter) => $ordered->contains('id', $chapter->id))
                ->each(fn (Chapter $chapter) => $ordered->push($chapter));

            // Нумерация глав всегда подряд: 1, 2, 3…
            $ordered->each(function (Chapter $chapter, int $index) {
                if ((int) $chapter->number !== $index + 1) {
                    $chapter->update(['number' => $index + 1]);
                }
            });
        });
    }
}
