<?php

use App\Http\Controllers\Api\FoodController;
use App\Http\Controllers\Api\BrandController;
use App\Http\Controllers\Api\FoodImportExportController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\FoodLogController;
use App\Http\Controllers\Api\ManufacturerController;
use Illuminate\Support\Facades\Route;

# AUTH
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout']);

# PUBLIC (Visitor + User + Admin)
Route::get('/foods/check-barcode', [FoodController::class, 'checkBarcode']);
Route::get('/foods', [FoodController::class, 'index']);
Route::get('/foods/{id}', [FoodController::class, 'show']);
Route::get('/brands', [BrandController::class, 'index']);
Route::get('/manufacturers', [ManufacturerController::class, 'index']);
Route::get('/main-categories', [CategoryController::class, 'mainCategories']);
Route::get('/sub-categories', [CategoryController::class, 'subCategories']);

# LOGGED IN USERS & ADMIN
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    
    Route::post('/foods', [FoodController::class, 'store']);
    Route::put('/foods/{id}', [FoodController::class, 'update']);
    Route::delete('/foods/{food}', [FoodController::class, 'destroy']);

    Route::get('/food-logs', [FoodLogController::class, 'index']);
    Route::get('/food-logs/history', [FoodLogController::class, 'history']);
    Route::post('/food-logs', [FoodLogController::class, 'store']);
    Route::put('/food-logs/{id}', [FoodLogController::class, 'update']);
    Route::delete('/food-logs/{id}', [FoodLogController::class, 'destroy']);

    # ADMIN ONLY
    Route::middleware('is_admin')->group(function () {
        Route::post('/brands', [BrandController::class, 'store']);
        
        # IMPORT/EXPORT 
        Route::get('/export/foods', [FoodImportExportController::class, 'export']);
        Route::post('/import/foods', [FoodImportExportController::class, 'import']);

        # USERS
        Route::get('/users', [UserController::class, 'index']);
        Route::post('/users', [UserController::class, 'store']);
        Route::get('/users/{id}', [UserController::class, 'show']);
        Route::put('/users/{id}', [UserController::class, 'update']);
        Route::delete('/users/{id}', [UserController::class, 'destroy']);
    });
});