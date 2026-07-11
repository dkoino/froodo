<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Food;
use App\Models\Brand;
use App\Models\Manufacturer;
use App\Models\MainCategory;
use App\Models\SubCategory;
use Carbon\Carbon;

class FoodImportExportController extends Controller
{
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt',
        ]);

        $path = $request->file('file')->getRealPath();
        $file = fopen($path, 'r');

        $firstLine = fgets($file);
        $delimiter = strpos($firstLine, ';') !== false ? ';' : ',';
        rewind($file);

        $headers = fgetcsv($file, 0, $delimiter);
        $headers[0] = preg_replace('/^\xEF\xBB\xBF/', '', $headers[0]);

        $createdCount = 0;
        $updatedCount = 0;

        while (($row = fgetcsv($file, 0, $delimiter)) !== false) {
            if (empty(array_filter($row))) continue;

            $data = array_combine($headers, $row);

            $brandId = null;
            if (!empty($data['brand_name'])) {

                $manufacturerId = null;
                if (!empty($data['manufacturer'])) {
                    $manufacturer = Manufacturer::firstOrCreate(
                        ['name' => trim($data['manufacturer'])]
                    );
                    $manufacturerId = $manufacturer->id;
                }

                $brand = Brand::firstOrCreate(
                    ['name' => trim($data['brand_name'])],
                    ['manufacturer_id' => $manufacturerId]
                );
                $brandId = $brand->id;
            }

            $mainCategoryId = null;
            if (!empty($data['main_category'])) {
                $mainCategory = MainCategory::firstOrCreate(
                    ['name' => trim($data['main_category'])]
                );
                $mainCategoryId = $mainCategory->id;
            }

            $subCategoryId = null;
            if (!empty($data['sub_category']) && $mainCategoryId) {
                $subCategory = SubCategory::firstOrCreate(
                    [
                        'name' => trim($data['sub_category']),
                        'main_category_id' => $mainCategoryId
                    ]
                );
                $subCategoryId = $subCategory->id;
            }

            $macros = ['calories_p100', 'fat_p100', 'sat_fat_p100', 'carbs_p100', 'sugar_p100', 'protein_p100', 'salt_p100', 'fiber_p100'];
            foreach ($macros as $macro) {
                if (isset($data[$macro])) {
                    $val = str_replace(',', '.', $data[$macro]);
                    $data[$macro] = $val === '' ? null : (float) $val;
                }
            }

            $foodId = !empty($data['id']) ? $data['id'] : null;

            $updateData = [
                'name' => $data['name'] ?? null,
                'variant' => $data['variant'] ?? null,
                'brand_id' => $brandId,
                'main_category_id' => $mainCategoryId,
                'sub_category_id' => $subCategoryId,
                'meat_type' => !empty($data['meat_type']) ? $data['meat_type'] : 'Unbekannt',
                'state' => $data['state'] ?? null,
                'calories_p100' => $data['calories_p100'] ?? null,
                'fat_p100' => $data['fat_p100'] ?? null,
                'sat_fat_p100' => $data['sat_fat_p100'] ?? null,
                'carbs_p100' => $data['carbs_p100'] ?? null,
                'sugar_p100' => $data['sugar_p100'] ?? null,
                'protein_p100' => $data['protein_p100'] ?? null,
                'salt_p100' => $data['salt_p100'] ?? null,
                'fiber_p100' => $data['fiber_p100'] ?? null,
                'total_amount' => !empty($data['total_amount']) ? str_replace(',', '.', $data['total_amount']) : null,
                'measurement_unit' => $data['measurement_unit'] ?? null,
                'portion_amount' => !empty($data['portion_amount']) ? str_replace(',', '.', $data['portion_amount']) : null,
                'portion_label' => $data['portion_label'] ?? null,
                'barcode' => $data['barcode'] ?? null,
                'price' => isset($data['price']) && trim($data['price']) !== '' ? str_replace([',', '€', ' '], ['.', '', ''], $data['price']) : null,
                'notes' => $data['notes'] ?? null,
                'source_type' => !empty($data['source_type']) ? $data['source_type'] : null,
                'source_url' => $data['source_url'] ?? null,
            ];

            $food = Food::updateOrCreate(
                ['id' => $foodId],
                $updateData
            );

            if (!empty($data['created_at'])) {
                try {
                    $parsedDate = Carbon::createFromFormat('d.m.Y', $data['created_at'])->startOfDay();
                    $food->created_at = $parsedDate;
                    $food->save(['timestamps' => false]);
                } catch (\Exception $e) {
                }
            }

            if ($food->wasRecentlyCreated) {
                $createdCount++;
            } else {
                $updatedCount++;
            }
        }

        fclose($file);

        return response()->json([
            'message' => 'Import erfolgreich abgeschlossen.',
            'created' => $createdCount,
            'updated' => $updatedCount
        ], 200);
    }

    public function export()
    {
        $foods = Food::with(['brand.manufacturer', 'mainCategory', 'subCategory'])->get();

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=foods_export_" . date('Y-m-d_H-i') . ".csv",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = [
            'id',
            'name',
            'variant',
            'brand_id',
            'brand_name',
            'manufacturer_id',
            'manufacturer',
            'main_category_id',
            'main_category',
            'sub_category_id',
            'sub_category',
            'meat_type',
            'state',
            'calories_p100',
            'fat_p100',
            'sat_fat_p100',
            'carbs_p100',
            'sugar_p100',
            'protein_p100',
            'salt_p100',
            'fiber_p100',
            'created_at',
            'total_amount',
            'measurement_unit',
            'portion_label',
            'portion_amount',
            'notes',
            'price',
            'barcode',
            'source_type',
            'source_url',
            'updated_at',
            'photos_info'
        ];

        $callback = function () use ($foods, $columns) {
            $file = fopen('php://output', 'w');

            fputs($file, "\xEF\xBB\xBF");
            fputcsv($file, $columns, ';');

            foreach ($foods as $food) {
                $row['id'] = $food->id;
                $row['name'] = $food->name;
                $row['variant'] = $food->variant;
                $row['brand_id'] = $food->brand_id;
                $row['brand_name'] = $food->brand ? $food->brand->name : '';

                $row['manufacturer_id'] = ($food->brand && $food->brand->manufacturer) ? $food->brand->manufacturer->id : '';
                $row['manufacturer'] = ($food->brand && $food->brand->manufacturer) ? $food->brand->manufacturer->name : '';

                $row['main_category_id'] = $food->main_category_id;
                $row['main_category'] = $food->mainCategory ? $food->mainCategory->name : '';
                $row['sub_category_id'] = $food->sub_category_id;
                $row['sub_category'] = $food->subCategory ? $food->subCategory->name : '';
                $row['meat_type'] = $food->meat_type;
                $row['state'] = $food->state;

                $macros = ['calories_p100', 'fat_p100', 'sat_fat_p100', 'carbs_p100', 'sugar_p100', 'protein_p100', 'salt_p100', 'fiber_p100'];
                foreach ($macros as $macro) {
                    $val = $food->$macro;
                    $row[$macro] = $val !== null ? str_replace('.', ',', (string)$val) : '';
                }

                $row['created_at'] = $food->created_at ? $food->created_at->format('d.m.Y') : '';
                $row['total_amount'] = $food->total_amount !== null ? str_replace('.', ',', (string)$food->total_amount) : '';
                $row['measurement_unit'] = $food->measurement_unit;
                $row['portion_label'] = $food->portion_label;
                $row['portion_amount'] = $food->portion_amount !== null ? str_replace('.', ',', (string)$food->portion_amount) : '';
                $row['notes'] = $food->notes;
                $row['price'] = $food->price !== null ? str_replace('.', ',', (string)$food->price) : '';
                $row['barcode'] = $food->barcode;
                $row['source_type'] = $food->source_type;
                $row['source_url'] = $food->source_url;
                $row['updated_at'] = $food->updated_at ? $food->updated_at->format('d.m.Y') : '';
                $row['photos_info'] = '';

                $mappedRow = [];
                foreach ($columns as $col) {
                    $mappedRow[] = $row[$col] ?? '';
                }

                fputcsv($file, $mappedRow, ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
