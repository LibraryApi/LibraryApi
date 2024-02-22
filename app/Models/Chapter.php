<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chapter extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'number',
        'duration',
        'characters',
        'images',
        'comments_count',
        'likes_count',
        'views_count',
    ];

    public function book()
    {
        return $this->belongsTo(Book::class);
    }
}
