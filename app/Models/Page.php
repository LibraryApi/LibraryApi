<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $fillable = [
        'title',
        'content',
        'number',
    ];

    public function chapter()
    {
        return $this->belongsTo(Chapter::class);
    }
}
