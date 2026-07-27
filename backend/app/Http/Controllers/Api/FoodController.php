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
        return response()->json(Food::with(['brand.manufacturer', 'photos', 'mainCategory', 'subCategory', 'creator', 'updater'])->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'variant' => 'nullable|string',
            'brand_name' => 'nullable|string',
            'manufacturer_name' => 'nullable|string',
            'total_amount' => 'required|integer|min:1',
            'measurement_unit' => 'required|string|in:g,ml',
            'portion_label' => 'nullable|string',
            'portion_amount' => 'nullable|integer|min:1',
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
            'photos.*' => 'nullable|image|max:2048',
            'photo_types.*' => 'nullable|string',
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
        
        if (!isset($data['source_type']) || $data['source_type'] === '') {
            $data['source_type'] = 'Verpackung';
        }
        $data['created_by'] = auth()->id();

        $food = Food::create($data);

        if ($request->hasFile('photos')) {
            $types = $request->input('photo_types', []);
            $recordedAts = $request->input('photo_recorded_at', []);
            \Log::info('Store photos', ['types' => $types, 'files_count' => count($request->file('photos'))]);
            $files = $request->file('photos');

            foreach ($files as $index => $file) {
                if ($file->isValid()) {
                    $path = $file->store('food-photos', 'public');
                    $type = $types[$index] ?? 'other';
                    $recordedAt = !empty($recordedAts[$index]) ? $recordedAts[$index] : null;

                    $food->photos()->create([
                        'file_path' => $path,
                        'type' => $type,
                        'recorded_at' => $recordedAt,
                    ]);
                }
            }
        }

        return response()->json(['message' => 'Lebensmittel erfolgreich gespeichert!', 'food' => $food], 201);
    }

    public function destroy($id)
    {
        $food = \App\Models\Food::findOrFail($id);
        
        if (!auth()->user()->is_admin && $food->created_by !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $food->delete();

        return response()->json(null, 204);
    }
    
    public function show($id)
    {
        return response()->json(Food::with(['brand.manufacturer', 'photos', 'mainCategory', 'subCategory', 'creator', 'updater'])->findOrFail($id));
    }

    public function update(Request $request, $id)
{
    $food = Food::findOrFail($id);

    if (!auth()->user()->is_admin && $food->created_by !== auth()->id()) {
        return response()->json(['message' => 'Unauthorized.'], 403);
    }

    $updateData = $request->except([
        'photos', 
        'photo_types', 
        'photo_recorded_at',
        'deleted_photo_ids', 
        'existing_photo_types', 
        'existing_photo_recorded_at',
        '_method',
        'main_category_name',
        'sub_category_name',
        'brand_name',
        'manufacturer_name'
    ]);

    $request->validate([
        'photos.*' => 'nullable|image|max:2048',
        'photo_types.*' => 'nullable|string',
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

    $updateData['updated_by'] = auth()->id();

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
        $existingRecordedAts = $request->input('existing_photo_recorded_at', []);
        foreach ($request->input('existing_photo_types') as $photoId => $newType) {
            $recordedAt = !empty($existingRecordedAts[$photoId]) ? $existingRecordedAts[$photoId] : null;
            $food->photos()->where('id', $photoId)->update([
                'type' => $newType,
                'recorded_at' => $recordedAt
            ]);
        }
    }

    if ($request->hasFile('photos')) {
        $types = $request->input('photo_types', []);
        $recordedAts = $request->input('photo_recorded_at', []);
        \Log::info('Update photos received:', ['types' => $types, 'files_count' => count($request->file('photos'))]);
        
        foreach ($request->file('photos') as $index => $file) {
            if ($file->isValid()) {
                $path = $file->store('food-photos', 'public');
                $type = $types[$index] ?? 'other';
                $recordedAt = !empty($recordedAts[$index]) ? $recordedAts[$index] : null;
                \Log::info("Mapping photo {$index} to type: {$type}");
                
                $food->photos()->create([
                    'file_path' => $path,
                    'type'      => $type,
                    'recorded_at' => $recordedAt,
                ]);
            }
        }
    }

    return response()->json($food->load(['brand.manufacturer', 'photos', 'mainCategory', 'subCategory', 'creator', 'updater']), 200);
    }
}
