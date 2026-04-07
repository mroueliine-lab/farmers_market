<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;

class ProductController extends Controller
{
    public function index()
{
    $products = Product::with('category')->get();
    return response()->json(['success' => true, 'data' => $products]);
}

public function store(StoreProductRequest $request)
{
    $product = Product::create($request->validated());
    return response()->json(['success' => true, 'data' => $product], 201);
}

public function update(UpdateProductRequest $request, $id)
{
    $product = Product::findOrFail($id);
    $product->update($request->validated());
    return response()->json(['success' => true, 'data' => $product]);
}

public function destroy($id)
{
    Product::findOrFail($id)->delete();
    return response()->json(['success' => true, 'message' => 'Product deleted']);
}
}
