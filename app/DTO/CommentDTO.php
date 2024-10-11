<?php

namespace App\DTO;

class CommentDTO
{
    public string $content;
    public int $user_id;
    public int $commentable_id;
    public string $commentable_type;

    public function __construct(array $data)
    {
        $this->content = $data['content'];
        $this->user_id = $data['user_id'];
        $this->commentable_id = $data['commentable_id'];
        $this->commentable_type = $data['commentable_type'];
    }
}
