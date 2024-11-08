<?php

namespace App\Repositories\Api\V1;

use App\Models\Image;

class ImageRepository
{
    public function findByImageTableIdAndType(int $imagetableId, string $imageType): ?Image
    {
        return Image::where('imagetable_id', $imagetableId)
            ->where('image_type', $imageType)
            ->first();
    }

    public function deleteImage(Image $image): void
    {
        $image->delete();
    }

    public function createImage(array $data): Image
    {
        return Image::create($data);
    }
}
