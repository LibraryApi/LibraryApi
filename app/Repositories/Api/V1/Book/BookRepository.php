<?php

namespace App\Repositories\Api\V1\Book;

use App\Models\Book;

class BookRepository
{
    public function all($perPage, $filters = [])
    {
        $query = Book::query();

        if (isset($filters['author'])) {
            $query->where('author', $filters['author']);
        }

        if (isset($filters['category'])) {
            $query->whereHas('categories', function ($categoryQuery) use ($filters) {
                $categoryQuery->where('name', $filters['category']);
            });
        }

        return $query->paginate($perPage);
    }

    public function find($bookId): ?Book
    {
        return Book::find($bookId);
    }

    public function create(Book $book, array $categories = [])
    {
        $book->save();
        if (!empty($categories)) {
            $book->categories()->attach($categories);
        }
    }

    public function update(Book $book, array $data, array $categories = [])
    {
        $book->update($data);
        if (!empty($categories)) {
            $book->categories()->sync($categories);
        }
    }

    public function delete(Book $book)
    {
        $book->delete();
    }
}
