<?php

namespace App\Services\Wrappers\Book;

use App\DTO\Book\BookDTO;
use App\Models\Book;
use App\Models\User;
use App\Repositories\Api\V1\Book\BookRepository;
use App\Services\RoleService;
use Exception;
use Illuminate\Support\Facades\Gate;

class BookService
{
    protected BookRepository $bookRepository;
    protected RoleService $roleService;

    public function __construct(BookRepository $bookRepository, RoleService $roleService)
    {
        $this->bookRepository = $bookRepository;
        $this->roleService = $roleService;
    }

    public function getAllBooksAndSort($perPage, $filters)
    {
        return $this->bookRepository->getAllBooksAndSort($perPage, $filters);
    }

    public function getBook(string $bookId): Book
    {
        $book = $this->bookRepository->find($bookId);

        if (!$book) {
            throw new Exception('Книга не найдена');
        }

        return $book;
    }

    public function createBook(BookDTO $bookDTO, $user): Book
    {
        $book = new Book([
            'title' => $bookDTO->title,
            'author' => $bookDTO->author,
            'description' => $bookDTO->description,
            'user_id' => $user->id,
            'cover_image' => $bookDTO->cover_image,
            'author_bio' => $bookDTO->author_bio,
            'language' => $bookDTO->language,
            'rating' => $bookDTO->rating,
            'number_of_pages' => $bookDTO->number_of_pages,
            'is_published' => $bookDTO->is_published,
            'comments_count' => 0,
            'likes_count' => 0,
            'views_count' => 0,
        ]);

        $this->roleService->assignRoleToUser($user, User::ROLE_AUTHOR);

        if (Gate::denies('create', $book)) {
            throw new \Illuminate\Auth\Access\AuthorizationException('У вас нет прав на создание книги');
        }

        $this->bookRepository->create($book, $bookDTO->categories);

        return $book;
    }

    public function updateBook(string $bookId, BookDTO $bookDTO): Book
    {
        $book = $this->bookRepository->find($bookId);

        if (!$book) {
            throw new Exception('Книга не найдена');
        }

        if (Gate::denies('update', $book)) {
            throw new \Illuminate\Auth\Access\AuthorizationException('У вас нет прав на обновление книги');
        }

        $this->bookRepository->update($book, (array)$bookDTO, $bookDTO->categories);
        return $book;
    }

    public function deleteBook(string $bookId): void
    {
        $book = $this->bookRepository->find($bookId);

        if (!$book) {
            throw new Exception('Книга не найдена');
        }

        if (Gate::denies('delete', $book)) {
            throw new \Illuminate\Auth\Access\AuthorizationException('У вас нет прав на обновление книги');
        }

        $this->bookRepository->delete($book);
    }
}
