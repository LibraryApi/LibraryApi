<?php

namespace App\Repositories\Api\V1;

use App\Models\User;
use App\Models\Comment;
use App\Models\Post;
use App\Models\Book;

class UserRepository
{
    public function getAllUsers()
    {
        return User::paginate(10);
    }

    public function getUserById(string $id): ?User
    {
        return User::with('posts')->find($id);
    }

    public function updateUser(User $user, array $data): User
    {
        $user->update($data);
        return $user;
    }

    public function deleteUser(User $user): void
    {
        Comment::where('user_id', $user->id)->delete();
        Post::where('user_id', $user->id)->delete();
        Book::where('user_id', $user->id)->delete();
        $user->delete();
    }
}
