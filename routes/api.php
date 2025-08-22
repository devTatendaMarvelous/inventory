<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\WareHouseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//Version 1
Route::prefix('v1')->group(function () {
    Route::controller(AuthController::class)->group(function () {
        Route::post('/login', 'login');
        Route::post('/register', 'register');
        Route::post('/reset-password', 'resetPassword');
        Route::post('/forgot-password', 'sendPasswordResetLink');
        Route::post('/logout', 'logout');
    });

//    Route::middleware('auth:api')->group(function () {
    Route::resource('categories', CategoryController::class);
    Route::resource('products', ProductController::class);
    Route::resource('warehouses', WareHouseController::class);
//    });


});
