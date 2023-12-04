<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    /* Принимает токен и форматирует JSON-ответ с данными о токене.
    Включает токен, тип токена, и время его истечения.
    Используется для обработки ответов в методах login и refreshToken. */

    protected function respondWithToken($token)
    {
        $user = auth('sanctum')->user();

        if ($user) {
            return response()->json([
                'access_token' => $token,
                'token_type' => 'Bearer',
                'expires_in' => now()->addMinutes(config('sanctum.expiration'))->diffInSeconds(),
            ]);
        }

        // Обработка случая, если пользователь не найден
        return response()->json(['error' => 'User not found'], 404);
    }
    
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:5',
        ]);

        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;
        $success = ["user" => $user->name, "token" => $token];
        return response()->json(['success' => $success, 'message' => 'Пользователь успешно зарегистрирован']);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (auth()->attempt($credentials)) {
            $user = User::where('email', $request->email)->first();

            // Проверка наличия активной сессии
            /* if ($user->tokens->where('name', 'auth_token')->first()) {
                return response()->json(['message' => 'Пользователь уже авторизован'], 200);
            } */
            // Создание нового токена
            $token = $user->createToken('auth_token')->plainTextToken;

            // Ответ с успешной авторизацией и токеном
            return response()->json([
                'success' => [
                    'name' => $user->name,
                    'token' => $token,
                ],
                'message' => 'Пользователь успешно авторизован'
            ]);
        }

        // Ответ при неудачной аутентификации
        return response()->json(['error' => 'Не удалось авторизовать пользователя. Неверный логин или пароль.'], 401);
    }

    /* Возвращает JSON-ответ с данными аутентифицированного пользователя. */
    public function getUser()
    {
        return response()->json(auth()->user());
    }

    /* Обновляет токен текущего пользователя, генерируя новый токен.
    Использует метод respondWithToken для форматирования JSON-ответа с обновленным токеном. */
    public function refreshToken()
    {
        $user = User::find(auth()->id());

        if (!$user) {
            return response()->json(['error' => 'Пользователь не найден'], 404);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->respondWithToken($token);
    }

    /*  Удаляет все токены текущего пользователя, выходя из системы.
    Возвращает JSON-ответ с успешным выходом из системы. */

    public function logout()
    {
        $user = User::find(auth()->id());

        if ($user) {
            // Удаление всех токенов пользователя
            $user->tokens()->delete();

            return response()->json(['message' => 'Successfully logged out']);
        }

        // Если пользователь не найден, вернуть ошибку
        return response()->json(['error' => 'User not found'], 404);
    }
}
