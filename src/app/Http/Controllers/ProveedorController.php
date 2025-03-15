<?php

namespace App\Http\Controllers;

use App\Models\Proveedor;
use Illuminate\Http\Request;

class ProveedorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $provedor = Proveedor::all();
        return response()->json(['data' => $provedor, 'status' => true]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // $request->validate([
        //     'name' => 'required|string|max:255',
        //     'lastName' => 'required|string|max:255',
        //     'phone' => 'required|string|max:255',
        //     'email' => 'required|email|unique:provedores,email',
        //     'address' => 'required|string|max:255',
        //     'services' => 'required|string|max:255',
        //     'availability' => 'required|string|max:255',
        //     'certification' => 'nullable|string|max:255',
        //     'description' => 'required|string',
        //     'evidence' => 'requeried|string|max:255',
        // ]);
        $provider = Proveedor::create($request->all());
        return  response()->json(['data' => $provider, 'status' => true], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $provider = Proveedor::findOrFail($id);
        return response()->json($provider);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $provider = Proveedor::findOrFail($id);
        $provider-> update($request->all());
        return  response()->json(['data' => [], 'status' => true]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $provider = Proveedor::findOrFail($id);
        $provider->delete();
        return  response()->json(['message' => 'Provider removed successfully', 'status' => true]);
    }
}
