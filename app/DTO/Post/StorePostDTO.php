<?php

namespace App\DTO\Post;

class StorePostDTO
{
    public string $title;
    public string $content;
    public ?int $book_id;

    public static function fromRequest(array $data): self
    {
        $dto = new self();
        $dto->title = $data['title'];
        $dto->content = $data['content'];
        $dto->book_id = $data['book_id'] ?? null;
        return $dto;
    }
}
