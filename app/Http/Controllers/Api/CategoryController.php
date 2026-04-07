<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
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
public function store(StoreCategoryRequest $request)
{
    $category = Category::create($request->validated());
    return response()->json(['success' => true, 'data' => $category], 201);
}

// Update a category
public function update(UpdateCategoryRequest $request, $id)
{
    $category = Category::findOrFail($id);
    $category->update($request->validated());
    return response()->json(['success' => true, 'data' => $category]);
}

// Delete a category
public function destroy($id)
{
    Category::findOrFail($id)->delete();
    return response()->json(['success' => true, 'message' => 'Category deleted']);
}

}
