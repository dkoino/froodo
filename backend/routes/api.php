<?php

use App\Http\Controllers\Api\FoodController;
use App\Http\Controllers\Api\BrandController;
use App\Http\Controllers\Api\FoodImportExportController;
use App\Http\Controllers\Api\CategoryController;
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