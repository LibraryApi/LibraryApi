<?php

namespace App\Repositories\Api\V1;

use App\DTO\PostDTO;
use App\Models\Post;

class PostRepository
{
    public function all()
    {
        return Post::with(['user', 'comments'])->paginate(10);
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
