<?php

use App\Http\Controllers\Api\FoodController;
use App\Http\Controllers\Api\BrandController;
use Illuminate\Support\Facades\Route;


# Foods Routes
Route::get('/foods', [FoodController::class, 'index']);
Route::post('/foods', [FoodController::class, 'store']);

Route::delete('/foods/{food}', [FoodController::class, 'destroy']);

# Brand Routes
Route::get('/brands', [BrandController::class, 'index']);
Route::post('/brands', [BrandController::class, 'store']);