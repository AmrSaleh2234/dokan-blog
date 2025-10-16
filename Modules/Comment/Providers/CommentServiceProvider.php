<?php

declare(strict_types=1);

namespace Modules\Comment\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Modules\Comment\Entities\Comment;
use Modules\Comment\Policies\CommentPolicy;
use Modules\Comment\Repositories\CommentRepository;
use Modules\Comment\Repositories\CommentRepositoryInterface;

class CommentServiceProvider extends ServiceProvider
{
    protected string $moduleName = 'Comment';
    
    protected string $moduleNameLower = 'comment';

    public function register(): void
    {
        $this->app->bind(CommentRepositoryInterface::class, CommentRepository::class);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
        
        $this->app['router']->prefix('api')
            ->middleware('api')
            ->group(__DIR__ . '/../Routes/api.php');
        
        Gate::policy(Comment::class, CommentPolicy::class);
    }
}
