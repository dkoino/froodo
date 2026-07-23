<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Photo extends Model
{
    protected $fillable = [
        'food_id',
        'file_path',
        'type',
        'is_primary'
    ];

    public function food()
    {
        return $this->belongsTo(Food::class);
    }
}