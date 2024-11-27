<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Events\UserRegistered;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Services\RoleService;
use App\Services\Application\Auth\AuthService;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    private $roleService;
    private $authService;

    public function __construct(RoleService $roleService, AuthService $authService)
    {
        $this->roleService = $roleService;
        $this->authService = $authService;
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        $data = $this->authService->register($request->validated());

        return response()->json([
            'success' => [
                'user' => $data['user']->name,
                'token' => $data['token'],
            ],
            'message' => __('auth.auth.register_success'),
        ], 201);
    }

    public function login(LoginRequest $request): \Illuminate\Http\JsonResponse
    {
        if (auth()->attempt($request->validated())) {
            $user = User::where('email', $request->email)->first();

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'success' => [
                    'name' => $user->name,
                    'token' => $token,
                ],
                'message' => 'Пользователь успешно авторизован'
            ]);
        }

        return response()->json(['error' => 'Не удалось аутентифицировать пользователя. Неверный логин или пароль.'], 401);
    }

    public function logout(): \Illuminate\Http\JsonResponse
    {
        $user = User::find(auth()->id());

        $token = request()->bearerToken();

        if ($token) {
            auth()->user()->tokens->where('id', auth()->user()->currentAccessToken()->id)->each(function ($token) {
                $token->delete();
            });

            return response()->json(['message' => 'Вы успешно вышли из аккаунта'], 200);
        }
        return response()->json(['error' => 'Пользователь не найден'], 404);
    }

    public function getUser(): \Illuminate\Http\JsonResponse
    {
        return response()->json(auth()->user());
    }

    public function refreshToken(): \Illuminate\Http\JsonResponse
    {
        $user = User::find(auth()->id());

        if (!$user) {
            return response()->json(['error' => 'Пользователь не найден'], 404);
        }

        auth()->user()->tokens->where('id', auth()->user()->currentAccessToken()->id)->each(function ($token) {
            $token->delete();
        });

        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->respondWithToken($token);
    }

    protected function respondWithToken(string $token): \Illuminate\Http\JsonResponse
    {
        $user = auth('sanctum')->user();

        if ($user) {
            return response()->json([
                'access_token' => $token,
                'token_type' => 'Bearer',
                'expires_in' => now()->addMinutes(config('sanctum.expiration'))->diffInSeconds(),
            ]);
        }

        return response()->json(['error' => 'Пользователь не найден'], 404);
    }
}
