<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\InventoryHistory;

class InventoryHistoryController extends Controller
{
    public function index(Request $request)
    {
        $query = InventoryHistory::query();

        // Filters
        if ($request->inventory_type_id) {
            $query->where('inventory_type_id', $request->inventory_type_id);
        }
        if ($request->distributor_id) {
            $query->where('distributor_id', $request->distributor_id);
        }
        if ($request->supplier_id) {
            $query->where('supplier_id', $request->supplier_id);
        }
        if ($request->invinorout) {
            $query->where('invinorout', $request->invinorout);
        }
        if ($request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('description', 'like', "%$search%");
            });
        }

        $perPage = $request->per_page ?? 10;

        $history = $query->with(['inventoryType', 'distributor', 'supplier', 'inventoryGroup'])
            ->orderBy('date_time_adjustment', 'desc')
            ->paginate($perPage);

        return response()->json($history);
    }
}
