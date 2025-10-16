<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Audit\Http\Controllers\AuditController;

Route::middleware('auth:sanctum')->prefix('audits')->group(function () {
    Route::get('/', [AuditController::class, 'index']);
    Route::get('/{id}', [AuditController::class, 'show']);
    Route::get('/model/{model}', [AuditController::class, 'byModel']);
    Route::get('/model/{model}/{id}', [AuditController::class, 'byModelAndId']);
    Route::get('/user/{userId}', [AuditController::class, 'byUser']);
});
