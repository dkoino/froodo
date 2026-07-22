<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FoodLog;
use Illuminate\Http\Request;
use Carbon\Carbon;

class FoodLogController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->query('date') ? Carbon::parse($request->query('date')) : Carbon::today();
        
        $logs = FoodLog::with(['food.brand'])
            ->where('user_id', auth()->id())
            ->whereDate('consumed_at', $date)
            ->orderBy('consumed_at', 'asc')
            ->get();
            
        return response()->json($logs);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'food_id' => 'required|exists:foods,id',
            'amount' => 'required|numeric|min:0.1',
            'meal_type' => 'nullable|string',
            'consumed_at' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $log = FoodLog::create([
            'user_id' => auth()->id(),
            'food_id' => $validated['food_id'],
            'amount' => $validated['amount'],
            'meal_type' => $validated['meal_type'] ?? 'Sonstiges',
            'consumed_at' => $validated['consumed_at'] ?? Carbon::now(),
            'notes' => $validated['notes'] ?? null,
        ]);

        return response()->json($log->load('food.brand'), 201);
    }

    public function update(Request $request, $id)
    {
        $log = FoodLog::where('user_id', auth()->id())->findOrFail($id);
        
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.1',
        ]);

        $log->update(['amount' => $validated['amount']]);

        return response()->json($log->load('food.brand'));
    }

    public function destroy($id)
    {
        $log = FoodLog::where('user_id', auth()->id())->findOrFail($id);
        $log->delete();
        
        return response()->json(null, 204);
    }
}
