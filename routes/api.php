<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\{
    LoginController,
    UserController,
    InventoryTypeController,
    SupplierController,
    DistributorController,
    InventoryController,
    InventoryInController,
    InventoryHistoryController,
    PosSaleController
};

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::post('/login', [LoginController::class, 'login']);

Route::middleware('auth:sanctum')->get('/roles', function () {
    return \App\Models\Role::all();
});

// Sanctum-protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Paginated & searchable users
    Route::get('/users', [UserController::class, 'index']); 
    // Add user
    Route::post('/users', [UserController::class, 'store']);
    // Update user
    Route::put('/users/{user}', [UserController::class, 'update']);
    // Delete user
    Route::delete('/users/{user}', [UserController::class, 'destroy']);
    // Get current authenticated user
    Route::middleware('auth:sanctum')->get('/me', function (Request $request) {
        $user = $request->user()->load('role'); // eager load role
        return response()->json($user);
    });

    // Logout
    Route::post('/logout', [LoginController::class, 'logout']);

    // Update profile (info + password)
    Route::put('/profile', function (Request $request) {
        $user = $request->user();

        $request->validate([
            'fname' => 'sometimes|required|string',
            'lname' => 'sometimes|required|string',
            'email' => 'sometimes|required|email|unique:users,email,' . $user->id,
            'contact_number' => 'nullable|string',
            'password' => 'nullable|string|confirmed|min:6'
        ]);

        if ($request->has('fname')) $user->fname = $request->fname;
        if ($request->has('lname')) $user->lname = $request->lname;
        if ($request->has('email')) $user->email = $request->email;
        if ($request->has('contact_number')) $user->contact_number = $request->contact_number;

        if ($request->filled('password')) {
            $user->password = bcrypt($request->password);
        }

        $user->save();

        return response()->json($user);
    });
});


Route::prefix('inventory-types')->group(function () {
    Route::get('/', [InventoryTypeController::class, 'index']);
    Route::post('/', [InventoryTypeController::class, 'store']);
    Route::get('/{id}', [InventoryTypeController::class, 'show']);
    Route::put('/{id}', [InventoryTypeController::class, 'update']);
    Route::delete('/{id}', [InventoryTypeController::class, 'destroy']);
});

Route::prefix('suppliers')->group(function () {
    Route::get('/', [SupplierController::class, 'index']);
    Route::post('/', [SupplierController::class, 'store']);
    Route::put('/{id}', [SupplierController::class, 'update']);
    Route::delete('/{id}', [SupplierController::class, 'destroy']);
});

Route::prefix('distributors')->group(function () {
    Route::get('/', [DistributorController::class, 'index']);
    Route::post('/', [DistributorController::class, 'store']);
    Route::put('/{id}', [DistributorController::class, 'update']);
    Route::delete('/{id}', [DistributorController::class, 'destroy']);
});

Route::apiResource('inventory', InventoryController::class);

// routes/api.php
Route::post('/inventory_in', [InventoryInController::class, 'store']);
Route::post('/pos-sale', [InventoryInController::class, 'store_out']);
Route::get('/inventory-history', [InventoryHistoryController::class, 'index']);
Route::get('/pos-sales', [PosSaleController::class, 'index']);



