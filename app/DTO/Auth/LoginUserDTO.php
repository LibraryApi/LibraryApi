<?php

namespace App\DTO\Auth;

class LoginUserDTO
{
    public string $email;
    public string $password;

    public static function fromRequest(array $data): self
    {
        $dto = new self();
        $dto->email = $data['email'];
        $dto->password = $data['password'];

        return $dto;
    }
}
