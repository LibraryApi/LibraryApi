<?php
namespace App\Services\Application\Auth;

use App\DTO\Auth\LoginUserDTO;
use App\DTO\Auth\RegisterUserDTO;
use App\Models\User;
use App\Services\RoleService;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function __construct(
        private RoleService $roleService
    ) {}

    public function register(RegisterUserDTO $data): bool
    {

        $user = User::create([
            'name' => $data->name,
            'email' => $data->email,
            'password' => Hash::make($data->password),
        ]);
        
        $this->roleService->assignRoleToUser($user, User::ROLE_READER);

        $token = $user->createToken('auth_token')->plainTextToken;

        return true;
    }

    public function login(LoginUserDTO $data): ?array
    {
        if (!auth()->attempt(['email' => $data->email, 'password' => $data->password])) {
            return null;
        }

        $user = User::where('email', $data->email)->first();
        $token = $user->createToken('auth_token')->plainTextToken;

        return compact('user', 'token');
    }

    public function logout(): bool
    {
        $user = auth()->user();

        if ($user) {
            $user->currentAccessToken()->delete();
            return true;
        }

        return false;
    }

    public function refreshToken(): ?string
    {
        $user = auth()->user();

        if (!$user) {
            return null;
        }

        $user->currentAccessToken()->delete();
        return $user->createToken('auth_token')->plainTextToken;
    }

    public function getUser(): ?User
    {
        return auth()->user();
    }
}
