<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function register(Request $request)
{
    // Validate input
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|string|min:8|confirmed',
        'role' => 'required|string|in:admin,supervisor,operator', // Ensure role is one of the allowed values
    ]);
    
    // Create user (password automatically hashed)
    $user = User::create([
        'name' => $validated['name'],
        'email' => $validated['email'],
        'password' => Hash::make($validated['password']),
    ]);

    // Create API token
    $token = $user->createToken('auth_token')->plainTextToken;

    // Return user and token
    return response()->json([
        'success' => true,
        'message' => 'User registered successfully',
        'user' => $user,
        'token' => $token,
    ], 201);
}

public function login(Request $request)
{
    // Validate input
    $validated = $request->validate([
        'email' => 'required|email',
        'password' => 'required|string',
    ]);

    // Find user by email
    $user = User::where('email', $validated['email'])->first();

    // Check if user exists and password is correct
    if (!$user || !Hash::check($validated['password'], $user->password)) {
        return response()->json([
            'success' => false,
            'message' => 'Invalid email or password',
        ], 401);
    }

    // Create API token
    $token = $user->createToken('auth_token')->plainTextToken;

    // Return user and token
    return response()->json([
        'success' => true,
        'message' => 'User logged in successfully',
        'user' => $user,
        'token' => $token,
    ], 200);
}

public function logout(Request $request)
{
    // Revoke the token that was used to authenticate the current request
    $request->user()->currentAccessToken()->delete();

    return response()->json([
        'success' => true,
        'message' => 'User logged out successfully',
    ], 200);
}

}
