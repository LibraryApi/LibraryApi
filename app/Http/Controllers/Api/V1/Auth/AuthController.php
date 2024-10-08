<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\Api\V1\AuthService;

class AuthController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function register(RegisterRequest $request): \Illuminate\Http\JsonResponse
    {
        $success = $this->authService->register($request->validated());

        return response()->json(['success' => $success, 'message' => 'Пользователь успешно зарегистрирован']);
    }

    public function login(LoginRequest $request): \Illuminate\Http\JsonResponse
    {
        $success = $this->authService->login($request->validated());

        if (!$success) {
            return response()->json(['message' => 'Пользователь уже авторизован'], 409);
        }

        return response()->json([
            'success' => $success,
            'message' => 'Пользователь успешно авторизован',
        ]);
    }

    public function logout(): \Illuminate\Http\JsonResponse
    {
        $userId = auth()->id();
        $success = $this->authService->logout($userId);

        if ($success) {
            return response()->json(['message' => 'Вы успешно вышли из аккаунта'], 200);
        }

        return response()->json(['error' => 'Пользователь не найден'], 404);
    }

    public function refreshToken(): \Illuminate\Http\JsonResponse
    {
        $userId = auth()->id();
        $token = $this->authService->refreshToken($userId);

        if (!$token) {
            return response()->json(['error' => 'Пользователь не найден'], 404);
        }

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => now()->addMinutes(config('sanctum.expiration'))->diffInSeconds(),
        ]);
    }
}