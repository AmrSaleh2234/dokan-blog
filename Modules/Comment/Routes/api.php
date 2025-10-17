<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Comment\Http\Controllers\CommentController;

Route::middleware('auth:sanctum')->group(function () {
    Route::post('posts/{postId}/comments', [CommentController::class, 'store']);
    Route::get('comments/trashed', [CommentController::class, 'trashed']);
    Route::post('comments/{id}/restore', [CommentController::class, 'restore']);
    Route::put('comments/{id}', [CommentController::class, 'update']);
    Route::delete('comments/{id}', [CommentController::class, 'destroy']);
});
