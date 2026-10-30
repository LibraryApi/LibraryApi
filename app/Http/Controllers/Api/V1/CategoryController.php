<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Category\StoreCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use App\Http\Resources\CategoryCollection;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\ErrorResource;
use App\Http\Resources\MessageResource;
use App\Models\Category;
use App\Services\Application\Category\CategoryService;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    public function __construct(private readonly CategoryService $categoryService)
    {
    }

    public function index(): JsonResponse
    {
        $categories = $this->categoryService->all();

        if ($categories->isEmpty()) {
            return response()->json(new ErrorResource('Категорий не найдено'), 404);
        }

        return response()->json(new CategoryCollection($categories));
    }

    public function store(StoreCategoryRequest $request): JsonResponse
    {
        $this->authorize('create', Category::class);

        return response()->json(
            new CategoryResource($this->categoryService->create($request->validated())),
            201
        );
    }

    public function show(int $categoryId): JsonResponse
    {
        $category = $this->categoryService->find($categoryId);

        if (!$category) {
            return response()->json(new ErrorResource('Категория не найдена'), 404);
        }

        return response()->json(new CategoryResource($category));
    }

    public function update(UpdateCategoryRequest $request, int $categoryId): JsonResponse
    {
        $category = $this->categoryService->find($categoryId);

        if (!$category) {
            return response()->json(new ErrorResource('Категория не найдена'), 404);
        }

        $this->authorize('update', $category);

        return response()->json(new CategoryResource(
            $this->categoryService->update($category, $request->validated())
        ));
    }

    public function destroy(int $categoryId): JsonResponse
    {
        $category = $this->categoryService->find($categoryId);

        if (!$category) {
            return response()->json(new ErrorResource('Категория не найдена'), 404);
        }

        $this->authorize('delete', $category);
        $this->categoryService->delete($category);

        return response()->json(new MessageResource('Категория успешно удалена'));
    }
}
