<?php

namespace App\Services\Wrappers;

use App\DTO\CategoryDTO;
use App\Models\Category;
use App\Repositories\Api\V1\CategoryRepository;
use Exception;
use Illuminate\Support\Facades\Gate;

class CategoryService
{
    protected CategoryRepository $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function getAllCategories()
    {
        $categories = $this->categoryRepository->all();

        if ($categories->isEmpty()) {
            throw new Exception('категорий не найдено');
        }

        return $categories;
    }

    public function getCategory(string $categoryId): Category
    {
        $category = $this->categoryRepository->find($categoryId);

        if (!$category) {
            throw new Exception('категория не найдена');
        }

        return $category;
    }

    public function createCategory(CategoryDTO $categoryDTO): Category
    {
        Gate::authorize('create', Category::class);

        return $this->categoryRepository->create([
            'name' => $categoryDTO->name,
            'description' => $categoryDTO->description,
        ]);
    }

    public function updateCategory(string $categoryId, CategoryDTO $categoryDTO): Category
    {
        $category = $this->getCategory($categoryId);

        Gate::authorize('update', $category);

        $this->categoryRepository->update($category, [
            'name' => $categoryDTO->name,
            'description' => $categoryDTO->description,
        ]);

        return $category;
    }

    public function deleteCategory(string $categoryId): void
    {
        $category = $this->getCategory($categoryId);

        Gate::authorize('delete', $category);

        $this->categoryRepository->delete($category);
    }
}
