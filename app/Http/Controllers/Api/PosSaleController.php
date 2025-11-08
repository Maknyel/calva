<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\InventoryHistoryGroup;

class PosSaleController extends Controller
{
    public function index(Request $request)
    {
        $query = InventoryHistoryGroup::query();

        // Optional date filter
        if ($request->start_date) {
            $query->whereDate('date_time_adjustment', '>=', $request->start_date);
        }
        if ($request->end_date) {
            $query->whereDate('date_time_adjustment', '<=', $request->end_date);
        }

        // Order by latest first
        $sales = $query->orderBy('date_time_adjustment', 'desc')->get();

        // Format response for chart
        $data = $sales->map(function ($sale) {
            return [
                'id' => $sale->id,
                'date_time_adjustment' => $sale->date_time_adjustment,
                'total_amount' => $sale->total_amount,
                'discount_percentage' => $sale->discount_percentage,
                'discounted_amount' => $sale->discounted_amount,
                'grand_total_amount' => $sale->grand_total_amount,
            ];
        });

        return response()->json(['data' => $data]);
    }
}
