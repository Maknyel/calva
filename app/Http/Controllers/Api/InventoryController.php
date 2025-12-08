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
        // Fix: Convert string "null" to actual null
        if ($request->image === "null" || $request->image === "") {
            $request->merge(['image' => null]);
        }

        $data = $request->validate([
            'distributor_id' => 'nullable|exists:distributors,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'inventory_type_id' => 'required|exists:inventory_types,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048', // optional image
            'reordering_level' => 'required|integer',
            'unit' => 'required|string|max:255',
            'current_quantity' => 'required|integer',
            'current_cost_price' => 'required|numeric',
            'current_sale_price' => 'required|numeric',
            'user_id'   => 'required|numeric'
        ]);

        if ($request->hasFile('image')) {

            // Store file
            $path = $request->file('image')->store('inventory', 'public');
            $data['image'] = $path;

            // ---- Hostinger Workaround ----
            $source = storage_path('app/public/' . $path);
            $destination = public_path('storage/' . $path);

            if (!file_exists(dirname($destination))) {
                mkdir(dirname($destination), 0755, true);
            }

            copy($source, $destination);
            // -------------------------------

        } else {
            $data['image'] = null; // optional image
        }

        $inventory = Inventory::create($data);

        return response()->json($inventory, 201);
    }



    public function update(Request $request, Inventory $inventory)
    {
        // Fix: Convert string "null" or empty string to actual null
        if ($request->image === "null" || $request->image === "") {
            $request->merge(['image' => null]);
        }

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

        // Handle image if uploaded
        if ($request->hasFile('image')) {
            $request->validate([
                'image' => 'image|max:2048', // validate only if a file is uploaded
            ]);

            // Delete old image if exists
            if ($inventory->image) {
                $oldPath = public_path('storage/' . $inventory->image);
                if (file_exists($oldPath)) {
                    unlink($oldPath); // remove from public/storage
                }
                Storage::disk('public')->delete($inventory->image); // remove from storage/app/public
            }

            // Store new image
            $path = $request->file('image')->store('inventory', 'public');
            $data['image'] = $path;

            // ---- Hostinger Workaround ----
            $source = storage_path('app/public/' . $path);
            $destination = public_path('storage/' . $path);

            if (!file_exists(dirname($destination))) {
                mkdir(dirname($destination), 0755, true);
            }

            copy($source, $destination);
            // -------------------------------
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
