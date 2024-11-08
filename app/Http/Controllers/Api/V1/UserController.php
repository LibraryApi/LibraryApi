<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Services\Wrappers\UserService;
use App\DTO\UserDTO;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index(): JsonResponse
    {
        $users = $this->userService->getAllUsers();
        return response()->json(UserResource::collection($users));
    }

    public function show(string $id): JsonResponse
    {
        $user = $this->userService->getUser($id);
        $userResource = new UserResource($user);

        return response()->json($userResource);
    }

    public function update(UpdateUserRequest $request, string $id): JsonResponse
    {
        $userDTO = new UserDTO(array_merge($request->validated(), ['id' => $id]));

        $user = $this->userService->getUser($id);
        $this->authorize('update', $user);

        $updatedUser = $this->userService->updateUser($userDTO);

        return response()->json(['message' => 'Данные пользователя успешно обновлены', 'user' => new UserResource($updatedUser)]);
    }

    public function getUserWithToken(): \Illuminate\Http\JsonResponse
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json(['error' => 'Пользователь не найден или токен недействителен'], 404);
        }

        return response()->json(new UserResource($user), 200);
    }

    public function destroy(string $id): JsonResponse
    {
        $user = $this->userService->getUser($id);
        $this->authorize('delete', $user);

        $this->userService->deleteUser($id);

        return response()->json(['message' => 'Пользователь успешно удален'], 200);
    }
}
