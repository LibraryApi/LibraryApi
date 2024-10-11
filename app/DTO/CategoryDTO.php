<?php

namespace App\DTO;

class CategoryDTO
{
    public string $name;
    public ?string $description;

    public function __construct(array $data)
    {
        $this->name = $data['name'];
        $this->description = $data['description'] ?? null;
    }
}
