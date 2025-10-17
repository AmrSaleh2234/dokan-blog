<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Post\Http\Controllers\PostController;

Route::prefix('posts')->group(function () {
    Route::get('/', [PostController::class, 'index']);
    
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/trashed', [PostController::class, 'trashed']);
        Route::post('/{id}/restore', [PostController::class, 'restore']);
        Route::post('/', [PostController::class, 'store']);
        Route::put('/{id}', [PostController::class, 'update']);
        Route::delete('/{id}', [PostController::class, 'destroy']);
    });
    
    Route::get('/{id}', [PostController::class, 'show']);
});

Route::get('categories/{categoryId}/posts', [PostController::class, 'byCategory']);
