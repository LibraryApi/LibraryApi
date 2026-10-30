<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Chapter;
use App\Models\Comment;
use App\Models\Like;
use App\Models\Post;
use App\Models\Rating;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CommentApiTest extends TestCase
{
    use RefreshDatabase;

    private function setUpRoles(): void
    {
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'author']);
        Role::create(['name' => 'reader']);
        Role::create(['name' => 'guest']);
    }

    private function createUser(array $roles = []): User
    {
        $user = User::factory()->create();
        foreach ($roles as $role) {
            $user->assignRole($role);
        }
        return $user;
    }

    private function createPost(User $author): Post
    {
        $book = Book::factory()->create(['user_id' => $author->id]);
        return Post::factory()->create([
            'user_id' => $author->id,
            'book_id' => $book->id,
        ]);
    }

    public function test_unauthenticated_user_cannot_create_comment(): void
    {
        $post = Post::factory()->create();
        $response = $this->postJson('/api/v1/comments', [
            'commentable_type' => 'post',
            'commentable_id' => $post->id,
            'content' => 'Test comment',
        ]);
        $response->assertStatus(401);
    }

    public function test_authenticated_user_can_create_comment(): void
    {
        $this->setUpRoles();
        $user = $this->createUser(['reader']);
        $post = $this->createPost($user);

        $response = $this->actingAs($user)->postJson('/api/v1/comments', [
            'commentable_type' => 'post',
            'commentable_id' => $post->id,
            'content' => 'Тестовый комментарий',
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'content' => 'Тестовый комментарий',
                'parent_id' => null,
            ]);

        $this->assertDatabaseHas('comments', [
            'content' => 'Тестовый комментарий',
            'user_id' => $user->id,
            'commentable_id' => $post->id,
            'commentable_type' => 'App\Models\Post',
            'parent_id' => null,
        ]);
    }

    public function test_can_create_reply_to_comment(): void
    {
        $this->setUpRoles();
        $user = $this->createUser(['reader']);
        $post = $this->createPost($user);
        $parentComment = Comment::factory()->create([
            'user_id' => $user->id,
            'commentable_id' => $post->id,
            'commentable_type' => 'App\Models\Post',
            'content' => 'Родительский комментарий',
            'parent_id' => null,
        ]);

        $response = $this->actingAs($user)->postJson('/api/v1/comments', [
            'commentable_type' => 'post',
            'commentable_id' => $post->id,
            'content' => 'Ответ на комментарий',
            'parent_id' => $parentComment->id,
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'content' => 'Ответ на комментарий',
                'parent_id' => $parentComment->id,
            ]);

        $this->assertDatabaseHas('comments', [
            'content' => 'Ответ на комментарий',
            'parent_id' => $parentComment->id,
        ]);
    }

    public function test_can_create_nested_replies_up_to_multiple_levels(): void
    {
        $this->setUpRoles();
        $user = $this->createUser(['reader']);
        $post = $this->createPost($user);

        $level1 = Comment::factory()->create([
            'user_id' => $user->id,
            'commentable_id' => $post->id,
            'commentable_type' => 'App\Models\Post',
            'content' => 'Уровень 1',
            'parent_id' => null,
        ]);
        $level2 = Comment::factory()->create([
            'user_id' => $user->id,
            'commentable_id' => $post->id,
            'commentable_type' => 'App\Models\Post',
            'content' => 'Уровень 2',
            'parent_id' => $level1->id,
        ]);
        $level3 = Comment::factory()->create([
            'user_id' => $user->id,
            'commentable_id' => $post->id,
            'commentable_type' => 'App\Models\Post',
            'content' => 'Уровень 3',
            'parent_id' => $level2->id,
        ]);
        $level4 = Comment::factory()->create([
            'user_id' => $user->id,
            'commentable_id' => $post->id,
            'commentable_type' => 'App\Models\Post',
            'content' => 'Уровень 4',
            'parent_id' => $level3->id,
        ]);
        $level5 = Comment::factory()->create([
            'user_id' => $user->id,
            'commentable_id' => $post->id,
            'commentable_type' => 'App\Models\Post',
            'content' => 'Уровень 5',
            'parent_id' => $level4->id,
        ]);

        $this->assertDatabaseHas('comments', ['id' => $level1->id, 'parent_id' => null]);
        $this->assertDatabaseHas('comments', ['id' => $level2->id, 'parent_id' => $level1->id]);
        $this->assertDatabaseHas('comments', ['id' => $level3->id, 'parent_id' => $level2->id]);
        $this->assertDatabaseHas('comments', ['id' => $level4->id, 'parent_id' => $level3->id]);
        $this->assertDatabaseHas('comments', ['id' => $level5->id, 'parent_id' => $level4->id]);
    }

    public function test_user_can_edit_own_comment(): void
    {
        $this->setUpRoles();
        $user = $this->createUser(['reader']);
        $post = $this->createPost($user);
        $comment = Comment::factory()->create([
            'user_id' => $user->id,
            'commentable_id' => $post->id,
            'commentable_type' => 'App\Models\Post',
            'content' => 'Исходный текст',
        ]);

        $response = $this->actingAs($user)->patchJson("/api/v1/comments/{$comment->id}", [
            'content' => 'Обновлённый текст',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('comments', [
            'id' => $comment->id,
            'content' => 'Обновлённый текст',
        ]);
    }

    public function test_user_cannot_edit_another_users_comment(): void
    {
        $this->setUpRoles();
        $user1 = $this->createUser(['reader']);
        $user2 = $this->createUser(['reader']);
        $post = $this->createPost($user1);
        $comment = Comment::factory()->create([
            'user_id' => $user1->id,
            'commentable_id' => $post->id,
            'commentable_type' => 'App\Models\Post',
            'content' => 'Чужой комментарий',
        ]);

        $response = $this->actingAs($user2)->patchJson("/api/v1/comments/{$comment->id}", [
            'content' => 'Попытка редактирования',
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseHas('comments', [
            'id' => $comment->id,
            'content' => 'Чужой комментарий',
        ]);
    }

    public function test_user_can_delete_own_comment(): void
    {
        $this->setUpRoles();
        $user = $this->createUser(['reader']);
        $post = $this->createPost($user);
        $comment = Comment::factory()->create([
            'user_id' => $user->id,
            'commentable_id' => $post->id,
            'commentable_type' => 'App\Models\Post',
            'content' => 'Удалить меня',
        ]);

        $response = $this->actingAs($user)->deleteJson("/api/v1/comments/{$comment->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('comments', ['id' => $comment->id]);
    }

    public function test_user_cannot_delete_another_users_comment(): void
    {
        $this->setUpRoles();
        $user1 = $this->createUser(['reader']);
        $user2 = $this->createUser(['reader']);
        $post = $this->createPost($user1);
        $comment = Comment::factory()->create([
            'user_id' => $user1->id,
            'commentable_id' => $post->id,
            'commentable_type' => 'App\Models\Post',
            'content' => 'Чужой комментарий',
        ]);

        $response = $this->actingAs($user2)->deleteJson("/api/v1/comments/{$comment->id}");

        $response->assertStatus(403);
        $this->assertDatabaseHas('comments', ['id' => $comment->id]);
    }

    public function test_admin_can_edit_any_comment(): void
    {
        $this->setUpRoles();
        $admin = $this->createUser(['admin']);
        $user = $this->createUser(['reader']);
        $post = $this->createPost($user);
        $comment = Comment::factory()->create([
            'user_id' => $user->id,
            'commentable_id' => $post->id,
            'commentable_type' => 'App\Models\Post',
            'content' => 'Комментарий обычного пользователя',
        ]);

        $response = $this->actingAs($admin)->patchJson("/api/v1/comments/{$comment->id}", [
            'content' => 'Отредактировано админом',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('comments', [
            'id' => $comment->id,
            'content' => 'Отредактировано админом',
        ]);
    }

    public function test_admin_can_delete_any_comment(): void
    {
        $this->setUpRoles();
        $admin = $this->createUser(['admin']);
        $user = $this->createUser(['reader']);
        $post = $this->createPost($user);
        $comment = Comment::factory()->create([
            'user_id' => $user->id,
            'commentable_id' => $post->id,
            'commentable_type' => 'App\Models\Post',
            'content' => 'Комментарий для удаления админом',
        ]);

        $response = $this->actingAs($admin)->deleteJson("/api/v1/comments/{$comment->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('comments', ['id' => $comment->id]);
    }

    public function test_can_fetch_comments_for_post(): void
    {
        $this->setUpRoles();
        $user = $this->createUser(['reader']);
        $post = $this->createPost($user);

        Comment::factory()->count(3)->create([
            'user_id' => $user->id,
            'commentable_id' => $post->id,
            'commentable_type' => 'App\Models\Post',
            'parent_id' => null,
        ]);

        $response = $this->getJson("/api/v1/comments?commentable_type=post&commentable_id={$post->id}");

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data')
            ->assertJsonPath('total', 3);
    }

    public function test_comments_are_paginated_by_default_6_per_page(): void
    {
        $this->setUpRoles();
        $user = $this->createUser(['reader']);
        $post = $this->createPost($user);

        Comment::factory()->count(8)->create([
            'user_id' => $user->id,
            'commentable_id' => $post->id,
            'commentable_type' => 'App\Models\Post',
            'parent_id' => null,
        ]);

        $response = $this->getJson("/api/v1/comments?commentable_type=post&commentable_id={$post->id}");

        $response->assertStatus(200)
            ->assertJsonCount(6, 'data')
            ->assertJsonPath('total', 8)
            ->assertJsonPath('per_page', 6)
            ->assertJsonPath('current_page', 1)
            ->assertJsonPath('last_page', 2);
    }

    public function test_comments_pagination_respects_per_page_parameter(): void
    {
        $this->setUpRoles();
        $user = $this->createUser(['reader']);
        $post = $this->createPost($user);

        Comment::factory()->count(5)->create([
            'user_id' => $user->id,
            'commentable_id' => $post->id,
            'commentable_type' => 'App\Models\Post',
            'parent_id' => null,
        ]);

        $response = $this->getJson("/api/v1/comments?commentable_type=post&commentable_id={$post->id}&per_page=5");

        $response->assertStatus(200)
            ->assertJsonCount(5, 'data')
            ->assertJsonPath('per_page', 5);
    }

    public function test_parent_comments_only_returned_without_replies_at_top_level(): void
    {
        $this->setUpRoles();
        $user = $this->createUser(['reader']);
        $post = $this->createPost($user);

        $parent = Comment::factory()->create([
            'user_id' => $user->id,
            'commentable_id' => $post->id,
            'commentable_type' => 'App\Models\Post',
            'parent_id' => null,
        ]);
        Comment::factory()->count(3)->create([
            'user_id' => $user->id,
            'commentable_id' => $post->id,
            'commentable_type' => 'App\Models\Post',
            'parent_id' => $parent->id,
        ]);

        $response = $this->getJson("/api/v1/comments?commentable_type=post&commentable_id={$post->id}");

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('0.parent_id', null);
    }

    public function test_user_can_like_and_unlike_comment(): void
    {
        $this->setUpRoles();
        $user = $this->createUser(['reader']);
        $post = $this->createPost($user);
        $comment = Comment::factory()->create([
            'user_id' => $user->id,
            'commentable_id' => $post->id,
            'commentable_type' => 'App\Models\Post',
        ]);

        $likeResponse = $this->actingAs($user)->postJson("/api/v1/comments/{$comment->id}/like");
        $likeResponse->assertStatus(201)
            ->assertJson(['liked' => true, 'likes_count' => 1]);

        $unlikeResponse = $this->actingAs($user)->deleteJson("/api/v1/comments/{$comment->id}/like");
        $unlikeResponse->assertStatus(200)
            ->assertJson(['liked' => false, 'likes_count' => 0]);
    }

    private function createComment(User $user, Post $post, ?int $parentId, string $content)
    {
        return $this->actingAs($user)->postJson('/api/v1/comments', [
            'commentable_type' => 'post',
            'commentable_id' => $post->id,
            'content' => $content,
            'parent_id' => $parentId,
        ]);
    }

    public function test_duplicate_like_is_prevented(): void
    {
        $this->setUpRoles();
        $user = $this->createUser(['reader']);
        $post = $this->createPost($user);
        $comment = Comment::factory()->create([
            'user_id' => $user->id,
            'commentable_id' => $post->id,
            'commentable_type' => 'App\Models\Post',
        ]);

        $this->actingAs($user)->postJson("/api/v1/comments/{$comment->id}/like");
        $response = $this->actingAs($user)->postJson("/api/v1/comments/{$comment->id}/like");

        $response->assertStatus(200)
            ->assertJson(['liked' => true, 'likes_count' => 1]);

        $this->assertDatabaseHas('likes', [
            'user_id' => $user->id,
            'likeable_id' => $comment->id,
            'likeable_type' => 'App\Models\Comment',
        ]);
    }
}