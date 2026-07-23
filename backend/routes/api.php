<?php

use App\Http\Controllers\Api\FoodController;
use App\Http\Controllers\Api\BrandController;
use App\Http\Controllers\Api\FoodImportExportController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\FoodLogController;
use Illuminate\Support\Facades\Route;

Route::get('/foods', [FoodController::class, 'index']);
Route::post('/foods', [FoodController::class, 'store']);
Route::get('/foods/{id}', [FoodController::class, 'show']);
Route::put('/foods/{id}', [FoodController::class, 'update']);
Route::delete('/foods/{food}', [FoodController::class, 'destroy']);

Route::get('/brands', [BrandController::class, 'index']);
Route::post('/brands', [BrandController::class, 'store']);

Route::get('/main-categories', [CategoryController::class, 'mainCategories']);
Route::get('/sub-categories', [CategoryController::class, 'subCategories']);

# IMPORT/EXPORT 
Route::get('/export/foods', [FoodImportExportController::class, 'export']);
Route::post('/import/foods', [FoodImportExportController::class, 'import']);

# USERS
Route::get('/users', [UserController::class, 'index']);
Route::post('/users', [UserController::class, 'store']);
Route::get('/users/{id}', [UserController::class, 'show']);
Route::put('/users/{id}', [UserController::class, 'update']);
Route::delete('/users/{id}', [UserController::class, 'destroy']);

# AUTH
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);
Route::get('/me', [AuthController::class, 'me'])->middleware('auth:sanctum');

# FOOD LOGS
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/food-logs', [FoodLogController::class, 'index']);
    Route::get('/food-logs/history', [FoodLogController::class, 'history']);
    Route::post('/food-logs', [FoodLogController::class, 'store']);
    Route::put('/food-logs/{id}', [FoodLogController::class, 'update']);
    Route::delete('/food-logs/{id}', [FoodLogController::class, 'destroy']);
});