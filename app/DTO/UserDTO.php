<?php

namespace App\DTO;

class UserDTO
{
    public string $id;
    public ?string $name;
    public ?string $email;
    public ?string $password;

    public function __construct(array $data)
    {
        $this->id = $data['id'];
        $this->name = $data['name'] ?? null;
        $this->email = $data['email'] ?? null;
        $this->password = $data['password'] ?? null;
    }
}
