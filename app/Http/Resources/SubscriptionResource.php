<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'price' => $this->price,
            'duration_months' => $this->duration_months,
            'access_level' => $this->access_level,
            'created_at' => $this->created_at,
            //'start_date' => $this->pivot->start_date,
           // 'end_date' => $this->pivot->end_date,
        ];
    }
}
