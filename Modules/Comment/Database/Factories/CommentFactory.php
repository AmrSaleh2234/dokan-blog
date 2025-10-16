<?php

declare(strict_types=1);

namespace Modules\Comment\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Auth\Entities\User;
use Modules\Comment\Entities\Comment;
use Modules\Post\Entities\Post;

class CommentFactory extends Factory
{
    protected $model = Comment::class;

    public function definition(): array
    {
        return [
            'content' => fake()->paragraph(),
            'post_id' => Post::factory(),
            'user_id' => User::factory(),
        ];
    }
}
