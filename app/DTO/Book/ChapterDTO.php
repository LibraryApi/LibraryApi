<?php

namespace App\DTO\Book;

class ChapterDTO
{
    public string $title;
    public ?string $content;
    public ?int $number;
    public ?int $duration;
    public ?array $characters;
    public ?array $images;

    public function __construct(array $data)
    {
        $this->title = $data['title'];
        $this->content = $data['content'] ?? null;
        $this->number = $data['number'] ?? null;
        $this->duration = $data['duration'] ?? null;
        $this->characters = $data['characters'] ?? [];
        $this->images = $data['images'] ?? [];
    }
}
