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
            'name' => 'required|string|max:255',
            'calories_p100' => 'required|integer',
            'fat_p100' => 'required|numeric',
            'sat_fat_p100' => 'required|numeric',
            'carbs_p100' => 'required|numeric',
            'sugar_p100' => 'required|numeric',
            'fiber_p100' => 'required|numeric',
            'protein_p100' => 'required|numeric',
            'salt_p100' => 'required|numeric',
            'base_unit' => 'required|string',
            'quantity_total' => 'required|integer',
            'source' => 'required|string',
        ]);

        $food = Food::create($validated);

        return response()->json([
            'message' => 'Food created successfully.',
            'food' => $food
        ], 201);
    }
}