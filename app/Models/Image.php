<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    use HasFactory;

    protected $fillable = ['imagetable_id', 'image_type', 'path'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function post()
    {
        return $this->belongsTo(Post::class, 'imagetable_id');
    }

    public function book()
    {
        return $this->belongsTo(Book::class, 'imagetable_id');
    }
}
