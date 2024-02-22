<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Category;
use App\Http\Controllers\Controller;
use App\Http\Requests\Category\StoreCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;

class CategoryController extends Controller
{
    public function index(): \Illuminate\Http\JsonResponse
    {
        $categories = Category::all();
        if($categories->isEmpty()){
            return response()->json(['error' => 'категорий не найдено'], 404);
        }

        $categories = CategoryResource::collection($categories);
        return response()->json($categories);
    }

    public function store(StoreCategoryRequest $request): \Illuminate\Http\JsonResponse
    {
        $data = $request->validated();

        $this->authorize('create', Category::class);
        $category = Category::create($data);

        $category = new CategoryResource($category);
        return response()->json($category, 201);
    }

    public function show($categoryId): \Illuminate\Http\JsonResponse
    {
        $category = Category::find($categoryId);
        if(!$category){
            return response()->json(['error' => 'категория не найдена'], 404);
        }
        $category = new CategoryResource($category);
        return response()->json($category);
    }

    public function update(UpdateCategoryRequest $request, $categoryId): \Illuminate\Http\JsonResponse
    {
        $category = Category::find($categoryId);
        if(!$category){
            return response()->json(['error' => 'категория не найдена'], 404);
        }
        $data = $request->validated();

        $this->authorize('update', $category);
        $category->update($data);

        $category = new CategoryResource($category);
        return response()->json($category, 200);
    }

    public function destroy(string $categoryId): \Illuminate\Http\JsonResponse
    {
        $category = Category::find($categoryId);
        if(!$category){
            return response()->json(['error' => 'категория не найдена'], 404);
        }
        $this->authorize('delete', $category);
        $category->delete();

        return response()->json(['message' => 'Категория успешно удалена'], 200);
    }
}

