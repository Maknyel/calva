<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\InventoryHistoryGroup;
use Illuminate\Http\Request;

class InventoryGroupController extends Controller
{
    public function index(Request $request)
    {
        $query = InventoryHistoryGroup::query();

        // Search by customer name, phone, or group ID
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%")
                  ->orWhere('id', 'like', "%{$search}%");
            });
        }

        // Filter by payment method
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        // Order by most recent first
        $query->orderBy('date_time_adjustment', 'desc');

        // Paginate results
        $perPage = $request->get('per_page', 15);
        $groups = $query->paginate($perPage);

        return response()->json($groups);
    }
}
