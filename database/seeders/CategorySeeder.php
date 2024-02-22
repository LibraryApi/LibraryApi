<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Фантастика',
            'Роман',
            'Детектив',
            'Фэнтези',
            'Ужасы',
            'Наука и образование',
            'Поэзия',
            'Приключения',
            'Классика',
            'Драма',
            'Комедия',
            'Бизнес и экономика',
            'Философия',
            'История',
            'Биография',
            'Научная фантастика',
            'Триллер',
            'Детская литература',
            'Психология',
        ];

        $books = Book::all();

        foreach ($books as $book) {
            // Генерируем случайное количество категорий (2-3)
            $numCategories = rand(2, 3);

            // Выбираем случайные категории
            $selectedCategories = collect($categories)->random($numCategories);

            foreach ($selectedCategories as $categoryName) {
                $selectedCategories = collect($categories)->random($numCategories);
                $category = Category::firstOrCreate(['name' => $categoryName]);

                // Проверяем, есть ли уже такая категория у книги
                $book->categories()->attach($category);
            }
        }
    }
}
