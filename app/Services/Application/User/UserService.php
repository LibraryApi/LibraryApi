<?php

namespace App\Services\Application\User;

use App\Models\Book;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserService
{
    public function paginate(int $perPage): LengthAwarePaginator
    {
        return User::query()->paginate($perPage);
    }

    public function find(int $id): ?User
    {
        return User::find($id);
    }

    public function profile(int $id): ?User
    {
        return User::query()
            ->with([
                'posts' => function ($query) {
                    $query->with([
                        'user',
                        'book' => fn ($bookQuery) => $bookQuery->with('categories'),
                    ])
                        ->withCount(['likes', 'comments', 'ratings'])
                        ->withAvg('ratings', 'value')
                        ->latest();
                },
                'likedPosts' => function ($query) {
                    $query->with([
                        'user',
                        'book' => fn ($bookQuery) => $bookQuery->with('categories'),
                    ])
                        ->withCount(['likes', 'comments', 'ratings'])
                        ->withAvg('ratings', 'value')
                        ->latest();
                },
                'books' => fn ($query) => $query->with('categories')->withCount('chapters')->latest(),
                'likedBooks' => fn ($query) => $query->with('categories')->withCount('chapters')->latest(),
                'followers' => fn ($query) => $query->select('users.id', 'users.name', 'users.avatar', 'users.city')
                    ->orderBy('users.name')
                    ->limit(30),
                'following' => fn ($query) => $query->select('users.id', 'users.name', 'users.avatar', 'users.city')
                    ->orderBy('users.name')
                    ->limit(30),
            ])
            ->withCount(['posts', 'books', 'comments', 'followers', 'following'])
            ->find($id);
    }

    public function update(User $user, array $data, ?UploadedFile $avatar, ?UploadedFile $banner): User
    {
        foreach (['name', 'email', 'birth_date', 'city', 'telegram', 'phone', 'github', 'vk', 'habr', 'about'] as $field) {
            if (array_key_exists($field, $data)) {
                $user->{$field} = $data[$field];
            }
        }

        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        if ($avatar) {
            $this->deleteUpload($user->avatar);
            $user->avatar = $this->storeUpload($avatar, 'avatars');
        }

        if ($banner) {
            $this->deleteUpload($user->banner_image);
            $user->banner_image = $this->storeUpload($banner, 'banners');
        }

        $user->save();

        return $user->fresh();
    }

    public function follow(User $viewer, User $target): array
    {
        if ((int) $viewer->id === (int) $target->id) {
            return ['ok' => false, 'status' => 422, 'error' => 'Нельзя подписаться на себя'];
        }

        $viewer->following()->syncWithoutDetaching([$target->id]);

        return $this->followResult($viewer, $target, true);
    }

    public function unfollow(User $viewer, User $target): array
    {
        $viewer->following()->detach($target->id);

        return $this->followResult($viewer, $target, false);
    }

    public function delete(User $user): void
    {
        $this->deleteUpload($user->avatar);
        $this->deleteUpload($user->banner_image);

        Comment::where('user_id', $user->id)->delete();
        Post::where('user_id', $user->id)->delete();
        Book::where('user_id', $user->id)->delete();
        $user->delete();
    }

    private function followResult(User $viewer, User $target, bool $isFollowing): array
    {
        return [
            'ok' => true,
            'data' => [
                'is_following' => $isFollowing,
                'followers_count' => $target->followers()->count(),
                'following_count' => $viewer->following()->count(),
            ],
        ];
    }

    private function storeUpload(UploadedFile $file, string $folder): string
    {
        $directory = public_path('uploads/' . $folder);
        File::ensureDirectoryExists($directory, 0755, true);

        $extension = strtolower($file->getClientOriginalExtension() ?: 'jpg');
        $filename = Str::random(28) . '.' . $extension;
        $file->move($directory, $filename);

        return '/uploads/' . $folder . '/' . $filename;
    }

    private function deleteUpload(?string $path): void
    {
        if (!$path || !str_starts_with($path, '/uploads/')) {
            return;
        }

        $fullPath = public_path(ltrim($path, '/'));

        if (File::exists($fullPath)) {
            File::delete($fullPath);
        }
    }
}
