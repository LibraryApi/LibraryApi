<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Post;
use App\Http\Resources\UserResource;
use App\Http\Resources\CommentResource;

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
            "user" => new UserResource($this->user),
            //"comments"=> new CommentResource($this->comments),
            "title"=> $this->title,
            "content"=> $this->author,
            "created_at" => $this->created_at,
        ];
    }
    
}
