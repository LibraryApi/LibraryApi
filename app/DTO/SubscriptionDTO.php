<?php

namespace App\DTO;

class SubscriptionDTO
{
    public string $name;
    public int $price;
    public int $duration;
    public string $access_level;

    public function __construct(array $data)
    {
        $this->name = $data['name'];
        $this->price = $data['price'];
        $this->duration = $data['duration'];
        $this->access_level = $data['access_level'];
    }
}