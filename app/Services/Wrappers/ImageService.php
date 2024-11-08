<?php

namespace App\Services\Wrappers;

use App\DTO\ImageDTO;
use App\Repositories\Api\V1\ImageRepository;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class ImageService
{
    protected ImageRepository $imageRepository;

    public function __construct(ImageRepository $imageRepository)
    {
        $this->imageRepository = $imageRepository;
    }

    public function storeImage(ImageDTO $imageDTO): array
    {
        $existingImage = $this->imageRepository->findByImageTableIdAndType($imageDTO->imagetableId, $imageDTO->imageType);

        if ($existingImage) {
            Cloudinary::destroy($existingImage->cloudinary_public_id);
            $this->imageRepository->deleteImage($existingImage);
        }

        $uploadResult = Cloudinary::upload($imageDTO->image->getRealPath(), [
            'folder' => 'Library',
        ]);

        $newImage = $this->imageRepository->createImage([
            'imagetable_id' => $imageDTO->imagetableId,
            'image_type' => $imageDTO->imageType,
            'image' => $uploadResult->getSecurePath(),
            'cloudinary_public_id' => $uploadResult->getPublicId(),
        ]);

        return [
            'message' => 'Изображение успешно добавлено',
            'path' => $newImage->image,
        ];
    }

    public function getImagePathById(int $id): ?string
    {
        $image = $this->imageRepository->findByImageTableIdAndType($id);

        return $image ? $image->image : null;
    }
}
