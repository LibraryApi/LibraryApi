<?php

namespace App\Exports;

use App\Models\Book;
use App\Models\Post;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;

class BooksExport implements FromCollection
{
    protected $request;
    public function __construct(Request $request){
        $this->request = $request;
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $data = $this->request->all();

        if(isset($data['book_id'])){
            $book = Book::find($data['book_id']);
            return new Collection([$book]);
        }
        return Book::all();
    }
}
