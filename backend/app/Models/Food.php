<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Food extends Model
{
    use HasFactory;

    protected $table = 'foods';

    protected $fillable = [
        'name',
        'variant',
        'brand_id',
        'main_category_id',
        'sub_category_id',
        'meat_type',
        'state',
        'calories_p100',
        'fat_p100',
        'sat_fat_p100',
        'carbs_p100',
        'sugar_p100',
        'fiber_p100',
        'protein_p100',
        'salt_p100',
        'total_amount',
        'measurement_unit',
        'portion_amount',
        'portion_label',
        'portion_descr',
        'barcode',
        'price',
        'notes',
        'source_type',
        'source_url',
        'user_id'
    ];

    public function mainCategory()
    {
        return $this->belongsTo(MainCategory::class);
    }

    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }
    public function photos()
    {
        return $this->hasMany(Photo::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
