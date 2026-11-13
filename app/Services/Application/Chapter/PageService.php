<?php

namespace App\Services\Application\Chapter;

use App\Models\Chapter;
use App\Models\Page;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class PageService
{
    public function all(Chapter $chapter): Collection
    {
        return $chapter->pages()->get();
    }

    public function findPage(Chapter $chapter, int $pageId): ?Page
    {
        return $chapter->pages()->find($pageId);
    }

    public function create(Chapter $chapter, array $data): Page
    {
        $number = $data['number'] ?? ($chapter->pages()->max('number') + 1);

        $page = $chapter->pages()->create([
            'title' => $data['title'] ?? null,
            'content' => $data['content'],
            'number' => $number,
        ]);

        $this->resequence($chapter);
        $this->syncChapterContent($chapter);

        return $page->fresh();
    }

    /**
     * Массовое добавление страниц — например, когда автор вставляет текст
     * с разделителями и сразу получает несколько страниц.
     *
     * @param  array<int, array{title?: ?string, content: string}>  $pages
     * @return array<int, Page>
     */
    public function createMany(Chapter $chapter, array $pages): array
    {
        return DB::transaction(function () use ($chapter, $pages) {
            $start = (int) $chapter->pages()->max('number');

            $created = [];

            foreach (array_values($pages) as $index => $page) {
                $created[] = $chapter->pages()->create([
                    'title' => $page['title'] ?? null,
                    'content' => $page['content'],
                    'number' => $start + $index + 1,
                ]);
            }

            $this->resequence($chapter);
            $this->syncChapterContent($chapter);

            return $created;
        });
    }

    /**
     * @param  array<int, int>  $ids
     */
    public function reorder(Chapter $chapter, array $ids): void
    {
        DB::transaction(function () use ($chapter, $ids) {
            $pages = $chapter->pages()->get();
            $ordered = collect($ids)
                ->map(fn ($id) => $pages->firstWhere('id', (int) $id))
                ->filter()
                ->values();

            $pages
                ->reject(fn (Page $page) => $ordered->contains('id', $page->id))
                ->each(fn (Page $page) => $ordered->push($page));

            $ordered->each(function (Page $page, int $index) {
                if ((int) $page->number !== $index + 1) {
                    $page->update(['number' => $index + 1]);
                }
            });

            $this->syncChapterContent($chapter);
        });
    }

    public function update(Page $page, array $data): Page
    {
        $page->update($data);

        $this->resequence($page->chapter);
        $this->syncChapterContent($page->chapter);

        return $page->fresh();
    }

    public function delete(Page $page): void
    {
        $chapter = $page->chapter;
        $page->delete();

        $this->resequence($chapter);
        $this->syncChapterContent($chapter);
    }

    /**
     * Нумерация страниц всегда идёт подряд: 1, 2, 3… — без дыр и дублей.
     */
    private function resequence(Chapter $chapter): void
    {
        $chapter->pages()->get()->values()->each(function (Page $item, int $index) {
            if ((int) $item->number !== $index + 1) {
                $item->update(['number' => $index + 1]);
            }
        });
    }

    /**
     * Держим legacy-поле chapter.content синхронным с первой страницей:
     * старые клиенты и превью в списке глав продолжают работать.
     */
    private function syncChapterContent(Chapter $chapter): void
    {
        $firstPage = $chapter->pages()->first();

        if ($firstPage && $chapter->content !== $firstPage->content) {
            $chapter->update(['content' => $firstPage->content]);
        }
    }
}
