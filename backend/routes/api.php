<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'auth'
], function ($router) {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
});

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
})->middleware('auth:api');

Route::group([
    'middleware' => 'api',
    'prefix' => 'users'
], function ($router) {
    Route::get('me', [UserController::class, 'me']);
})->middleware('auth:api');

Route::resource('products', App\Http\Controllers\ProductController::class)->middleware('auth:api');
Route::resource('forums', App\Http\Controllers\ForumController::class)->middleware('auth:api');
Route::resource('forums/{forum}/comments', App\Http\Controllers\ForumCommentController::class)->middleware('auth:api');