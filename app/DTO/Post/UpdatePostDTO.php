<?php

namespace App\DTO\Post;

class UpdatePostDTO
{
    public ?string $title;
    public ?string $content;
    public ?int $book_id;

    public static function fromRequest(array $data): self
    {
        $dto = new self();
        $dto->title = $data['title'] ?? null;
        $dto->content = $data['content'] ?? null;
        $dto->book_id = $data['book_id'] ?? null;
        return $dto;
    }
}
