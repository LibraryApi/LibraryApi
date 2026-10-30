<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\IndexUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Resources\ErrorResource;
use App\Http\Resources\FollowResource;
use App\Http\Resources\MessageResource;
use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;
use App\Services\Application\User\UserService;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    public function __construct(private readonly UserService $userService)
    {
    }

    public function index(IndexUserRequest $request): JsonResponse
    {
        return response()->json(new UserCollection($this->userService->paginate($request->perPage())));
    }

    public function show(int $id): JsonResponse
    {
        $user = $this->userService->profile($id);

        if (!$user) {
            return response()->json(new ErrorResource('Пользователь не найден'), 404);
        }

        return response()->json(new UserResource($user));
    }

    public function update(UpdateUserRequest $request, int $id): JsonResponse
    {
        $user = $this->userService->find($id);

        if (!$user) {
            return response()->json(new ErrorResource('Пользователь не найден'), 404);
        }

        $this->authorize('update', $user);

        $user = $this->userService->update(
            $user,
            $request->validated(),
            $request->file('avatar'),
            $request->file('banner_image')
        );

        return response()->json(new UserResource($user));
    }

    public function follow(int $id): JsonResponse
    {
        $target = $this->userService->find($id);

        if (!$target) {
            return response()->json(new ErrorResource('Пользователь не найден'), 404);
        }

        $result = $this->userService->follow(auth()->user(), $target);

        if (!$result['ok']) {
            return response()->json(new ErrorResource($result['error']), $result['status']);
        }

        return response()->json(new FollowResource($result['data']));
    }

    public function unfollow(int $id): JsonResponse
    {
        $target = $this->userService->find($id);

        if (!$target) {
            return response()->json(new ErrorResource('Пользователь не найден'), 404);
        }

        return response()->json(new FollowResource(
            $this->userService->unfollow(auth()->user(), $target)['data']
        ));
    }

    public function destroy(int $id): JsonResponse
    {
        $user = $this->userService->find($id);

        if (!$user) {
            return response()->json(new ErrorResource('Пользователь не найден'), 404);
        }

        $this->authorize('delete', $user);
        $this->userService->delete($user);

        return response()->json(new MessageResource('Пользователь успешно удален'));
    }
}
