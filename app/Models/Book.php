<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'author',
        'description',
        'user_id',
        'cover_image',
        'author_bio',
        'language',
        'rating',
        'number_of_pages',
        'is_published',
        'comments_count',
        'likes_count',
        'views_count',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function chapters()
    {
        return $this->hasMany(Chapter::class);
    }

    public function getAuthorNameAttribute()
    {
        return $this->user->name;
    }

    public function getPostCountAttribute()
    {
        return $this->posts->count();
    }
}
