<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Farmer;

class FarmerController extends Controller
{
    public function store(Request $request)
{
    $validated = $request->validate([
        'identifier'   => 'required|string|unique:farmers,identifier',
        'firstname'    => 'required|string',
        'lastname'     => 'required|string',
        'phone_number' => 'required|string|unique:farmers,phone_number',
        'credit_limit' => 'required|numeric|min:0',
    ]);

    $farmer = Farmer::create($validated);
    return response()->json(['success' => true, 'data' => $farmer], 201);
}

public function show($id)
{
    $farmer = Farmer::with(['debts' => function($q) {
        $q->whereIn('status', ['pending', 'partial']);
    }])->findOrFail($id);

    return response()->json(['success' => true, 'data' => $farmer]);
}

public function search(Request $request)
{
    $query = $request->query('q');

    $farmer = Farmer::where('identifier', $query)
        ->orWhere('phone_number', $query)
        ->first();

    if (!$farmer) {
        return response()->json(['success' => false, 'message' => 'Farmer not found'], 404);
    }

    return response()->json(['success' => true, 'data' => $farmer]);
}


}
