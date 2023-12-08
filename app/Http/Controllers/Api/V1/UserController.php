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

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = UserResource::collection(User::get());
        return response()->json($users);
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::with('posts')->findOrFail($id);
        $this->authorize('view', $user);

        $userResource = new UserResource($user);
        return response()->json($userResource);
    }


    public function update(UpdateUserRequest $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $this->authorize('update', $user);

        $user->update([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => $request->has('password') ? Hash::make($request->input('password')) : $user->password,
        ]);

        return response()->json(['message' => 'User updated successfully']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $this->authorize('delete', $user);

        Comment::where('user_id', $id)->delete();
        Post::where('user_id', $id)->delete();
        $user->delete();

        return response()->json(['message' => 'User deleted successfully'], 204);
    }
}
