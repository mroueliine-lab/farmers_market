<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFarmerRequest;
use Illuminate\Http\Request;
use App\Models\Farmer;

class FarmerController extends Controller
{
    public function store(StoreFarmerRequest $request)
{
    $farmer = Farmer::create($request->validated());
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
    $request->validate([
        'q' => 'required|string|max:50',
    ]);

    $query = $request->query('q');

    $farmer = Farmer::where('identifier', $query)
        ->orWhere('phone_number', $query)
        ->first();

    if (!$farmer) {
        return response()->json(['success' => false, 'message' => 'Farmer not found'], 404);
    }

    return response()->json(['success' => true, 'data' => $farmer]);
}

public function debts($id)
{
    $farmer = Farmer::findOrFail($id);
    $debts = $farmer->debts()->orderBy('created_at', 'asc')->get();
    return response()->json(['success' => true, 'data' => $debts]);
}

public function repayments($id)
{
    $farmer = Farmer::findOrFail($id);
    $repayments = $farmer->repayments()->orderBy('created_at', 'asc')->get();
    return response()->json(['success' => true, 'data' => $repayments]);
}


}
