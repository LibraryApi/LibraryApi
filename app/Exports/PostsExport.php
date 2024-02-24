<?php

namespace App\Exports;

use App\Models\Post;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;

class PostsExport implements FromCollection
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

        if(isset($data['post_id'])){
            $post = Post::find($data['post_id']);
            return new Collection([$post]);
        }
        return Post::all();
    }
}
