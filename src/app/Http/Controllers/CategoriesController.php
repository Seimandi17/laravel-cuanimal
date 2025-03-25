<?php

namespace App\Http\Controllers;

use App\Models\categories;
use Illuminate\Http\Request;

class CategoriesController extends Controller
{
    public function index()
    {
        $category = categories::all();
        return response()->json(['data' => $category, 'status' => true]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $category = categories::create($request->all());
        return  response()->json(['data' => $category, 'status' => true], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $category = categories::findOrFail($id);
        return response()->json($category);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $category = categories::findOrFail($id);
        $category-> update($request->all());
        return  response()->json(['data' => [], 'status' => true]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $category = categories::findOrFail($id);
        $category->delete();
        return  response()->json(['message' => 'category removed successfully', 'status' => true]);
    }
}
