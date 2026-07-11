<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    protected $fillable = ['name', 'manufacturer_id', 'logo_path'];

    public function manufacturer()
    {
        return $this->belongsTo(Manufacturer::class);
    }

    public function foods()
    {
        return $this->hasMany(Food::class);
    }
}
