<?php

namespace App\Repositories\Api\V1\Book;

use App\Models\Book;
use App\Models\Category;
use App\Repositories\Api\V1\CategoryRepository;

class BookRepository
{
    public function getAllBooksAndSort($perPage, $filters = [])
    {
        $query = Book::with('images');

        if (!empty($filters['search'])) {
            $query = $query->where('title', 'like', '%' . $filters['search'] . '%');
        }

        if (isset($filters['sortName']) && $filters['sortName']) {
            $sortOrder = $filters['sortName'] === 'name_asc' ? 'asc' : 'desc';
            $query->orderBy('title', $sortOrder);
        }

        if (isset($filters['sortDate']) && $filters['sortDate']) {
            $sortOrder = $filters['sortDate'] === 'date_asc' ? 'asc' : 'desc';
            $query->orderBy('created_at', $sortOrder);
        }

        if ($filters['category'] !== null && $filters['category'] !== "") {
            $requestCategory = (new CategoryRepository())->find($filters['category']);

            if ($requestCategory) {
                $query->whereHas('categories', function ($categoryQuery) use ($requestCategory) {
                    $categoryQuery->where('name', $requestCategory->name);
                });
            }
        }

        /* $telegram = $this->telegram->createMessageSender('document');
        $telegram->message(["caption" => "отчет за апрель", "document" => Storage::get('/public/file.png'), "filename" => "отчет.doc"])->sendMessage(); */

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
