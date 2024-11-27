<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\DTO\Auth\LoginUserDTO;
use App\DTO\Auth\RegisterUserDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\Application\Auth\AuthService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    private $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        $userDTO = RegisterUserDTO::fromRequest($request->validated());

        $status = $this->authService->register($userDTO);

        if (!$status) {
            return response()->json(['message' => __('auth/auth.registration.failed')], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json(['message' => __('auth/auth.register_success')], Response::HTTP_CREATED);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $userDTO = LoginUserDTO::fromRequest($request->validated());
        $result = $this->authService->login($userDTO);

        if (!$result) {
            return response()->json(['message' => __('auth/auth.invalid_credentials')], Response::HTTP_UNAUTHORIZED);
        }

        return response()->json([
            'user' => $result['user']->only('name', 'email'),
            'token' => $result['token'],
            'message' => __('auth/auth.login_success'),
        ], Response::HTTP_OK);
    }

    public function logout(): JsonResponse
    {
        $this->authService->logout();

        return response()->json(['message' => __('auth/auth.logout_success')], Response::HTTP_OK);
    }

    public function getUser(): JsonResponse
    {
        $user = $this->authService->getUser();

        if (!$user) {
            return response()->json(['message' => __('auth/auth.user_not_found')], Response::HTTP_NOT_FOUND);
        }

        return response()->json($user, Response::HTTP_OK);
    }

    public function refreshToken(): JsonResponse
    {
        $token = $this->authService->refreshToken();

        if (!$token) {
            return response()->json(['message' => __('auth/auth.user_not_found')], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => now()->addMinutes(config('sanctum.expiration'))->diffInSeconds(),
        ], Response::HTTP_OK);
    }
}
