<?php

declare(strict_types=1);

namespace Modules\Category\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Category\Repositories\CategoryRepository;
use Modules\Category\Repositories\CategoryRepositoryInterface;

class CategoryServiceProvider extends ServiceProvider
{
    protected string $moduleName = 'Category';
    
    protected string $moduleNameLower = 'category';

    public function register(): void
    {
        $this->app->bind(CategoryRepositoryInterface::class, CategoryRepository::class);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
        
        $this->app['router']->prefix('api')
            ->middleware('api')
            ->group(__DIR__ . '/../Routes/api.php');
    }
}
