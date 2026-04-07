<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\StoreOperatorRequest;
use App\Http\Requests\StoreUserRequest;
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

public function store(StoreUserRequest $request)
{
    return $this->createUser($request->validated(), 'supervisor');
}

public function storeOperator(StoreOperatorRequest $request)
{
    return $this->createUser($request->validated(), 'operator');
}

private function createUser(array $validated, string $role)
{

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
