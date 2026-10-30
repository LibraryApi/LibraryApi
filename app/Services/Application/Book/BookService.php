<?php

namespace App\Services\Application\Book;

use App\Models\Book;
use App\Models\Comment;
use App\Models\User;
use App\Services\WrapperServices\RoleService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class BookService
{
    public function __construct(private readonly RoleService $roleService)
    {
    }

    public function paginate(array $filters, int $perPage): LengthAwarePaginator
    {
        $query = Book::query();

        if (!empty($filters['search'])) {
            $search = trim((string) $filters['search']);
            $query->where(function ($subQuery) use ($search) {
                $subQuery
                    ->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('author', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['author'])) {
            $query->where('author', $filters['author']);
        }

        if (!empty($filters['category'])) {
            $query->whereHas('categories', function ($categoryQuery) use ($filters) {
                $categoryQuery->where('name', $filters['category']);
            });
        }

        if (!empty($filters['category_ids'])) {
            $query->whereHas('categories', function ($categoryQuery) use ($filters) {
                $categoryQuery->whereIn('categories.id', $filters['category_ids']);
            });
        }

        if (!empty($filters['startDate'])) {
            $query->whereDate('created_at', '>=', $filters['startDate']);
        }

        if (!empty($filters['endDate'])) {
            $query->whereDate('created_at', '<=', $filters['endDate']);
        }

        if (!empty($filters['minRating'])) {
            $query->where('rating', '>=', $filters['minRating']);
        }

        if (!empty($filters['tags'])) {
            $tags = is_array($filters['tags']) ? $filters['tags'] : explode(',', (string) $filters['tags']);
            $query->whereHas('categories', function ($categoryQuery) use ($tags) {
                $categoryQuery->whereIn('name', $tags);
            });
        }

        $this->applySorting($query, $filters);

        return $query
            ->with('categories')
            ->withCount(['chapters', 'likes', 'comments'])
            ->withAvg('ratings', 'value')
            ->paginate($perPage);
    }

    public function authors(): Collection
    {
        return Book::query()
            ->select('author')
            ->selectRaw('COUNT(*) as books_count')
            ->whereNotNull('author')
            ->where('author', '!=', '')
            ->groupBy('author')
            ->orderBy('author')
            ->get()
            ->map(static fn ($row) => [
                'name' => $row->author,
                'books_count' => (int) $row->books_count,
            ]);
    }

    public function findBasic(int $bookId): ?Book
    {
        return Book::find($bookId);
    }

    public function find(int $bookId): ?Book
    {
        $book = Book::with([
            'categories',
            'user',
            'chapters' => fn ($query) => $query->orderBy('number', 'asc')->orderBy('id', 'asc'),
        ])
            ->withCount(['chapters', 'likes', 'comments'])
            ->withAvg('ratings', 'value')
            ->find($bookId);

        if (!$book) {
            return null;
        }

        $book->setAttribute('comments_count', Comment::query()
            ->where('commentable_type', Book::class)
            ->where('commentable_id', $book->id)
            ->whereNull('parent_id')
            ->count());

        return $book;
    }

    public function create(array $data, ?UploadedFile $cover): Book
    {
        $book = Book::create([
            'title' => $data['title'],
            'author' => $data['author'],
            'description' => $data['description'],
            'user_id' => auth()->id(),
            'cover_image' => $this->storeUpload($cover),
            'author_bio' => $data['author_bio'] ?? null,
            'language' => $data['language'] ?? 'ru',
            'rating' => $data['rating'] ?? null,
            'number_of_pages' => $data['number_of_pages'] ?? null,
            'is_published' => $data['is_published'] ?? true,
            'comments_count' => 0,
            'likes_count' => 0,
            'views_count' => 0,
        ]);

        if (isset($data['categories'])) {
            $book->categories()->attach($data['categories']);
        }

        $this->roleService->assignRoleToUser(auth()->user(), User::ROLE_AUTHOR);

        return $this->loadForResponse($book);
    }

    public function update(Book $book, array $data, ?UploadedFile $cover): Book
    {
        if ($cover) {
            $this->deleteUpload($book->cover_image);
            $data['cover_image'] = $this->storeUpload($cover);
        } else {
            unset($data['cover_image']);
        }

        $categories = $data['categories'] ?? null;
        unset($data['categories']);

        $book->update($data);

        if ($categories !== null) {
            $book->categories()->sync($categories);
        }

        return $this->loadForResponse($book);
    }

    public function delete(Book $book): void
    {
        $this->deleteUpload($book->cover_image);
        $book->delete();
    }

    private function loadForResponse(Book $book): Book
    {
        $book->load('categories', 'chapters', 'user');
        $book->loadCount(['chapters', 'likes', 'comments']);

        return $book;
    }

    private function applySorting($query, array $filters): void
    {
        switch ($filters['sort'] ?? 'date_desc') {
            case 'date_asc':
                $query->orderBy('created_at');
                break;
            case 'title_asc':
                $query->orderBy('title');
                break;
            case 'title_desc':
                $query->orderByDesc('title');
                break;
            case 'rating_desc':
                $query->orderByDesc('rating');
                break;
            case 'rating_asc':
                $query->orderBy('rating');
                break;
            case 'likes_desc':
                $query->orderByDesc('likes_count');
                break;
            case 'likes_asc':
                $query->orderBy('likes_count');
                break;
            default:
                $query->latest();
                break;
        }
    }

    private function storeUpload(?UploadedFile $file): ?string
    {
        if (!$file) {
            return null;
        }

        $directory = public_path('uploads/covers');
        File::ensureDirectoryExists($directory, 0755, true);

        $extension = strtolower($file->getClientOriginalExtension() ?: 'jpg');
        $filename = Str::random(28) . '.' . $extension;
        $file->move($directory, $filename);

        return '/uploads/covers/' . $filename;
    }

    private function deleteUpload(?string $path): void
    {
        if (!$path || !str_starts_with($path, '/uploads/')) {
            return;
        }

        $fullPath = public_path(ltrim($path, '/'));

        if (File::exists($fullPath)) {
            File::delete($fullPath);
        }
    }
}
