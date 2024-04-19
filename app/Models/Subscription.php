<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    public const ACCESS_BASE = 'base';
    public const ACCESS_PREMIUM = 'premium';
    public const DURATION_MONTH = 'month';
    public const DURATION_YEARS = 'years';

    protected $fillable = [
        'name',
        'price',
        'duration',
        'access_level',
        'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
