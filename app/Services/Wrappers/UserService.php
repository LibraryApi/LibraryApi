<?php

namespace App\Services\Wrappers;

use App\DTO\UserDTO;
use App\Repositories\Api\V1\UserRepository;
use Exception;

class UserService
{
    protected UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function getAllUsers()
    {
        return $this->userRepository->getAllUsers();
    }

    public function getUser(string $id)
    {
        $user = $this->userRepository->getUserById($id);
        if (!$user) {
            throw new Exception('Пользователь не найден');
        }
        return $user;
    }

    public function updateUser(UserDTO $userDTO)
    {
        $user = $this->userRepository->getUserById($userDTO->id);
        if (!$user) {
            throw new Exception('Пользователь не найден');
        }

        return $this->userRepository->updateUser($user, [
            'name' => $userDTO->name ?? $user->name,
            'email' => $userDTO->email ?? $user->email,
            'password' => $userDTO->password ? bcrypt($userDTO->password) : $user->password,
        ]);
    }

    public function deleteUser(string $id)
    {
        $user = $this->userRepository->getUserById($id);
        if (!$user) {
            throw new Exception('Пользователь не найден');
        }

        $this->userRepository->deleteUser($user);
    }
}
