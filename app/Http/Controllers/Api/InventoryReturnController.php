<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{
    Inventory,
    InventoryHistory,
    InventoryReturn
};
use Illuminate\Support\Facades\DB;

class InventoryReturnController extends Controller
{
    public function index(Request $request)
    {
        $query = InventoryReturn::with(['inventoryHistory.inventory']);

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->whereHas('inventoryHistory', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        // Sorting
        $sortBy = $request->get('sortBy', 'created_at');
        $sortOrder = $request->get('sortOrder', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Pagination
        $perPage = $request->get('perPage', 10);
        $returns = $query->paginate($perPage);

        return response()->json($returns);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'inventory_out_id' => 'required|exists:inventory_histories,id',
            'quantity' => 'required|integer|min:1',
            'remarks' => 'nullable|string',
        ]);

        DB::transaction(function () use ($validated) {
            // Get the inventory history record (type 'out')
            $inventoryHistory = InventoryHistory::findOrFail($validated['inventory_out_id']);

            // Verify it's an 'out' type transaction
            if ($inventoryHistory->invinorout !== 'out') {
                throw new \Exception("Can only return items from 'out' type inventory history.");
            }

            // Create the return record
            $return = InventoryReturn::create([
                'inventory_out_id' => $validated['inventory_out_id'],
                'quantity' => $validated['quantity'],
                'remarks' => $validated['remarks'] ?? null,
                'date_time_adjustment' => now(),
            ]);

            // Update inventory current_quantity (add back returned items)
            $inventory = Inventory::find($inventoryHistory->inventory_id);
            $inventory->current_quantity += $validated['quantity'];
            $inventory->save();

            // Optionally update the inventory_history return_quantity field
            $inventoryHistory->return_quantity = ($inventoryHistory->return_quantity ?? 0) + $validated['quantity'];
            $inventoryHistory->save();
        });

        return response()->json(['message' => 'Return processed successfully.']);
    }

    public function show($id)
    {
        $return = InventoryReturn::with(['inventoryHistory.inventory'])->findOrFail($id);
        return response()->json($return);
    }

    public function destroy($id)
    {
        $return = InventoryReturn::findOrFail($id);
        $return->delete();

        return response()->json(['message' => 'Return deleted successfully.']);
    }

    // Get inventory history records with type 'out' for return selection
    public function getInventoryInHistory(Request $request)
    {
        $query = InventoryHistory::where('invinorout', 'out')
            ->with(['inventory', 'supplier', 'distributor']);

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        // Pagination
        $perPage = $request->get('perPage', 10);
        $histories = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json($histories);
    }
}
