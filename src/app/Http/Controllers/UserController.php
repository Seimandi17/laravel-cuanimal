<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function login(Request $request)
{
    try {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            return response()->json([
                'message' => 'Credenciales inválidas',
                'status' => false
            ], 401);
        }
        if ($user && (!$user->status === 'active')) {
            return response()->json([
                'message' => 'pending',
                'status' => false
            ], 400);
        }

        $user->tokens()->delete();

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'data' => [
                'user' => $user,
                'token' => $token
            ],
            'status' => true
        ], 200);
    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'data' => [],
            'status' => false,
            'message' => 'Error de validación',
            'errors' => $e->errors(),
        ], 422);
    } catch (\Exception $e) {
        return response()->json([
            'data' => [],
            'status' => false,
            'message' => 'Error al iniciar sesión: ' . $e->getMessage(),
        ], 500);
    }
}

    public function logout(Request $request)
{
    $user = $request->user();

    if (!$user) {
        return response()->json([
            'message' => 'No autenticado',
            'status' => false
        ], 401);
    }

    $user->currentAccessToken()->delete();

    return response()->json([
        'message' => 'Sesión cerrada con éxito',
        'status' => true
    ]);
}
    public function index()
    {
        $provedor = User::all();
        return response()->json(['data' => $provedor, 'status' => true]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'lastName' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
        ]);

        $user = User::create([
            'name' => $request->name,
            'lastName' => $request->lastName,
            'email' => $request->email,
            'status' => 'active',
            'role_id' => 3,
            'password' => Hash::make($request->password),
        ]);

        // $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'data' => [
                'user' => $user
                // 'token' => $token
            ],
            'status' => true
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::findOrFail($id);
        return response()->json($user);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);
        $user->update($request->all());
        return  response()->json(['data' => [], 'status' => true]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return  response()->json(['message' => 'user removed successfully', 'status' => true]);
    }
}
