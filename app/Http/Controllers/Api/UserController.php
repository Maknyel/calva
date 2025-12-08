<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    // List users (paginated & searchable)
    public function index(Request $request)
    {
        $search = $request->query('search');
        $perPage = $request->query('per_page', 6);

        $users = User::with('role') // eager load role
            ->where('id','!=', $request->query('user_id'))
            ->where('id','!=', 1)
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('fname', 'like', "%{$search}%")
                        ->orWhere('lname', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->orderBy('id', 'desc')
            ->paginate($perPage);

        // Add header image URL for each user
        $users->getCollection()->transform(function ($user) {
            $user->header_image = asset('assets/images/calva.jpg');
            return $user;
        });

        return response()->json($users);
    }

    // Add new user
    public function store(Request $request)
    {
        $validated = $request->validate([
            'fname' => 'required|string|max:255',
            'lname' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'contact_number' => 'required|string|max:255',
            'password' => 'nullable|string|min:6',
            'role_id' => 'required|exists:roles,id',  // validate against roles table
            'user_id'   => 'required|numeric'
        ]);

        $user = User::create([
            'fname' => $validated['fname'],
            'lname' => $validated['lname'],
            'email' => $validated['email'],
            'created_by'    => $validated['user_id'],
            'contact_number' => $validated['contact_number'],
            'password' => Hash::make($validated['password'] ?? 'Abc123456'),
            'role_id' => $validated['role_id'],
        ]);

        return response()->json($user->load('role'), 201);
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'fname' => 'required|string|max:255',
            'lname' => 'required|string|max:255',
            'contact_number' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:6',
            'role_id' => 'required|exists:roles,id',
        ]);

        $user->update([
            'fname' => $validated['fname'],
            'lname' => $validated['lname'],
            'email' => $validated['email'],
            'contact_number' => $validated['contact_number'],
            'role_id' => $validated['role_id'],
            'password' => $validated['password'] ? Hash::make($validated['password']) : $user->password,
        ]);

        return response()->json($user->load('role'));
    }

    // Delete user
    public function destroy(User $user)
    {
        $user->delete();
        return response()->json(['message' => 'User deleted']);
    }
}
