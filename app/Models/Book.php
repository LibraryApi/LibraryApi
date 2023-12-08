<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'author', 'description', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
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
