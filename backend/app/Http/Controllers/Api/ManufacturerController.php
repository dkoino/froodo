<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Manufacturer;

class ManufacturerController extends Controller
{
    public function index()
    {
        return response()->json(Manufacturer::all());
    }
}
