<?php

namespace App\DTO\Auth;


use Spatie\DataTransferObject\DataTransferObject;

class RegisterUserDTO
{
    public string $name;
    public string $email;
    public string $password;

    public static function fromRequest(array $data): self
    {
        $dto = new self();
        $dto->name = $data['name'];
        $dto->email = $data['email'];
        $dto->password = $data['password'];
        return $dto;
    }
}