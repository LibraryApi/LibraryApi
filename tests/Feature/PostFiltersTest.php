<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Like;
use App\Models\Post;
use App\Models\Rating;
use App\Models\User;
use App\Models\Book;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class PostFiltersTest extends TestCase
{
    use RefreshDatabase;

    private function createPost(
        string $title,
        string $content,
        string $authorName,
        string $categoryName,
        Carbon $createdAt
    ): Post {
        $user = User::factory()->create(['name' => $authorName]);
        $book = Book::factory()->create(['title' => "Book for {$title}"]);
        $category = Category::firstOrCreate(['name' => $categoryName]);
        $book->categories()->attach($category->id);

        $post = Post::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'title' => $title,
            'content' => $content,
        ]);

        $post->forceFill(['created_at' => $createdAt, 'updated_at' => $createdAt])->saveQuietly();

        return $post->fresh();
    }

    public function test_search_matches_title_content_and_author(): void
    {
        $this->createPost('Волшебство Гарри Поттера', 'История о школе магии', 'Руслан', 'Фэнтези', Carbon::parse('2026-10-28 10:00:00'));
        $this->createPost('Чистый код', 'Практики разработки', 'admin', 'Роман', Carbon::parse('2026-10-28 11:00:00'));

        $this->getJson('/api/v1/posts?search=Гарри')
            ->assertOk()
            ->assertJsonPath('total_posts', 1)
            ->assertJsonPath('posts.0.title', 'Волшебство Гарри Поттера');

        $this->getJson('/api/v1/posts?search=admin')
            ->assertOk()
            ->assertJsonPath('total_posts', 1)
            ->assertJsonPath('posts.0.title', 'Чистый код');
    }

    public function test_category_author_likes_and_rating_filters_work_together(): void
    {
        $first = $this->createPost('Как писать чистый код', 'Чистый код и архитектура', 'admin', 'Роман', Carbon::parse('2026-10-28 10:00:00'));
        $second = $this->createPost('Фантастическое путешествие', 'История о времени', 'author1', 'Фэнтези', Carbon::parse('2026-10-28 11:00:00'));
        $third = $this->createPost('Волшебство Гарри Поттера', 'Школа магии', 'admin', 'Роман', Carbon::parse('2026-10-28 12:00:00'));

        // Two posts by the same account must be found by author id.
        $third->forceFill(['user_id' => $first->user_id])->saveQuietly();
        $third->refresh();

        foreach ([1, 2, 3] as $userId) {
            Like::forceCreate([
                'user_id' => $userId,
                'likeable_id' => $first->id,
                'likeable_type' => Post::class,
            ]);
        }
        Like::forceCreate(['user_id' => 1, 'likeable_id' => $second->id, 'likeable_type' => Post::class]);

        Rating::forceCreate(['user_id' => 1, 'rateable_id' => $first->id, 'rateable_type' => Post::class, 'value' => 5]);
        Rating::forceCreate(['user_id' => 2, 'rateable_id' => $first->id, 'rateable_type' => Post::class, 'value' => 4]);
        Rating::forceCreate(['user_id' => 1, 'rateable_id' => $second->id, 'rateable_type' => Post::class, 'value' => 2]);

        $firstCategory = $first->book->categories()->firstOrFail();
        $firstAuthor = $first->user;

        $this->getJson("/api/v1/posts?category_ids={$firstCategory->id}")
            ->assertOk()
            ->assertJsonPath('total_posts', 2);

        $this->getJson("/api/v1/posts?author_id={$firstAuthor->id}")
            ->assertOk()
            ->assertJsonPath('total_posts', 2);

        $this->getJson('/api/v1/posts?minLikes=2')
            ->assertOk()
            ->assertJsonPath('total_posts', 1)
            ->assertJsonPath('posts.0.title', 'Как писать чистый код')
            ->assertJsonPath('posts.0.likes_count', 3);

        $this->assertSame(2, Rating::where('rateable_id', $first->id)->count());
        $this->assertSame(4.5, (float) $first->ratings()->avg('value'));

        \Illuminate\Support\Facades\DB::listen(function ($query) {
            if (str_contains($query->sql, 'ratings')) {
                fwrite(STDERR, "RATING SQL: {$query->sql}\nBIND: ".json_encode($query->bindings)."\n");
            }
        });

        fwrite(STDERR, 'ROWS: '.json_encode(\Illuminate\Support\Facades\DB::table('ratings')->get())."\n");
        fwrite(STDERR, 'SUB: '.json_encode(\Illuminate\Support\Facades\DB::table('ratings')->select('rateable_id')->where('rateable_type', Post::class)->groupBy('rateable_id')->havingRaw('avg(ratings.value) >= ?', [4])->pluck('rateable_id'))."\n");
        fwrite(STDERR, 'POSTS: '.json_encode(Post::whereIn('posts.id', function ($q) { $q->select('ratings.rateable_id')->from('ratings')->where('ratings.rateable_type', Post::class)->groupBy('ratings.rateable_id')->havingRaw('avg(ratings.value) >= ?', [4]); })->pluck('id'))."\n");

        $this->getJson('/api/v1/posts?minRating=4')
            ->assertOk()
            ->assertJsonPath('total_posts', 1)
            ->assertJsonPath('posts.0.average_rating', 4.5);

        $this->getJson('/api/v1/posts?maxRating=3')
            ->assertOk()
            ->assertJsonPath('total_posts', 1)
            ->assertJsonPath('posts.0.title', 'Фантастическое путешествие');

        $this->getJson('/api/v1/posts?sort=likes_desc')
            ->assertOk()
            ->assertJsonPath('posts.0.title', 'Как писать чистый код');

        $this->getJson(
            "/api/v1/posts?search=чистый&category_ids={$firstCategory->id}&author_id={$firstAuthor->id}"
            . '&startDate=2026-10-28&endDate=2026-10-28&timezone=Europe/Volgograd&sort=likes_desc'
        )
            ->assertOk()
            ->assertJsonPath('total_posts', 1)
            ->assertJsonPath('posts.0.title', 'Как писать чистый код');

        $this->getJson('/api/v1/posts/authors')
            ->assertOk()
            ->assertJsonFragment(['name' => 'admin', 'posts_count' => 2]);
    }

    public function test_invalid_date_range_returns_validation_error(): void
    {
        $this->getJson('/api/v1/posts?startDate=2026-10-29&endDate=2026-10-20')
            ->assertStatus(422)
            ->assertJsonValidationErrors('endDate');
    }
}
