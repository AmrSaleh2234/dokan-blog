<?php

declare(strict_types=1);

namespace Modules\Post\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Entities\User;
use Modules\Category\Entities\Category;
use Modules\Post\Entities\Post;
use Tests\TestCase;

class PostTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_can_list_posts_with_pagination(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        Post::factory()->count(20)->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
        ]);

        $response = $this->getJson('/api/posts');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    ['id', 'title', 'content', 'user_name', 'category_name', 'comments_count'],
                ],
                'meta' => ['current_page', 'last_page', 'per_page', 'total'],
            ]);
    }

    public function test_authenticated_user_can_create_post(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/posts', [
            'title' => 'Test Post',
            'content' => 'This is a test post content.',
            'category_id' => $category->id,
        ]);

        $response->assertCreated()
            ->assertJsonFragment([
                'title' => 'Test Post',
                'content' => 'This is a test post content.',
            ]);

        $this->assertDatabaseHas('posts', [
            'title' => 'Test Post',
            'user_id' => $user->id,
        ]);
    }

    public function test_unauthenticated_user_cannot_create_post(): void
    {
        $category = Category::factory()->create();

        $response = $this->postJson('/api/posts', [
            'title' => 'Test Post',
            'content' => 'This is a test post content.',
            'category_id' => $category->id,
        ]);

        $response->assertUnauthorized();
    }

    public function test_it_can_show_post_with_comments(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $post = Post::factory()->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
        ]);

        $response = $this->getJson("/api/posts/{$post->id}");

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'title',
                    'content',
                    'user',
                    'category',
                    'comments',
                ],
            ]);
    }

    public function test_post_owner_can_update_post(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id, 'category_id' => $category->id]);
        $token = $user->createToken('auth_token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->putJson("/api/posts/{$post->id}", [
            'title' => 'Updated Title',
        ]);

        $response->assertOk()
            ->assertJsonFragment(['title' => 'Updated Title']);
    }

    public function test_non_owner_cannot_update_post(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $category = Category::factory()->create();
        $post = Post::factory()->create(['user_id' => $owner->id, 'category_id' => $category->id]);
        $token = $otherUser->createToken('auth_token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->putJson("/api/posts/{$post->id}", [
            'title' => 'Hacked Title',
        ]);

        $response->assertForbidden();
    }

    public function test_post_owner_can_delete_post(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id, 'category_id' => $category->id]);
        $token = $user->createToken('auth_token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->deleteJson("/api/posts/{$post->id}");

        $response->assertOk();
        $this->assertSoftDeleted('posts', ['id' => $post->id]);
    }

    public function test_it_can_get_posts_by_category_and_descendants(): void
    {
        $user = User::factory()->create();
        
        $tech = Category::create(['name' => 'Technology']);
        $mobile = Category::create(['name' => 'Mobile', 'parent_id' => $tech->id]);
        $android = Category::create(['name' => 'Android', 'parent_id' => $mobile->id]);

        Post::factory()->create(['user_id' => $user->id, 'category_id' => $tech->id]);
        Post::factory()->create(['user_id' => $user->id, 'category_id' => $mobile->id]);
        Post::factory()->create(['user_id' => $user->id, 'category_id' => $android->id]);

        $response = $this->getJson("/api/categories/{$tech->id}/posts");

        $response->assertOk();
        $this->assertEquals(3, count($response->json('data')));
    }
}
