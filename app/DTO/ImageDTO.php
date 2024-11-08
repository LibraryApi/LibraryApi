<?php

namespace App\DTO;

class ImageDTO
{
    public int $imagetableId;
    public string $imageType;
    public \Illuminate\Http\UploadedFile $image;

    public function __construct(array $data)
    {
        $this->imagetableId = $data['imagetable_id'];
        $this->imageType = $data['image_type'];
        $this->image = $data['image'];
    }
}
