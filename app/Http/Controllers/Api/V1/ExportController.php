<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Export\ExportRequest;
use App\Http\Resources\ErrorResource;
use App\Services\Application\Export\ExportService;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportController extends Controller
{
    public function __construct(private readonly ExportService $exportService)
    {
    }

    public function export(ExportRequest $request): BinaryFileResponse|\Illuminate\Http\JsonResponse
    {
        $export = $this->exportService->make($request->validated());

        if (!$export) {
            return response()->json(new ErrorResource('Неизвестная сущность'), 400);
        }

        return Excel::download($export, $request->validated('entity') . '.xlsx');
    }
}
