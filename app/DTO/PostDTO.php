<?php

namespace App\DTO;

class PostDTO
{
    public string $title;
    public string $content;
    public ?int $bookId;

    public function __construct(array $data)
    {
        $this->title = $data['title'] ?? null;
        $this->content = $data['content'] ?? null;
        $this->bookId = $data['book_id'] ?? null;
    }
}
