<?php

declare(strict_types=1);

namespace Modules\Post\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Modules\Post\Entities\Post;
use Modules\Post\Policies\PostPolicy;
use Modules\Post\Repositories\PostRepository;
use Modules\Post\Repositories\PostRepositoryInterface;

class PostServiceProvider extends ServiceProvider
{
    protected string $moduleName = 'Post';
    
    protected string $moduleNameLower = 'post';

    public function register(): void
    {
        $this->app->bind(PostRepositoryInterface::class, PostRepository::class);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
        
        $this->app['router']->prefix('api')
            ->middleware('api')
            ->group(__DIR__ . '/../Routes/api.php');
        
        Gate::policy(Post::class, PostPolicy::class);
    }
}
