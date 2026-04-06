<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
    use App\Models\Category;


class CategoryController extends Controller
{

// List all categories with their children
public function index()
{
    $categories = Category::with(['children', 'children.products', 'products'])->whereNull('parent_id')->get();

    return response()->json(['success' => true, 'data' => $categories]);
}

// Create a category
public function store(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'parent_id' => 'nullable|exists:categories,id',
    ]);

    $category = Category::create($validated);
    return response()->json(['success' => true, 'data' => $category], 201);
}

// Update a category
public function update(Request $request, $id)
{
    $category = Category::findOrFail($id);

    $validated = $request->validate([
        'name' => 'sometimes|string|max:255',
        'parent_id' => 'nullable|exists:categories,id',
    ]);

    $category->update($validated);
    return response()->json(['success' => true, 'data' => $category]);
}

// Delete a category
public function destroy($id)
{
    Category::findOrFail($id)->delete();
    return response()->json(['success' => true, 'message' => 'Category deleted']);
}

}
