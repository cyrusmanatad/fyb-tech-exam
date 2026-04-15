<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ForumController;
use App\Http\Controllers\ForumCommentController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductInventoryController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
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

        // Users
        Route::apiResource('users', UserController::class)->only(['index','store', 'destroy']);
        Route::patch('users/{user}/role', [UserController::class, 'update_role']);
        Route::patch('users/{user}/status', [UserController::class, 'update_status']);
        Route::get('users/total', [UserController::class, 'total']);
        Route::get('users/me', [AuthController::class, 'me']);

        // Customers
        Route::get('customers/total', [CustomerController::class, 'total']);
        Route::apiResource('customers', CustomerController::class);
        
        // Roles
        Route::get('roles', [RoleController::class, 'index']);
        Route::post('roles', [RoleController::class, 'store']);
        Route::get('roles/permissions', [RoleController::class, 'permissions']);
        Route::put('roles/{role}', [RoleController::class, 'update']);
        Route::delete('roles/{role}', [RoleController::class, 'destroy']);

        // Products
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

        // Orders
        Route::apiResource('orders', OrderController::class)->only(['index','store','update','destroy']);
        Route::get('orders/total', [OrderController::class,'total']);
        Route::get('orders/export', [OrderController::class,'export']);

        // Analytics
        Route::prefix('analytics')->group(function () {
            Route::get('revenue', [AnalyticsController::class, 'revenue']);
            Route::get('categories', [AnalyticsController::class, 'categories']);
            Route::get('kpi', [AnalyticsController::class, 'kpi']);
        });
    });
});