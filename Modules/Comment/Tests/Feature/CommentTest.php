<?php

declare(strict_types=1);

namespace Modules\Comment\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Entities\User;
use Modules\Category\Entities\Category;
use Modules\Comment\Entities\Comment;
use Modules\Post\Entities\Post;
use Tests\TestCase;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_add_comment_to_post(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id, 'category_id' => $category->id]);
        $token = $user->createToken('auth_token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson("/api/posts/{$post->id}/comments", [
            'content' => 'This is a great post!',
        ]);

        $response->assertCreated()
            ->assertJsonFragment(['content' => 'This is a great post!']);

        $this->assertDatabaseHas('comments', [
            'content' => 'This is a great post!',
            'post_id' => $post->id,
            'user_id' => $user->id,
        ]);
    }

    public function test_unauthenticated_user_cannot_add_comment(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id, 'category_id' => $category->id]);

        $response = $this->postJson("/api/posts/{$post->id}/comments", [
            'content' => 'This is a great post!',
        ]);

        $response->assertUnauthorized();
    }

    public function test_comment_owner_can_update_comment(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id, 'category_id' => $category->id]);
        $comment = Comment::factory()->create([
            'post_id' => $post->id,
            'user_id' => $user->id,
            'content' => 'Original comment',
        ]);
        $token = $user->createToken('auth_token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->putJson("/api/comments/{$comment->id}", [
            'content' => 'Updated comment',
        ]);

        $response->assertOk()
            ->assertJsonFragment(['content' => 'Updated comment']);
    }

    public function test_non_owner_cannot_update_comment(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $category = Category::factory()->create();
        $post = Post::factory()->create(['user_id' => $owner->id, 'category_id' => $category->id]);
        $comment = Comment::factory()->create([
            'post_id' => $post->id,
            'user_id' => $owner->id,
        ]);
        $token = $otherUser->createToken('auth_token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->putJson("/api/comments/{$comment->id}", [
            'content' => 'Hacked comment',
        ]);

        $response->assertForbidden();
    }

    public function test_comment_owner_can_delete_comment(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id, 'category_id' => $category->id]);
        $comment = Comment::factory()->create([
            'post_id' => $post->id,
            'user_id' => $user->id,
        ]);
        $token = $user->createToken('auth_token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->deleteJson("/api/comments/{$comment->id}");

        $response->assertOk();
        $this->assertSoftDeleted('comments', ['id' => $comment->id]);
    }

    public function test_non_owner_cannot_delete_comment(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $category = Category::factory()->create();
        $post = Post::factory()->create(['user_id' => $owner->id, 'category_id' => $category->id]);
        $comment = Comment::factory()->create([
            'post_id' => $post->id,
            'user_id' => $owner->id,
        ]);
        $token = $otherUser->createToken('auth_token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->deleteJson("/api/comments/{$comment->id}");

        $response->assertForbidden();
    }
}
