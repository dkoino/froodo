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
        return response()->json(Food::with(['brand.manufacturer', 'photos', 'mainCategory', 'subCategory'])->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'variant' => 'nullable|string',
            'brand_name' => 'nullable|string',
            'manufacturer_name' => 'nullable|string',
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
            'barcode' => 'nullable|unique:foods,barcode',
            'source_type' => 'required|string',
            'source_url' => 'nullable|url',
            'notes' => 'nullable|string',
            'main_category_name' => 'nullable|string',
            'sub_category_name' => 'nullable|string',
            'meat_type' => 'nullable|string',
            'state' => 'nullable|string',
        ]);

        $mainCategoryId = null;
        if (!empty($data['main_category_name'])) {
            $mainCat = \App\Models\MainCategory::firstOrCreate(['name' => $data['main_category_name']]);
            $mainCategoryId = $mainCat->id;
        }

        $subCategoryId = null;
        if (!empty($data['sub_category_name'])) {
            $subCatData = ['name' => $data['sub_category_name']];
            if ($mainCategoryId) {
                $subCatData['main_category_id'] = $mainCategoryId;
            }
            $subCat = \App\Models\SubCategory::firstOrCreate($subCatData);
            $subCategoryId = $subCat->id;
        }

        $data['main_category_id'] = $mainCategoryId;
        $data['sub_category_id'] = $subCategoryId;
        unset($data['main_category_name']);
        unset($data['sub_category_name']);

        $manufacturerId = null;
        if (!empty($data['manufacturer_name'])) {
            $manufacturer = \App\Models\Manufacturer::firstOrCreate(['name' => $data['manufacturer_name']]);
            $manufacturerId = $manufacturer->id;
        }

        $brandId = null;
        if (!empty($data['brand_name'])) {
            $brand = \App\Models\Brand::firstOrCreate(['name' => $data['brand_name']]);
            if ($manufacturerId && $brand->manufacturer_id !== $manufacturerId) {
                $brand->manufacturer_id = $manufacturerId;
                $brand->save();
            }
            $brandId = $brand->id;
        }
        $data['brand_id'] = $brandId;
        unset($data['brand_name']);
        unset($data['manufacturer_name']);

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
        return response()->json(Food::with(['brand.manufacturer', 'photos', 'mainCategory', 'subCategory'])->findOrFail($id));
    }

    public function update(Request $request, $id)
{
    $food = Food::findOrFail($id);

    $updateData = $request->except([
        'photos', 
        'photo_types', 
        'deleted_photo_ids', 
        'existing_photo_types', 
        '_method',
        'main_category_name',
        'sub_category_name',
        'brand_name',
        'manufacturer_name'
    ]);

    $mainCategoryId = null;
    if ($request->filled('main_category_name')) {
        $mainCat = \App\Models\MainCategory::firstOrCreate(['name' => $request->input('main_category_name')]);
        $mainCategoryId = $mainCat->id;
    }

    $subCategoryId = null;
    if ($request->filled('sub_category_name')) {
        $subCatData = ['name' => $request->input('sub_category_name')];
        if ($mainCategoryId) {
            $subCatData['main_category_id'] = $mainCategoryId;
        }
        $subCat = \App\Models\SubCategory::firstOrCreate($subCatData);
        $subCategoryId = $subCat->id;
    }

    $updateData['main_category_id'] = $mainCategoryId;
    $updateData['sub_category_id'] = $subCategoryId;

    $manufacturerId = null;
    if ($request->filled('manufacturer_name')) {
        $manufacturer = \App\Models\Manufacturer::firstOrCreate(['name' => $request->input('manufacturer_name')]);
        $manufacturerId = $manufacturer->id;
    }

    $brandId = null;
    if ($request->filled('brand_name')) {
        $brand = \App\Models\Brand::firstOrCreate(['name' => $request->input('brand_name')]);
        if ($manufacturerId && $brand->manufacturer_id !== $manufacturerId) {
            $brand->manufacturer_id = $manufacturerId;
            $brand->save();
        }
        $brandId = $brand->id;
    }
    $updateData['brand_id'] = $brandId;

    $food->update($updateData);

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

    return response()->json($food->load(['brand.manufacturer', 'photos', 'mainCategory', 'subCategory']), 200);
    }
}
