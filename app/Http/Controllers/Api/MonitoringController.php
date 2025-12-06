<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\InventoryHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MonitoringController extends Controller
{
    public function getMonitoringData(Request $request)
    {
        $request->validate([
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from',
        ]);

        $dateFrom = $request->date_from;
        $dateTo = $request->date_to;

        // Get aggregated data grouped by inventory_group_id
        $aggregatedData = InventoryHistory::whereBetween('date_time_adjustment', [$dateFrom, $dateTo])
            ->whereNotNull('inventory_group_id')
            ->where('invinorout', 'out') // Only count sales (out transactions)
            ->select('inventory_group_id')
            ->selectRaw('SUM(sale_price_cost) as total_kita')
            ->selectRaw('SUM(cost_price_sold) as total_benta')
            ->selectRaw('SUM(quantity_sold) as total_quantity')
            ->selectRaw('COUNT(*) as transaction_count')
            ->groupBy('inventory_group_id')
            ->get();

        // Calculate totals
        $totalKita = $aggregatedData->sum('total_kita');
        $totalBenta = $aggregatedData->sum('total_benta');
        $totalProfit = $totalKita - $totalBenta;

        // Get items sold details
        $itemsSold = InventoryHistory::with(['inventory', 'inventoryType'])
            ->whereBetween('date_time_adjustment', [$dateFrom, $dateTo])
            ->where('invinorout', 'out')
            ->select(
                'inventory_id',
                'name',
                DB::raw('SUM(quantity_sold) as total_quantity_sold'),
                DB::raw('SUM(cost_price_sold) as total_cost_price'),
                DB::raw('SUM(sale_price_cost) as total_sale_price'),
                DB::raw('SUM(sale_price_cost - cost_price_sold) as profit')
            )
            ->groupBy('inventory_id', 'name')
            ->orderByDesc('total_quantity_sold')
            ->get();

        // Get sales over time for chart (grouped by date)
        $salesOverTime = InventoryHistory::whereBetween('date_time_adjustment', [$dateFrom, $dateTo])
            ->where('invinorout', 'out')
            ->select(
                DB::raw('DATE(date_time_adjustment) as sale_date'),
                DB::raw('SUM(sale_price_cost) as total_sales'),
                DB::raw('SUM(quantity_sold) as total_items_sold')
            )
            ->groupBy('sale_date')
            ->orderBy('sale_date')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'summary' => [
                    'total_kita' => round($totalKita, 2),
                    'total_benta' => round($totalBenta, 2),
                    'total_profit' => round($totalProfit, 2),
                    'total_transactions' => $aggregatedData->sum('transaction_count'),
                ],
                'items_sold' => $itemsSold,
                'sales_over_time' => $salesOverTime,
            ],
        ]);
    }
}
