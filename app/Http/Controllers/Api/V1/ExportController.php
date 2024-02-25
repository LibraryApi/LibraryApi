<?php

namespace App\Http\Controllers\Api\V1;


use App\Exports\ExselCollectionExport;
use App\Http\Controllers\Controller;
use App\Services\ExportService\BooksExportService;
use App\Services\ExportService\PostsExportService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends Controller
{

    public function export(Request $request)
    {

        $entity = $this->getEntity($request);
        $exportParams = $this->getExportParams($request);
        $exportClasses = [
            'books' => BooksExportService::class,
            'posts' =>  PostsExportService::class,
        ];

        if (isset($entity) && array_key_exists($entity, $exportClasses)) {
            $exportClass = $exportClasses[$entity];
            return Excel::download(new ExselCollectionExport($exportParams,new $exportClass), $entity . '.xlsx');
        }

        return response()->json(['error' => 'неизвестная сущность'], 400);
    }

    public function getEntity(Request $request)
    {
        return $request->input('entity');
    }

    public function getExportParams(Request $request){
        return $request->all();
    }
}
