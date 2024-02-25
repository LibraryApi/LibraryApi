<?php

namespace App\Exports;

use Illuminate\Database\Eloquent\Collection;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ExselCollectionExport implements FromCollection, ShouldAutoSize, WithMapping, WithHeadings
{
    protected $exportParams;
    protected $exportService;
    public function __construct(array $exportParams, $exportService)
    {
        $this->exportParams = $exportParams;
        $this->exportService = $exportService;
    }

    public function collection(): Collection
    {
        $books = $this->exportService->prepareData($this->exportParams);
        return new Collection($books);
    }

    public function map($book): array
    {
        return [
            $book['id'],
            $book['user'],
            $book['author'],
            $book['title'],
            $book['categories'],
            $book['date']->format('Y-m-d H:i:s'),
        ];
    }

    public function headings(): array
    {
        return [
            'ID книги',
            'Добавил на сайт',
            "Автор",
            'Название книги',
            'Категории',
            'Дата создания',
        ];
    }
}
