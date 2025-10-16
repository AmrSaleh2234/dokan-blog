<?php

declare(strict_types=1);

namespace Modules\Post\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Auth\Entities\User;
use Modules\Category\Entities\Category;
use Modules\Post\Entities\Post;

class PostSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $categories = Category::all();

        if ($users->isEmpty() || $categories->isEmpty()) {
            $this->command->warn('Please seed users and categories first.');
            return;
        }

        foreach ($users->take(5) as $user) {
            Post::factory()->count(3)->create([
                'user_id' => $user->id,
                'category_id' => $categories->random()->id,
            ]);
        }
    }
}
