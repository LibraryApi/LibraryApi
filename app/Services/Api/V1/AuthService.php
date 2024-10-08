<?php

namespace App\Services\Api\V1;

use App\DTO\Auth\RegisterDTO;
use App\DTO\Auth\LoginDTO;
use Illuminate\Support\Facades\Hash;
use App\Events\UserRegistered;
use App\Models\User;
use App\Repositories\Api\V1\UserRepository;
use App\Services\RoleService;

class AuthService
{
    protected UserRepository $userRepository;
    protected RoleService $roleService;

    public function __construct(UserRepository $userRepository, RoleService $roleService)
    {
        $this->userRepository = $userRepository;
        $this->roleService = $roleService;
    }

    public function register(array $data): array
    {
        $registerDTO = RegisterDTO::fromRequest($data);

        $user = $this->userRepository->create([
            'name' => $registerDTO->name,
            'email' => $registerDTO->email,
            'password' => Hash::make($registerDTO->password),
        ]);

        $this->roleService->assignRoleToUser($user, USER::ROLE_READER);
        //event(new UserRegistered($user));

        $token = $user->createToken('auth_token')->plainTextToken;

        return ['user' => $user->name, 'token' => $token];
    }

    public function login(array $data): ?array
    {
        $loginDTO = LoginDTO::fromRequest($data);

        if (auth()->attempt(['email' => $loginDTO->email, 'password' => $loginDTO->password])) {
            $user = $this->userRepository->findByEmail($loginDTO->email);

            if ($user->tokens->where('name', 'auth_token')->first()) {
                return null;
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            return ['name' => $user->name, 'token' => $token];
        }

        return null;
    }

    public function logout(int $userId): bool
    {
        $user = $this->userRepository->findById($userId);
        
        if ($user) {
            $user->tokens()->delete();
            return true;
        }

        return false;
    }

    public function refreshToken(int $userId): ?string
    {
        $user = $this->userRepository->findById($userId);
        
        if ($user) {
            $user->tokens()->delete();
            return $user->createToken('auth_token')->plainTextToken;
        }

        return null;
    }
}