<?php

declare(strict_types=1);

namespace Modules\Category\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Category\Database\Seeders\CategorySeeder;
use Modules\Category\Entities\Category;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_nested_category_tree(): void
    {
        $this->seed(CategorySeeder::class);

        $response = $this->getJson('/api/categories/tree');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    ['id', 'name', 'children' => [
                        ['id', 'name', 'children'],
                    ]],
                ],
            ]);

        $this->assertEquals('Technology', $response->json('data.0.name'));
        $this->assertEquals('Mobile', $response->json('data.0.children.0.name'));
        $this->assertEquals('Android', $response->json('data.0.children.0.children.0.name'));
    }

    public function test_it_can_list_all_categories(): void
    {
        Category::factory()->count(5)->create();

        $response = $this->getJson('/api/categories');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    ['id', 'name', 'parent_id'],
                ],
            ])
            ->assertJsonCount(5, 'data');
    }

    public function test_it_can_create_a_category(): void
    {
        $response = $this->postJson('/api/categories', [
            'name' => 'New Category',
        ]);

        $response->assertCreated()
            ->assertJsonFragment(['name' => 'New Category']);

        $this->assertDatabaseHas('categories', ['name' => 'New Category']);
    }

    public function test_it_can_create_a_child_category(): void
    {
        $parent = Category::factory()->create();

        $response = $this->postJson('/api/categories', [
            'name' => 'Child Category',
            'parent_id' => $parent->id,
        ]);

        $response->assertCreated()
            ->assertJsonFragment([
                'name' => 'Child Category',
                'parent_id' => $parent->id,
            ]);
    }

    public function test_it_can_show_a_category(): void
    {
        $category = Category::factory()->create(['name' => 'Test Category']);

        $response = $this->getJson("/api/categories/{$category->id}");

        $response->assertOk()
            ->assertJsonFragment(['name' => 'Test Category']);
    }

    public function test_it_can_update_a_category(): void
    {
        $category = Category::factory()->create();

        $response = $this->putJson("/api/categories/{$category->id}", [
            'name' => 'Updated Category',
        ]);

        $response->assertOk()
            ->assertJsonFragment(['name' => 'Updated Category']);

        $this->assertDatabaseHas('categories', ['name' => 'Updated Category']);
    }

    public function test_it_can_delete_a_category(): void
    {
        $category = Category::factory()->create();

        $response = $this->deleteJson("/api/categories/{$category->id}");

        $response->assertOk();
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }
}
