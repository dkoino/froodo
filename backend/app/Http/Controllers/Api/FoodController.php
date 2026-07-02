<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Food;
use Illuminate\Http\Request;

class FoodController extends Controller
{
    public function index()
    {
        return response()->json(Food::with(['brand', 'photos'])->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'variant' => 'nullable|string',
            'brand_id' => 'nullable|integer',
            'total_amount' => 'required|numeric',
            'measurement_unit' => 'required|string',
            'portion_label' => 'nullable|string',
            'portion_amount' => 'nullable|numeric',
            'calories_p100' => 'required|numeric',
            'fat_p100' => 'required|numeric',
            'sat_fat_p100' => 'nullable|numeric',
            'carbs_p100' => 'required|numeric',
            'sugar_p100' => 'nullable|numeric',
            'fiber_p100' => 'nullable|numeric',
            'protein_p100' => 'required|numeric',
            'salt_p100' => 'nullable|numeric',
            'price' => 'nullable|numeric',
            'barcode' => 'nullable|string',
            'source_type' => 'required|string',
            'source_url' => 'nullable|url',
            'notes' => 'nullable|string',
        ]);

        $food = Food::create($data);

        if ($request->hasFile('photos')) {
            $types = $request->input('photo_types', []);
            $files = $request->file('photos');

            foreach ($files as $index => $file) {
                if ($file->isValid()) {
                    $path = $file->store('food-photos', 'public');
                    $type = $types[$index] ?? 'general';

                    $food->photos()->create([
                        'file_path' => $path,
                        'type' => $type,
                    ]);
                }
            }
        }

        return response()->json(['message' => 'Lebensmittel erfolgreich gespeichert!', 'food' => $food], 201);
    }

    public function destroy($id)
    {
        $food = \App\Models\Food::findOrFail($id);
        $food->delete();

        return response()->json(null, 204);
    }
    
    public function show($id)
    {
        return response()->json(Food::with(['brand', 'photos'])->findOrFail($id));
    }
}
