<?php

namespace App\Http\Controllers\Api\V1;

use App\DTO\ImageDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Image\StoreImageRequest;
use App\Services\Wrappers\ImageService;
use Illuminate\Http\JsonResponse;

class ImageController extends Controller
{
    protected ImageService $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    public function store(StoreImageRequest $request): JsonResponse
    {
        if (!$request->hasFile('image')) {
            return response()->json(['error' => 'Вы пропустили фото'], 400);
        }

        $imageDTO = new ImageDTO($request->validated());
        $result = $this->imageService->storeImage($imageDTO);

        return response()->json($result, 201);
    }

    public function show(int $id): JsonResponse
    {
        $imagePath = $this->imageService->getImagePathById($id);

        if (!$imagePath) {
            return response()->json(['error' => 'Изображение не найдено'], 404);
        }

        return response()->json(['path' => $imagePath], 200);
    }
}
