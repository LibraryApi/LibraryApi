<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\DTO\Auth\LoginUserDTO;
use App\DTO\Auth\RegisterUserDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\Auth\LoginResource;
use App\Http\Resources\Auth\RegisterResource;
use App\Http\Resources\Auth\TokenResource;
use App\Http\Resources\MessageResource;
use App\Http\Resources\UserResource;
use App\Services\Application\Auth\AuthService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    public function __construct(private readonly AuthService $authService)
    {
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        $result = $this->authService->register(RegisterUserDTO::fromRequest($request->validated()));

        if (!$result) {
            return response()->json(
                new MessageResource(__('auth/auth.registration.failed')),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return response()->json(
            new RegisterResource([...$result, 'message' => __('auth/auth.register_success')]),
            Response::HTTP_CREATED
        );
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->authService->login(LoginUserDTO::fromRequest($request->validated()));

        if (!$result) {
            return response()->json(
                new MessageResource(__('auth/auth.invalid_credentials')),
                Response::HTTP_UNAUTHORIZED
            );
        }

        return response()->json(
            new LoginResource([...$result, 'message' => __('auth/auth.login_success')])
        );
    }

    public function logout(): JsonResponse
    {
        $this->authService->logout();

        return response()->json(new MessageResource(__('auth/auth.logout_success')));
    }

    public function getUser(): JsonResponse
    {
        $user = $this->authService->getUser();

        if (!$user) {
            return response()->json(
                new MessageResource(__('auth/auth.user_not_found')),
                Response::HTTP_NOT_FOUND
            );
        }

        return response()->json(new UserResource($user));
    }

    public function refreshToken(): JsonResponse
    {
        $token = $this->authService->refreshToken();

        if (!$token) {
            return response()->json(
                new MessageResource(__('auth/auth.user_not_found')),
                Response::HTTP_NOT_FOUND
            );
        }

        return response()->json(new TokenResource(['token' => $token]));
    }
}
