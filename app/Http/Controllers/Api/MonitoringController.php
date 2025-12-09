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

        // Get orders grouped by inventory_group_id with customer info
        $orders = InventoryHistory::with('inventoryGroup')
            ->whereBetween('date_time_adjustment', [$dateFrom, $dateTo])
            ->whereNotNull('inventory_group_id')
            ->where('invinorout', 'out')
            ->select('inventory_group_id')
            ->selectRaw('SUM(cost_price_sold) as total_cost')
            ->selectRaw('SUM(sale_price_cost) as total_sales')
            ->selectRaw('SUM(COALESCE(quantity_sold, 0) - COALESCE(return_quantity, 0)) as total_quantity')
            ->selectRaw('MIN(date_time_adjustment) as order_date')
            ->groupBy('inventory_group_id')
            ->get()
            ->map(function ($order) {
                $group = $order->inventoryGroup;
                return [
                    'order_id' => $order->inventory_group_id,
                    'order_date' => $order->order_date,
                    'customer_name' => $group->customer_name ?? 'Walk-in',
                    'customer_address' => $group->customer_address ?? 'N/A',
                    'customer_phone' => $group->customer_phone ?? 'N/A',
                    'payment_method' => $group->payment_method ?? 'N/A',
                    'total_cost' => round($order->total_cost, 2),
                    'total_sales' => round($order->total_sales, 2),
                    'profit' => round($order->total_sales - $order->total_cost, 2),
                    'total_quantity' => $order->total_quantity,
                    'grand_total' => round($group->grand_total_amount ?? 0, 2),
                    'amount_paid' => round($group->amount_paid ?? 0, 2),
                ];
            });

        // Calculate totals
        $totalCost = $orders->sum('total_cost');
        $totalSales = $orders->sum('total_sales');
        $totalProfit = $totalSales - $totalCost;

        // Get items sold details
        $itemsSold = InventoryHistory::with(['inventory', 'inventoryType'])
            ->whereBetween('date_time_adjustment', [$dateFrom, $dateTo])
            ->where('invinorout', 'out')
            ->select(
                'inventory_id',
                'name',
                DB::raw('SUM(COALESCE(quantity_sold, 0) - COALESCE(return_quantity, 0)) as total_quantity_sold'),
                DB::raw('SUM(cost_price_sold) as total_cost_price'),
                DB::raw('SUM(sale_price_cost) as total_sale_price'),
                DB::raw('SUM(sale_price_cost - cost_price_sold) as profit')  // This is already correct: sales - cost
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
                DB::raw('SUM(COALESCE(quantity_sold, 0) - COALESCE(return_quantity, 0)) as total_items_sold')
            )
            ->groupBy('sale_date')
            ->orderBy('sale_date')
            ->get();

        // Get orders by customer (grouped by customer from inventory_history_groups)
        $ordersByCustomer = InventoryHistory::with('inventoryGroup')
            ->whereBetween('date_time_adjustment', [$dateFrom, $dateTo])
            ->whereNotNull('inventory_group_id')
            ->where('invinorout', 'out')
            ->select('inventory_group_id')
            ->selectRaw('SUM(cost_price_sold) as total_cost')
            ->selectRaw('SUM(sale_price_cost) as total_sales')
            ->selectRaw('SUM(COALESCE(quantity_sold, 0) - COALESCE(return_quantity, 0)) as total_quantity')
            ->selectRaw('MIN(date_time_adjustment) as order_date')
            ->groupBy('inventory_group_id')
            ->get()
            ->groupBy(function ($item) {
                return $item->inventoryGroup->customer_name ?? 'Walk-in';
            })
            ->map(function ($customerOrders, $customerName) {
                $totalCost = $customerOrders->sum('total_cost');
                $totalSales = $customerOrders->sum('total_sales');
                $totalQuantity = $customerOrders->sum('total_quantity');
                $orderCount = $customerOrders->count();

                // Get customer details from first order
                $firstOrder = $customerOrders->first();
                $group = $firstOrder->inventoryGroup;

                // Map individual orders for this customer
                $individualOrders = $customerOrders->map(function ($order) {
                    $group = $order->inventoryGroup;
                    return [
                        'order_id' => $order->inventory_group_id,
                        'order_date' => $order->order_date,
                        'payment_method' => $group->payment_method ?? 'N/A',
                        'total_quantity' => $order->total_quantity,
                        'total_cost' => round($order->total_cost, 2),
                        'total_sales' => round($order->total_sales, 2),
                        'profit' => round($order->total_sales - $order->total_cost, 2),
                        'grand_total' => round($group->grand_total_amount ?? 0, 2),
                    ];
                })->values();

                return [
                    'customer_name' => $customerName,
                    'customer_phone' => $group->customer_phone ?? 'N/A',
                    'customer_address' => $group->customer_address ?? 'N/A',
                    'order_count' => $orderCount,
                    'total_quantity' => $totalQuantity,
                    'total_cost' => round($totalCost, 2),
                    'total_sales' => round($totalSales, 2),
                    'profit' => round($totalSales - $totalCost, 2),
                    'orders' => $individualOrders,
                ];
            })
            ->sortByDesc('total_sales')
            ->values();

        return response()->json([
            'success' => true,
            'data' => [
                'summary' => [
                    'total_kita' => round($totalCost, 2),
                    'total_benta' => round($totalSales, 2),
                    'total_profit' => round($totalProfit, 2),
                    'total_transactions' => $orders->count(),
                ],
                'orders' => $orders,
                'orders_by_customer' => $ordersByCustomer,
                'items_sold' => $itemsSold,
                'sales_over_time' => $salesOverTime,
            ],
        ]);
    }

    public function printMonitoringReport(Request $request)
    {
        $request->validate([
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from',
        ]);

        $dateFrom = $request->date_from;
        $dateTo = $request->date_to;

        // Get orders by customer with individual orders
        $ordersByCustomer = InventoryHistory::with('inventoryGroup')
            ->whereBetween('date_time_adjustment', [$dateFrom, $dateTo])
            ->whereNotNull('inventory_group_id')
            ->where('invinorout', 'out')
            ->select('inventory_group_id')
            ->selectRaw('SUM(cost_price_sold) as total_cost')
            ->selectRaw('SUM(sale_price_cost) as total_sales')
            ->selectRaw('SUM(COALESCE(quantity_sold, 0) - COALESCE(return_quantity, 0)) as total_quantity')
            ->selectRaw('MIN(date_time_adjustment) as order_date')
            ->groupBy('inventory_group_id')
            ->get()
            ->groupBy(function ($item) {
                return $item->inventoryGroup->customer_name ?? 'Walk-in';
            })
            ->map(function ($customerOrders, $customerName) {
                $totalCost = $customerOrders->sum('total_cost');
                $totalSales = $customerOrders->sum('total_sales');
                $totalQuantity = $customerOrders->sum('total_quantity');
                $orderCount = $customerOrders->count();

                $firstOrder = $customerOrders->first();
                $group = $firstOrder->inventoryGroup;

                $individualOrders = $customerOrders->map(function ($order) {
                    $group = $order->inventoryGroup;
                    return [
                        'order_id' => $order->inventory_group_id,
                        'order_date' => $order->order_date,
                        'payment_method' => $group->payment_method ?? 'N/A',
                        'total_quantity' => $order->total_quantity,
                        'total_cost' => round($order->total_cost, 2),
                        'total_sales' => round($order->total_sales, 2),
                        'profit' => round($order->total_sales - $order->total_cost, 2),
                    ];
                })->values();

                return [
                    'customer_name' => $customerName,
                    'customer_phone' => $group->customer_phone ?? 'N/A',
                    'customer_address' => $group->customer_address ?? 'N/A',
                    'order_count' => $orderCount,
                    'total_quantity' => $totalQuantity,
                    'total_cost' => round($totalCost, 2),
                    'total_sales' => round($totalSales, 2),
                    'profit' => round($totalSales - $totalCost, 2),
                    'orders' => $individualOrders,
                ];
            })
            ->sortByDesc('total_sales');

        // Calculate totals
        $totalCost = $ordersByCustomer->sum('total_cost');
        $totalSales = $ordersByCustomer->sum('total_sales');
        $totalProfit = $totalSales - $totalCost;
        $totalOrders = $ordersByCustomer->sum('order_count');
        $totalQuantity = $ordersByCustomer->sum('total_quantity');

        $summary = [
            'total_kita' => round($totalCost, 2),
            'total_benta' => round($totalSales, 2),
            'total_profit' => round($totalProfit, 2),
            'total_orders' => $totalOrders,
            'total_quantity' => $totalQuantity,
        ];

        return view('monitoring.print', [
            'ordersByCustomer' => $ordersByCustomer,
            'summary' => $summary,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ]);
    }
}
