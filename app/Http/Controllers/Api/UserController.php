<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;


class UserController extends Controller
{
    public function index()
{
    $users = User::where('role', 'supervisor')->get();
    return response()->json(['success' => true, 'data' => $users]);
}

public function update(Request $request, $id)
{
    $user = User::findOrFail($id);

    $validated = $request->validate([
        'name' => 'sometimes|string|max:255',
        'email' => 'sometimes|email|unique:users,email,' . $id,
        'password' => 'sometimes|string|min:8|confirmed',
    ]);

    if (isset($validated['password'])) {
        $validated['password'] = Hash::make($validated['password']);
    }

    $user->update($validated);

    return response()->json(['success' => true, 'data' => $user]);
}

public function store(Request $request)
{
    return $this->createUser($request, 'supervisor');
}

public function storeOperator(Request $request)
{
    return $this->createUser($request, 'operator');
}

private function createUser(Request $request, string $role)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|string|min:8|confirmed',
    ]);

    $user = User::create([
        'name' => $validated['name'],
        'email' => $validated['email'],
        'password' => Hash::make($validated['password']),
        'role' => $role,
    ]);

    return response()->json(['success' => true, 'data' => $user], 201);
}


public function destroy($id)
{
    User::findOrFail($id)->delete();
    return response()->json(['success' => true, 'message' => 'User deleted']);
}

public function listOperators()
{
    $operators = User::where('role', 'operator')->get();
    return response()->json(['success' => true, 'data' => $operators]);
}

}
