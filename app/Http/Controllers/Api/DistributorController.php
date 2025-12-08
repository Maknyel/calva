<?php

// app/Http/Controllers/API/DistributorController.php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Distributor;
use Illuminate\Http\Request;

class DistributorController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');
        $perPage = $request->query('per_page', 6);

        $distributors = Distributor::query()
            ->when($search, fn($q) => $q->where('name', 'like', "%{$search}%")
                ->orWhere('address', 'like', "%{$search}%")
                ->orWhere('contact_number', 'like', "%{$search}%"))
            ->orderBy('id', 'desc')
            ->paginate($perPage);

        return response()->json($distributors);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'contact_number' => 'nullable|string|max:50',
            'email' => 'required|string|max:255',
            'user_id'   => 'required|numeric'
        ]);

        $distributor = Distributor::create($validated);

        return response()->json($distributor, 201);
    }

    public function update(Request $request, $id)
    {
        $distributor = Distributor::findOrFail($id);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'contact_number' => 'nullable|string|max:50',
            'email' => 'required|string|max:255',
        ]);
        $distributor->update($validated);

        return response()->json($distributor);
    }

    public function destroy($id)
    {
        $distributor = Distributor::findOrFail($id);
        $distributor->delete();

        return response()->json(['message' => 'Distributor deleted successfully']);
    }
}
