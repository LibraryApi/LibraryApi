<?php

namespace App\Http\Resources\Comments;

use App\Http\Resources\BookResource;
use App\Http\Resources\Comments\PostResource;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Book;
use App\Models\Post;

class CommentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "content" => $this->content,
            "author" => new UserResource($this->whenLoaded('user')),
            'commentable_type' => $this->commentable_type,
            'commentable_id' => $this->commentable_id,
            'commentable' => $this->commentableData(),
        ];
    }

    protected function commentableData()
    {

        switch ($this->commentable_type) {
            case 'post':
                return new PostResource(Post::findOrFail($this->commentable_id));
            case 'book':
                return new BookResource(Book::findOrFail($this->commentable_id));
            default:
                return null;
        }
    }
}
