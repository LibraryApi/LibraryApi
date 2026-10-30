<?php

namespace App\Http\Resources\Comments;

use App\Http\Resources\BookResource;
use App\Http\Resources\Comments\PostResource;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $isLiked = false;
        if ($request->user()) {
            $isLiked = $this->likes()
                ->where('user_id', $request->user()->id)
                ->exists();
        }

        return [
            "id" => $this->id,
            "content" => $this->content,
            "author" => new UserResource($this->whenLoaded('user')),
            'commentable_type' => $this->commentable_type,
            'commentable' => $this->commentableData(),
            'parent_id' => $this->parent_id,
            'likes_count' => (int) ($this->likes_count ?? $this->likes->count()),
            'is_liked' => $isLiked,
            'replies' => self::collection($this->whenLoaded('replies')),
        ];
    }

    protected function commentableData()
    {
        if (!$this->relationLoaded('commentable')) {
            return null;
        }

        $commentable = $this->commentable;

        if ($commentable === null) {
            return null;
        }

        return match (get_class($commentable)) {
            \App\Models\Post::class => new PostResource($commentable),
            \App\Models\Book::class => new BookResource($commentable),
            \App\Models\Chapter::class => new \App\Http\Resources\Books\ChapterResource($commentable),
            default => null,
        };
    }
}
