<?php

namespace App\Http\Controllers\Api\V1;

use App\Exports\BooksExport;
use App\Exports\PostsExport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends Controller
{
    public function export(Request $request){

        $entities = $request->input('entities');

        if(isset($entities) & $entities == 'books'){
            return Excel::download(new BooksExport($request), 'books.xlsx');
        }

        if(isset($entities) & $entities == 'posts'){
            return Excel::download(new PostsExport($request), 'posts.xlsx');
        }
    }
}
