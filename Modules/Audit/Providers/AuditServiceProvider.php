<?php

declare(strict_types=1);

namespace Modules\Audit\Providers;

use Illuminate\Support\ServiceProvider;

class AuditServiceProvider extends ServiceProvider
{
    protected string $moduleName = 'Audit';

    protected string $moduleNameLower = 'audit';

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/../Routes/api.php');
    }

    public function register(): void
    {
        $this->app->register(RouteServiceProvider::class);
    }
}
