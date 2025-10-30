<?php

namespace App\Http\Controllers;

use App\Models\Proveedor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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

    public function listProviderPending()
    {
        $proveedores = Proveedor::with('user:id,name,lastName,email,status')
            ->whereHas('user', function ($query) {
                $query->where('status', 'pending');
            })
            ->select('id', 'businessName', 'user_id')
            ->get();
        
        return response()->json(['data' => $proveedores, 'status' => true]);
    }

    public function listProviderAll()
    {
        $proveedores = Proveedor::with('user:id,name,lastName,email,status')
            ->whereHas('user', function ($query) {
                $query->where('status', 'active');
            })
            ->select('id', 'businessName', 'user_id','phone', 'created_at')
            ->get();
        
        return response()->json(['data' => $proveedores, 'status' => true]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'lastName' => 'required|string|max:255',
            'businessName' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|max:255',
            // 'availability' => 'required|string|max:255',
            // 'certification' => 'nullable|string|max:255',
            // 'address' => 'required|string|max:255',
            // 'evidence' => 'required|string|max:255',
        ]);
        $user = User::create([
            'name' => $validated['name'],
            'lastName' => $validated['lastName'],
            'email' => $validated['email'],
            'role_id' => 2,
            'status' => 'pending',
            'password' => Hash::make($validated['password']),
        ]);
        if (!$user) {
            return response()->json([
                'data' => [],
                'message' => "server error",
                'status' => false
            ], 500);
        }
        $provider = [
                'businessName' => $validated['businessName'],
    'phone' => $validated['phone'],
    'user_id' => $user->id,
    'email' => $validated['email'],
    'category' => $request->category ?? '',
    'description' => $request->description ?? ''
        ];

        $data = Proveedor::create($provider);
        return  response()->json([
            'data' => $data,
            'status' => true
        ], 201);
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
        $provider->update($request->all());
        return  response()->json(['data' => [], 'status' => true]);
    }

    public function validateProvider(Request $request)
    {
        $validated = $request->validate([
            'status' => 'required|string|max:255',
            'id' => 'required',
        ]);
        
        $provider = Proveedor::findOrFail($validated['id']);
        $user = User::findOrFail($provider->user_id);

        if($validated['status'] === 'rejected'){
            
            $provider->delete();
            $user->delete();

        }else{

            $user->update($validated);
        }

        return  response()->json([
            'data' =>  Proveedor::with('user')->get(),
            'status' => true
        ], 200);
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
