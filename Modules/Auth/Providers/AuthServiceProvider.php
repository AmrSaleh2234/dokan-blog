<?php

declare(strict_types=1);

namespace Modules\Auth\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Auth\Repositories\UserRepository;
use Modules\Auth\Repositories\UserRepositoryInterface;

class AuthServiceProvider extends ServiceProvider
{
    protected string $moduleName = 'Auth';
    
    protected string $moduleNameLower = 'auth';

    public function register(): void
    {
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
        
        $this->app['router']->prefix('api')
            ->middleware('api')
            ->group(__DIR__ . '/../Routes/api.php');
    }
}
