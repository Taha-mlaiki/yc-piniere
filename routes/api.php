<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PlantController;
use App\Http\Controllers\StatisticsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Public plant routes
Route::get('/plants', [PlantController::class, 'index']);
Route::get('/plants/{slug}', [PlantController::class, 'show']);
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{id}', [CategoryController::class, 'show']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);

    // Orders
    Route::get('/orders', [OrderController::class, 'index']);
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);
    Route::put('/orders/{id}/status', [OrderController::class, 'updateStatus']);
    Route::post('/orders/{id}/cancel', [OrderController::class, 'cancel']);

    // Admin/Employee routes
    Route::middleware('can:manage-plants')->group(function () {
        // Plants
        Route::post('/plants', [PlantController::class, 'store']);
        Route::put('/plants/{id}', [PlantController::class, 'update']);
        Route::delete('/plants/{id}', [PlantController::class, 'destroy']);
        Route::post('/plants/{id}/images', [PlantController::class, 'uploadImage']);

        // Categories
        Route::post('/categories', [CategoryController::class, 'store']);
        Route::put('/categories/{id}', [CategoryController::class, 'update']);
        Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);
    });

    // Statistics (admin only)
    Route::middleware('can:view-statistics')->group(function () {
        Route::get('/statistics', [StatisticsController::class, 'index']);
    });
});
