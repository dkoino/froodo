<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Food extends Model
{
    use HasFactory;

    protected $table = 'foods';
    
    protected $fillable = [
        'brand_id',
        'name',
        'variant',
        'barcode',
        'calories_p100',
        'fat_p100',
        'sat_fat_p100',
        'carbs_p100',
        'sugar_p100',
        'fiber_p100',
        'protein_p100',
        'salt_p100',
        'measurement_unit',
        'total_amount',
        'portion_label',
        'portion_amount',
        'price',
        'source_type',
        'source_url',
        'notes',
    ];

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }
    public function photos()
    {
        return $this->hasMany(Photo::class);
    }
}