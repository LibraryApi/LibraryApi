<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'content',
        'user_id',
        'commentable_id',
        'commentable_type',
        'parent_id'
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function commentable()
    {
        return $this->morphTo();
    }
    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }
    public function replies()
    {
        return $this->hasMany(self::class, 'parent_id')->with(['user', 'replies']);
    }
    public function likes()
    {
        return $this->morphMany(Like::class, 'likeable');
    }
    /* public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    } */
}
