<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\ProductController;
use Illuminate\Support\Facades\Route;

//api version 1
Route::prefix('v1')->group(function () {
    //auth routes
    Route::controller(AuthController::class)->group(function () {
        Route::post('/register', 'register');
        Route::post('/login', 'login')->middleware('throttle:3,1');
    });

    Route::middleware('auth:api')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);

        //category routes
        Route::controller(CategoryController::class)->group(function () {
            Route::get('/categories', 'index');
            Route::post('/category/add', 'store');
            Route::get('/category/view/{id}', 'show');
            Route::put('/category/update/{id}', 'update');
            Route::delete('/category/delete/{id}', 'destroy');
        });

        //product routes
        Route::controller(ProductController::class)->group(function () {
            Route::get('/products', 'index');
            Route::post('/product/add', 'store');
            Route::get('/product/view/{id}', 'show');
            Route::put('/product/update/{id}', 'update');
            Route::delete('/product/delete/{id}', 'destroy');
        });
    });
});
