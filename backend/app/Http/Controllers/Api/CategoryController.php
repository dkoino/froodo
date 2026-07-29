<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MainCategory;
use App\Models\SubCategory;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function mainCategories(Request $request)
    {
        $query = MainCategory::query();
        if ($search = $request->query('search')) {
            $query->where('name', 'like', "%{$search}%");
        }
        return response()->json($query->limit(20)->get());
    }

    public function subCategories(Request $request)
    {
        $query = SubCategory::query();
        if ($search = $request->query('search')) {
            $query->where('name', 'like', "%{$search}%");
        }
        if ($mainCatId = $request->query('main_category_id')) {
            $query->where('main_category_id', $mainCatId);
        }
        return response()->json($query->limit(20)->get());
    }
}
