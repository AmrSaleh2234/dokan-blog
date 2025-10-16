<?php

return [
    App\Providers\AppServiceProvider::class,
    Modules\Auth\Providers\AuthServiceProvider::class,
    Modules\Category\Providers\CategoryServiceProvider::class,
    Modules\Post\Providers\PostServiceProvider::class,
    Modules\Comment\Providers\CommentServiceProvider::class,
    Modules\Audit\Providers\AuditServiceProvider::class,
];
