<?php

namespace App\Services\Application\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthService
{

    public function register(array $data): array
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return compact('user', 'token');
    }
}
