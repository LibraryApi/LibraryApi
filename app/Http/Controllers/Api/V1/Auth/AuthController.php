<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
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

    public function register(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:4',
        ]);

        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
        ]);

        $this->roleService->assignRoleToUser($user, USER::ROLE_READER);

        $token = $user->createToken('auth_token')->plainTextToken;
        $success = ["user" => $user->name, "token" => $token];
        return response()->json(['success' => $success, 'message' => 'Пользователь успешно зарегистрирован']);
    }

    public function login(Request $request): \Illuminate\Http\JsonResponse
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (auth()->attempt($credentials)) {
            $user = User::where('email', $request->email)->first();

            if ($user->tokens->where('name', 'auth_token')->first()) {
                return response()->json(['message' => 'Пользователь уже авторизован'], 409);
            }
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

        if ($user) {

            $user->tokens()->delete();

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

        $user->tokens()->delete();
        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->respondWithToken($token);
    }

    /* Принимает токен и форматирует JSON-ответ с данными о токене.
    Включает токен, тип токена, и время его истечения.
    Используется для обработки ответов в методах login и refreshToken. */

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
