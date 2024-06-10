<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    public const ACCESS_BASE = 'base';
    public const ACCESS_PREMIUM = 'premium';

    protected $fillable = [
        'name',
        'price',
        'duration_months',
        'access_level',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_subscriptions')
            ->withPivot('start_date', 'end_date')
            ->withTimestamps();
    }

    public static function findBySubscriptionData($name, $price, $durationMonths, $accessLevel)
    {
        return self::where('name', $name)
            ->where('price', $price)
            ->where('duration_months', $durationMonths)
            ->where('access_level', $accessLevel)
            ->first();
    }
}
