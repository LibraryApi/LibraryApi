<?php

namespace App\Services\Application\Export;

use App\Exports\ExselCollectionExport;
use App\Services\WrapperServices\ExportService\BooksExportService;
use App\Services\WrapperServices\ExportService\PostsExportService;

class ExportService
{
    private array $exportClasses = [
        'books' => BooksExportService::class,
        'posts' => PostsExportService::class,
    ];

    public function make(array $params): ?ExselCollectionExport
    {
        $entity = $params['entity'] ?? null;
        $exportClass = $this->exportClasses[$entity] ?? null;

        if (!$exportClass) {
            return null;
        }

        return new ExselCollectionExport($params, new $exportClass());
    }
}
