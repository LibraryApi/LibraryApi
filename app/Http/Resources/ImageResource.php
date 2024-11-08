<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ImageResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'image_type' => $this->image_type,
            'image' => $this->image,
        ];
    }
}
