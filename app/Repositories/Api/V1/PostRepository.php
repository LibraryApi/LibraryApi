<?php

namespace App\Repositories\Api\V1;

use App\DTO\PostDTO;
use App\Models\Post;

class PostRepository
{
    public function allAndSort($perPage, $filters = [])
    {
        $posts = Post::with(["user", "comments", "images"]);

        if ($filters['search']) {
            $posts = $posts->where('title', 'like', '%' . trim($filters['search']) . '%');
        }

        if ($filters['sortName']) {
            $sortOrder = $filters['sortName'] === 'name_asc' ? 'asc' : 'desc';
            $posts = $posts->orderBy('title', $sortOrder);
        }

        if ($filters['sortDate']) {
            $sortOrder = $filters['sortDate'] === 'date_asc' ? 'asc' : 'desc';
            $posts = $posts->orderBy('created_at', $sortOrder);
        }

        return $posts->paginate($perPage);
    }

    public function find($id)
    {
        return Post::find($id);
    }

    public function create(PostDTO $postDTO, $userId)
    {
        return Post::create([
            'title' => $postDTO->title,
            'content' => $postDTO->content,
            'book_id' => $postDTO->bookId,
            'user_id' => $userId,
        ]);
    }

    public function update(Post $post, PostDTO $postDTO)
    {
        // $post->update([
        //     'title' => !empty($postDTO->title) ? $postDTO->title : $post->title,
        //     'content' => !empty($postDTO->content) ? $postDTO->content : $post->content,
        // ]);

        $dataToUpdate = [];

        if (!empty($postDTO->title)) {
            $dataToUpdate['title'] = $postDTO->title;
        }

        if (!empty($postDTO->content)) {
            $dataToUpdate['content'] = $postDTO->content;
        }

        $post->update($dataToUpdate);
    }

    public function delete(Post $post)
    {
        return $post->delete();
    }
}
