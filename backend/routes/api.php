<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ForumController;
use App\Http\Controllers\ForumCommentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductInventoryController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Public
    Route::prefix('auth')->group(function () {
        Route::post('login', [AuthController::class, 'login'])->middleware('throttle:5,1');
        Route::post('register', [AuthController::class, 'register']);
    });

    // Protected
    Route::middleware(['api', 'auth:api'])->group(function () {

        Route::prefix('auth')->group(function () {
            Route::post('logout', [AuthController::class, 'logout']);
            Route::post('refresh', [AuthController::class, 'refresh']);
        });

        Route::get('users/me', [AuthController::class, 'me']);

        Route::apiResource('products', ProductController::class);
        Route::apiResource('forums', ForumController::class);
        Route::apiResource('forums.comments', ForumCommentController::class);

        Route::get('categories', [CategoryController::class, 'index']);

        Route::prefix('inventory')->group(function () {
            Route::get('total',       [ProductInventoryController::class, 'total']);
            Route::get('sales',       [ProductInventoryController::class, 'sales']);
            Route::get('stocks',      [ProductInventoryController::class, 'stocks']);
            Route::get('unavailable', [ProductInventoryController::class, 'unavailable']);
        });
    });
});