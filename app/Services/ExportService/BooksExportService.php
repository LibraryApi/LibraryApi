<?php

namespace App\Services\ExportService;

use App\Models\Book;
use Illuminate\Database\Eloquent\Collection;

class BooksExportService extends ExportService
{
    public function prepareData(array $exportParams): Collection
    {
        $books = Book::query();

        $reportPeriods = [
            'week' => fn ($query) => $query->where('created_at', '>', now()->subWeek()),
            'day' => fn ($query) => $query->where('created_at', '>', now()->subDay()),
            'month' => fn ($query) => $query->where('created_at', '>', now()->subMonth()),
            'custom' => fn ($query) => $query->whereBetween('created_at', [$exportParams['start'], $exportParams['end']]),
        ];

        if (isset($exportParams['report_period']) && array_key_exists($exportParams['report_period'], $reportPeriods)) {
            $reportPeriod = $exportParams['report_period'];
            $reportPeriods[$reportPeriod]($books);
        }

        $result = $books->get()->map(function ($book) {
            return [
                'id' => $book->id,
                'user' => $book->user->name,
                'author' => $book->author,
                'title' => $book->title,
                'categories' => $this->getCategories($book->categories->pluck('name')->toArray()),
                'date' => $book->created_at,
                // Добавить другие поля при необходимости
            ];
        });

        return new Collection($result);
    }

    public function getCategories($categories)
    {
        return implode(', ', $categories);
    }
}
