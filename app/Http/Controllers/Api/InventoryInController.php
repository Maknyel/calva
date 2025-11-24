<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\{
    Inventory,
    InventoryHistory,
    InventoryHistoryGroup
};
use Illuminate\Support\Facades\DB;

class InventoryInController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:inventories,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.cost_price' => 'required|numeric|min:0',
            'items.*.sale_price' => 'required|numeric|min:0',
            'items.*.expiration_date_time' => 'nullable|date',
        ]);

        DB::transaction(function () use ($validated) {
            foreach ($validated['items'] as $item) {
                // Update inventory table
                $inventory = Inventory::find($item['id']);
                $inventory->current_quantity += $item['quantity'];
                $inventory->current_cost_price = $item['cost_price'];
                $inventory->current_sale_price = $item['sale_price'];
                $inventory->save();

                // Create inventory history
                InventoryHistory::create([
                    'inventory_id' => $inventory->id,
                    'distributor_id' => $inventory->distributor_id,
                    'supplier_id' => $inventory->supplier_id,
                    'inventory_type_id' => $inventory->inventory_type_id,
                    'invinorout' => 'in',
                    'name' => $inventory->name,
                    'description' => $inventory->description,
                    'image' => $inventory->image,
                    'reordering_level' => $inventory->reordering_level,
                    'unit' => $inventory->unit,
                    'quantity_sold' => $item['quantity'],
                    'cost_price_sold' => $item['cost_price'],
                    'sale_price_cost' => $item['sale_price'],
                    'total' => $item['quantity'] * $item['cost_price'],
                    'inventory_group_id' => null,
                    'date_time_adjustment' => now(),
                    'expiration_date_time' => $item['expiration_date_time'] ?? null,
                ]);
            }
        });

        return response()->json(['message' => 'Inventory updated successfully.']);
    }


    public function store_out(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:inventories,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.cost_price' => 'required|numeric|min:0',
            'items.*.sale_price' => 'required|numeric|min:0',
            'discount_percent' => 'nullable|numeric|min:0|max:100',
            'dr_number' => 'nullable|string',
        ]);

        DB::transaction(function () use ($validated) {

            // Calculate totals
            $totalAmount = collect($validated['items'])->sum(function ($item) {
                return $item['sale_price'] * $item['quantity'];
            });

            $discountPercentage = $validated['discount_percent'] ?? 0;
            $discountedAmount = $totalAmount * ($discountPercentage / 100);
            $grandTotalAmount = $totalAmount - $discountedAmount;

            // Create inventory_history_group
            $historyGroup = InventoryHistoryGroup::create([
                'total_amount' => $totalAmount,
                'dr_number' => $validated['dr_number'] ?? null,
                'discount_percentage' => $discountPercentage,
                'discounted_amount' => $discountedAmount,
                'grand_total_amount' => $grandTotalAmount,
                'date_time_adjustment' => now(),
            ]);

            foreach ($validated['items'] as $item) {
                // Subtract quantity from inventory
                $inventory = Inventory::find($item['id']);
                if ($inventory->current_quantity < $item['quantity']) {
                    throw new \Exception("Inventory quantity for {$inventory->name} exceeds current stock.");
                }
                $inventory->current_quantity -= $item['quantity'];
                $inventory->save();

                // Create inventory history
                InventoryHistory::create([
                    'inventory_id' => $inventory->id,
                    'distributor_id' => $inventory->distributor_id,
                    'supplier_id' => $inventory->supplier_id,
                    'inventory_type_id' => $inventory->inventory_type_id,
                    'invinorout' => 'out',
                    'name' => $inventory->name,
                    'description' => $inventory->description,
                    'image' => $inventory->image,
                    'reordering_level' => $inventory->reordering_level,
                    'unit' => $inventory->unit,
                    'quantity_sold' => $item['quantity'],
                    'cost_price_sold' => $item['cost_price'],
                    'sale_price_cost' => $item['sale_price'],
                    'total' => $item['quantity'] * $item['sale_price'],
                    'inventory_group_id' => $historyGroup->id,
                    'date_time_adjustment' => now(),
                ]);
            }
        });

        return response()->json(['message' => 'Inventory out successfully processed.']);
    }
}
