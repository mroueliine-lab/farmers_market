<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
    public function index()
{
    $products = Product::with('category')->get();
    return response()->json(['success' => true, 'data' => $products]);
}

public function store(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'price_fcfa' => 'required|numeric|min:0',
        'category_id' => 'required|exists:categories,id',
    ]);

    $product = Product::create($validated);
    return response()->json(['success' => true, 'data' => $product], 201);
}

public function update(Request $request, $id)
{
    $product = Product::findOrFail($id);

    $validated = $request->validate([
        'name' => 'sometimes|string|max:255',
        'description' => 'nullable|string',
        'price_fcfa' => 'sometimes|numeric|min:0',
        'category_id' => 'sometimes|exists:categories,id',
    ]);

    $product->update($validated);
    return response()->json(['success' => true, 'data' => $product]);
}

public function destroy($id)
{
    Product::findOrFail($id)->delete();
    return response()->json(['success' => true, 'message' => 'Product deleted']);
}
}
