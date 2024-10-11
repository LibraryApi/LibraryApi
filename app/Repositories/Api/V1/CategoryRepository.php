<?php

namespace App\Repositories\Api\V1;

use App\Models\Category;

class CategoryRepository
{
    public function all()
    {
        return Category::all();
    }

    public function find($categoryId): ?Category
    {
        return Category::find($categoryId);
    }

    public function create(array $data): Category
    {
        return Category::create($data);
    }

    public function update(Category $category, array $data)
    {
        $category->update($data);
    }

    public function delete(Category $category)
    {
        $category->delete();
    }
}
