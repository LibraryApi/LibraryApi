<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use App\Models\User;
use App\Models\Comment;
use App\Models\Post;
use App\Models\Book;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): \Illuminate\Http\JsonResponse
    {
        $users = UserResource::collection(User::paginate(10));
        return response()->json($users);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): \Illuminate\Http\JsonResponse
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['error' => 'Пользователь не найден'], 404);
        }

        $userResource = new UserResource($user);
        return response()->json($userResource);
    }

    public function update(UpdateUserRequest $request, $id): \Illuminate\Http\JsonResponse
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['error' => 'Пользователь не найден'], 404);
        }

        $this->authorize('update', $user);

        $user->update([
            'name' => $request->has('name') ? $request->input('name') : $user->name,
            'email' => $request->has('email') ? $request->input('email') : $user->email,
            'password' => $request->has('password') ? bcrypt($request->input('password')) : $user->password,
        ]);

        return response()->json(['message' => 'Данные пользователя успешно обновлены']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): \Illuminate\Http\JsonResponse
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['error' => 'Пользователь не найден'], 404);
        }

        $this->authorize('delete', $user);

        Comment::where('user_id', $id)->delete();
        Post::where('user_id', $id)->delete();
        Book::where('user_id', $id)->delete();
        $user->delete();

        return response()->json(['message' => 'Пользователь успешно удален'], 200);
    }
}
