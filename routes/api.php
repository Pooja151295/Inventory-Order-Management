<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\TenantRegistrationController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::post('orders', [OrderController::class, 'store']);
    Route::get('orders', [OrderController::class, 'index']);

    Route::apiResource('products', ProductController::class);

});

Route::post('register-tenant', [TenantRegistrationController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
