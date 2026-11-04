<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasRoles, HasApiTokens, HasFactory, Notifiable;

    public const ROLE_GUEST = 'guest';
    public const ROLE_READER = 'reader';
    public const ROLE_AUTHOR = 'author';
    public const ROLE_ADMIN = 'admin';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'banner_image',
        'birth_date',
        'city',
        'telegram',
        'phone',
        'github',
        'vk',
        'habr',
        'about',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'birth_date' => 'date',
        'password' => 'hashed',
    ];

    public function posts()
    {
        return $this->hasMany(Post::class, 'user_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function books()
    {
        return $this->hasMany(Book::class);
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    public function followers()
    {
        return $this->belongsToMany(User::class, 'follows', 'following_id', 'follower_id')
            ->withTimestamps();
    }

    public function following()
    {
        return $this->belongsToMany(User::class, 'follows', 'follower_id', 'following_id')
            ->withTimestamps();
    }

    public function likedPosts()
    {
        return $this->belongsToMany(Post::class, 'likes', 'user_id', 'likeable_id')
            ->where('likes.likeable_type', (new Post())->getMorphClass())
            ->withTimestamps();
    }

    public function likedBooks()
    {
        return $this->belongsToMany(Book::class, 'likes', 'user_id', 'likeable_id')
            ->where('likes.likeable_type', (new Book())->getMorphClass())
            ->withTimestamps();
    }

    public function isFollowing(User $user): bool
    {
        return $this->following()->where('users.id', $user->id)->exists();
    }

    public function getAvatarUrlAttribute(): ?string
    {
        if (!$this->avatar) {
            return null;
        }

        if (str_starts_with($this->avatar, 'http://') || str_starts_with($this->avatar, 'https://')) {
            return $this->avatar;
        }

        return url($this->avatar);
    }

    public function getBannerUrlAttribute(): ?string
    {
        if (!$this->banner_image) {
            return null;
        }

        if (str_starts_with($this->banner_image, 'http://') || str_starts_with($this->banner_image, 'https://')) {
            return $this->banner_image;
        }

        return url($this->banner_image);
    }

    public function subscriptions()
    {
        return $this->belongsToMany(Subscription::class, 'user_subscriptions')
                    ->withPivot('start_date', 'end_date')
                    ->withTimestamps();
    }

    /* public function hasRole(string $role)
    {
        return $this->roles->contains('name', $role);
    } */
}
