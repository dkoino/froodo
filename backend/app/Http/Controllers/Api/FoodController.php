<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Food;
use Illuminate\Http\Request;

class FoodController extends Controller
{
public function store(Request $request)
    {
        $validated = $request->validate([
            'brand_id' => 'nullable|exists:brands,id',
            'name' => 'required|string|max:255',
            'variant' => 'nullable|string|max:255',
            'barcode' => 'nullable|string|max:255',
            'calories_p100' => 'required|integer',
            'fat_p100' => 'required|numeric',
            'sat_fat_p100' => 'required|numeric',
            'carbs_p100' => 'required|numeric',
            'sugar_p100' => 'required|numeric',
            'fiber_p100' => 'required|numeric',
            'protein_p100' => 'required|numeric',
            'salt_p100' => 'required|numeric',
            'base_unit' => 'required|in:g,ml',
            'quantity_total' => 'required|integer',
            'price' => 'nullable|numeric',
            'source' => 'required|string|max:255',
            'notes' => 'nullable|string',
           ]);

        $food = Food::create($validated);

        return response()->json([
            'message' => 'Food created successfully.',
            'food' => $food
        ], 201);
    }
}