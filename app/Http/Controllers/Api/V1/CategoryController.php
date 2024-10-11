<?php

namespace App\Http\Controllers\Api\V1;

use App\DTO\CategoryDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Category\StoreCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Services\Wrappers\CategoryService;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    protected CategoryService $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    public function index(): JsonResponse
    {
        $categories = $this->categoryService->getAllCategories();
        return response()->json(CategoryResource::collection($categories));
    }

    public function store(StoreCategoryRequest $request): JsonResponse
    {
        $categoryDTO = new CategoryDTO($request->validated());
        $category = $this->categoryService->createCategory($categoryDTO);

        return response()->json(new CategoryResource($category), 201);
    }

    public function show($categoryId): JsonResponse
    {
        $category = $this->categoryService->getCategory($categoryId);
        return response()->json(new CategoryResource($category));
    }

    public function update(UpdateCategoryRequest $request, $categoryId): JsonResponse
    {
        $categoryDTO = new CategoryDTO($request->validated());
        $category = $this->categoryService->updateCategory($categoryId, $categoryDTO);

        return response()->json(new CategoryResource($category), 200);
    }

    public function destroy($categoryId): JsonResponse
    {
        $this->categoryService->deleteCategory($categoryId);
        return response()->json(['message' => 'Категория успешно удалена'], 200);
    }
}
