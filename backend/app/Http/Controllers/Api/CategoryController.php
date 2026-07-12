<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MainCategory;
use App\Models\SubCategory;

class CategoryController extends Controller
{
    public function mainCategories()
    {
        return response()->json(MainCategory::all());
    }

    public function subCategories()
    {
        return response()->json(SubCategory::all());
    }
}
