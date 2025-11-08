<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');
        $perPage = $request->query('per_page', 6);

        $items = Inventory::with(['supplier', 'distributor', 'inventoryType'])
            ->when($search, fn($q) => $q->where('name', 'like', "%$search%")
                ->orWhere('description', 'like', "%$search%"))
            ->orderBy('id', 'desc')
            ->paginate($perPage);

        return response()->json($items);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'distributor_id' => 'nullable|exists:distributors,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'inventory_type_id' => 'required|exists:inventory_types,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'reordering_level' => 'required|integer',
            'unit' => 'required|string|max:255',
            'current_quantity' => 'required|integer',
            'current_cost_price' => 'required|numeric',
            'current_sale_price' => 'required|numeric',
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('inventory', 'public');
        }

        $inventory = Inventory::create($data);
        return response()->json($inventory, 201);
    }

    public function update(Request $request, Inventory $inventory)
    {
        // Validate all fields except image
        $data = $request->validate([
            'distributor_id' => 'nullable|exists:distributors,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'inventory_type_id' => 'required|exists:inventory_types,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'reordering_level' => 'required|integer',
            'unit' => 'required|string|max:255',
            'current_quantity' => 'required|integer',
            'current_cost_price' => 'required|numeric',
            'current_sale_price' => 'required|numeric',
        ]);

        // Only validate and update image if a file is uploaded
        if ($request->hasFile('image')) {
            $request->validate([
                'image' => 'image|max:2048', // only validate if a file is uploaded
            ]);

            // Delete old image if exists
            if ($inventory->image) {
                Storage::disk('public')->delete($inventory->image);
            }

            // Store new image
            $data['image'] = $request->file('image')->store('inventory', 'public');
        }

        $inventory->update($data);

        return response()->json($inventory);
    }

    public function destroy(Inventory $inventory)
    {
        if ($inventory->image) {
            Storage::disk('public')->delete($inventory->image);
        }
        $inventory->delete();
        return response()->json(['message' => 'Inventory deleted successfully']);
    }
}
