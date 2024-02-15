<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Post\StorePostRequest;
use App\Http\Requests\Post\UpdatePostRequest;
use App\Http\Resources\PostResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Models\Post;
use App\Models\Book;
use App\Models\User;


class PostController extends Controller
{

    public function index()
    {

        $posts = PostResource::collection(Post::with(["user", "comments"])->paginate(10));
        return response()->json($posts);
    }


    public function store(StorePostRequest $request)
    {
        if ($request->filled('book_id')) {
            $book = Book::find($request->input('book_id'));

            if (!$book) {
                return response()->json(['error' => 'Книга не найдена'], 404);
            }
        }

        $post = new Post([
            'title' => $request->input('title'),
            'content' => $request->input('content'),
            'book_id' => $request->input('book_id'),
            'user_id' => auth()->user()->id,
        ]);

        $this->authorize('create', $post);
        $post->save();

        return response()->json(new PostResource($post), 201);
    }

    public function show(string $id)
    {
        $post = Post::find($id);

        if (!$post) {
            return response()->json(['error' => 'Пост не найдена'], 404);
        }
        $postResource = new PostResource($post);
        return response()->json($postResource);
    }

    public function update(UpdatePostRequest $request, $id)
    {
        $user = auth()->user();
        $post = Post::find($id);

        if (!$post) {
            return response()->json(['error' => 'Пост не найден'], 404);
        }

        if ($request->filled('book_id')) {
            $book = Book::find($request->input('book_id'));

            if (!$book) {
                return response()->json(['error' => 'Книга не найдена'], 404);
            }
        }


        $this->authorize('update', $post);

        $post->update([
            'title' => $request->has('title') ? $request->input('title') : $user->title,
            'content' => $request->has('content') ? $request->input('content') : $user->content,
            'book_id' => $request->has('book_id') ? $request->input('book_id') : $user->book_id,
        ]);

        return new PostResource($post);
    }

    public function destroy($id)
    {
        $post = Post::find($id);

        if (!$post) {
            return response()->json(['error' => 'Пост не найден'], 404);
        }
        $this->authorize('delete', $post);
        $post->delete();

        return response()->json(['message' => 'Пост успешно удален']);
    }
}
