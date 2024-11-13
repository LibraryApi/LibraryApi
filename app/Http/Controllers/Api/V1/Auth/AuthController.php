<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Events\UserRegistered;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Services\RoleService;

class AuthController extends Controller
{
    protected $roleService;

    public function __construct(RoleService $roleService)
    {
        $this->roleService = $roleService;
    }

    public function register(RegisterRequest $request): \Illuminate\Http\JsonResponse
    {
        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
        ]);

        $this->roleService->assignRoleToUser($user, USER::ROLE_READER);

        $token = $user->createToken('auth_token')->plainTextToken;
        //event(new UserRegistered($user));

        $test = $this->respondWithToken($token, $user->name, 'Пользователь успешно зарегистрирован');
        return $test;
    }

    public function login(LoginRequest $request): \Illuminate\Http\JsonResponse
    {

        $t = $request->input();
        if (auth()->attempt($request->validated())) {
            $user = User::where('email', $request->email)->first();

            $token = $user->createToken('auth_token')->plainTextToken;

            return $this->respondWithToken($token, $user->name, 'Пользователь успешно авторизован');
        }

        return response()->json(['error' => 'Не удалось аутентифицировать пользователя. Неверный логин или пароль.'], 401);
    }

    public function logout(): \Illuminate\Http\JsonResponse
    {
        $user = User::find(auth()->id());

        if ($user) {
            auth()->user()->tokens->where('id', auth()->user()->currentAccessToken()->id)->each(function ($token) {
                $token->delete();
            });

            return response()->json(['message' => 'Вы успешно вышли из аккаунта'], 200)
                ->cookie('auth_token', '', 0);
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

    protected function respondWithToken(string $token, string $userName = null, string $message = null): \Illuminate\Http\JsonResponse
    {
        $cookie = cookie('auth_token', $token, config('sanctum.expiration'), null, null, false, true, false, 'Lax');

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => now()->addMinutes(config('sanctum.expiration'))->diffInSeconds(),
            'user' => $userName,
            'message' => $message
        ])->withCookie($cookie);
    }
}
