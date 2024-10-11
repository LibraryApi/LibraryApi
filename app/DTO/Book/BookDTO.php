<?php

namespace App\DTO\Book;

class BookDTO
{
    public string $title;
    public string $author;
    public string $description;
    public ?string $cover_image;
    public ?string $author_bio;
    public string $language;
    public ?float $rating;
    public ?int $number_of_pages;
    public bool $is_published;
    public array $categories;

    public function __construct(array $data)
    {
        $this->title = $data['title'];
        $this->author = $data['author'];
        $this->description = $data['description'];
        $this->cover_image = $data['cover_image'] ?? null;
        $this->author_bio = $data['author_bio'] ?? null;
        $this->language = $data['language'] ?? 'ru';
        $this->rating = $data['rating'] ?? null;
        $this->number_of_pages = $data['number_of_pages'] ?? null;
        $this->is_published = $data['is_published'] ?? false;
        $this->categories = $data['categories'] ?? [];
    }
}