<?php

use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ForumCommentController;
use App\Http\Controllers\ForumController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductInventoryController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Public
    Route::prefix('auth')->group(function () {
        Route::post('login', [AuthController::class, 'login'])->middleware('throttle:5,1');
        Route::post('register', [AuthController::class, 'register'])->middleware('throttle:5,1');
    });

    Route::get('catalog/products', [CatalogController::class, 'products']);
    Route::get('catalog/categories', [CatalogController::class, 'categories']);

    // Authenticated. Staff modules also require the matching seeded permission.
    Route::middleware(['api', 'auth:api'])->group(function () {

        Route::prefix('auth')->group(function () {
            Route::post('logout', [AuthController::class, 'logout']);
            Route::post('refresh', [AuthController::class, 'refresh']);
        });

        Route::prefix('profile')->group(function () {
            Route::get('/', [ProfileController::class, 'show']);
            Route::put('/', [ProfileController::class, 'update']);
            Route::put('/password', [ProfileController::class, 'updatePassword']);
        });

        // Any authenticated account, including a customer with no role.
        Route::get('users/me', [AuthController::class, 'me']);
        Route::post('checkout', [CheckoutController::class, 'store']);

        Route::middleware('permission:view users')->group(function () {
            Route::get('users/total', [UserController::class, 'total']);
            Route::get('users', [UserController::class, 'index']);
        });
        Route::post('users', [UserController::class, 'store'])->middleware('permission:create users');
        Route::patch('users/{user}/role', [UserController::class, 'update_role'])->middleware('permission:edit users');
        Route::patch('users/{user}/status', [UserController::class, 'update_status'])->middleware('permission:edit users');
        Route::delete('users/{user}', [UserController::class, 'destroy'])->middleware('permission:delete users');

        Route::middleware('permission:view customers')->group(function () {
            Route::get('customers/total', [CustomerController::class, 'total']);
            Route::get('customers', [CustomerController::class, 'index']);
            Route::get('customers/{user}', [CustomerController::class, 'show']);
        });

        Route::middleware('permission:view roles-permission')->group(function () {
            Route::get('roles/permissions', [RoleController::class, 'permissions']);
            Route::get('roles', [RoleController::class, 'index']);
        });
        Route::post('roles', [RoleController::class, 'store'])->middleware('permission:create roles-permission');
        Route::put('roles/{role}', [RoleController::class, 'update'])->middleware('permission:edit roles-permission');
        Route::delete('roles/{role}', [RoleController::class, 'destroy'])->middleware('permission:delete roles-permission');

        Route::middleware('permission:view products')->group(function () {
            Route::get('products', [ProductController::class, 'index']);
            Route::get('products/{product}', [ProductController::class, 'show']);
            Route::get('categories', [CategoryController::class, 'index']);
            Route::prefix('inventory')->group(function () {
                Route::get('total', [ProductInventoryController::class, 'total']);
                Route::get('sales', [ProductInventoryController::class, 'sales']);
                Route::get('stocks', [ProductInventoryController::class, 'stocks']);
                Route::get('unavailable', [ProductInventoryController::class, 'unavailable']);
            });
        });
        Route::post('products', [ProductController::class, 'store'])->middleware('permission:create products');
        Route::match(['put', 'patch'], 'products/{product}', [ProductController::class, 'update'])->middleware('permission:edit products');
        Route::delete('products/{product}', [ProductController::class, 'destroy'])->middleware('permission:delete products');

        // No forum permission is seeded. Keep the API off customer tokens.
        Route::middleware('role:'.User::ROLE_SUPER_ADMIN)->group(function () {
            Route::apiResource('forums', ForumController::class);
            Route::apiResource('forums.comments', ForumCommentController::class);
        });

        Route::middleware('permission:view orders')->group(function () {
            Route::get('orders/total', [OrderController::class, 'total']);
            Route::get('orders/export', [OrderController::class, 'export']);
            Route::get('orders', [OrderController::class, 'index']);
        });
        Route::post('orders', [OrderController::class, 'store'])->middleware('permission:create orders');
        Route::match(['put', 'patch'], 'orders/{order}', [OrderController::class, 'update'])->middleware('permission:edit orders');
        Route::delete('orders/{order}', [OrderController::class, 'destroy'])->middleware('permission:delete orders');

        Route::prefix('analytics')->middleware('permission:view analytics')->group(function () {
            Route::get('revenue', [AnalyticsController::class, 'revenue']);
            Route::get('categories', [AnalyticsController::class, 'categories']);
            Route::get('kpi', [AnalyticsController::class, 'kpi']);
        });
    });
});
