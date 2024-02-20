<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Post;
use App\Models\User;
use App\Models\Comment;
use App\Http\Resources\UserResource;
use App\Http\Resources\BookResource;
use App\Http\Resources\Comments\CommentResource;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [  
            "id"=> $this->id,
            "user" => new UserResource(User::findOrFail($this->user_id)),
            'comments' => $this->commentsData(),
            "title" => $this->title,
            "content" => $this->content,
            "book_id" => $this->book_id,
            "created_at" => $this->created_at,
        ];
    }

    protected function commentsData()
    {
        return new CommentResource(Comment::where('commentable_type', 'post')->where('commentable_id', $this->id)->first());
    }
    
}
