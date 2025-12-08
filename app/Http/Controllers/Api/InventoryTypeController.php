<?php
// app/Http/Controllers/API/InventoryTypeController.php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\InventoryType;
use Illuminate\Http\Request;

class InventoryTypeController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');
        $perPage = $request->query('per_page', 6);

        $inventoryTypes = InventoryType::query()
            ->when($search, fn($q) => $q->where('name', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%"))
            ->orderBy('id', 'desc')
            ->paginate($perPage);

        return response()->json($inventoryTypes);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'user_id'   => 'required|numeric'
        ]);

        $item = InventoryType::create($validated);

        return response()->json($item, 201);
    }

    public function show($id)
    {
        $item = InventoryType::findOrFail($id);
        return response()->json($item);
    }

    public function update(Request $request, $id)
    {
        $item = InventoryType::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $item->update($validated);

        return response()->json($item);
    }

    public function destroy($id)
    {
        $item = InventoryType::findOrFail($id);
        $item->delete();

        return response()->json(['message' => 'Inventory type deleted successfully']);
    }
}
