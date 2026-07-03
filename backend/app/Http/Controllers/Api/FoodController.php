<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Food;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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

    public function update(Request $request, $id)
{
    $food = Food::findOrFail($id);

    $food->update($request->except([
        'photos', 
        'photo_types', 
        'deleted_photo_ids', 
        'existing_photo_types', 
        '_method'
    ]));

    if ($request->has('deleted_photo_ids')) {
        $deletedIds = $request->input('deleted_photo_ids');
        $photosToDelete = $food->photos()->whereIn('id', $deletedIds)->get();
        
        foreach ($photosToDelete as $photo) {
            if (Storage::disk('public')->exists($photo->file_path)) {
                Storage::disk('public')->delete($photo->file_path);
            }
            $photo->delete();
        }
    }

    if ($request->has('existing_photo_types')) {
        foreach ($request->input('existing_photo_types') as $photoId => $newType) {
            $food->photos()->where('id', $photoId)->update(['type' => $newType]);
        }
    }

    if ($request->hasFile('photos')) {
        $types = $request->input('photo_types', []);
        
        foreach ($request->file('photos') as $index => $file) {
            $path = $file->store('food-photos', 'public');
            $type = $types[$index] ?? 'general';
            
            $food->photos()->create([
                'file_path' => $path,
                'type'      => $type,
            ]);
        }
    }

    return response()->json($food->load(['brand', 'photos']), 200);
    }
}
