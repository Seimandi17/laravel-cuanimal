<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function login(Request $request)
    {
        // // Validar los datos de entrada
        // $request->validate([
        //     'email' => 'required|email',
        //     'password' => 'required',
        // ]);

        // Buscar al usuario por email
        $user = User::where('email', $request->email)->first();

        // Verificar si el usuario existe y la contraseña es correcta
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Credenciales inválidas',
                'status' => false
            ], 401);
        }

        // Revocar todos los tokens existentes del usuario
        $user->tokens()->delete();

        // Generar un nuevo token
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'data' => [
                'user' => $user,
                'token' => $token
            ],
            'status' => true
        ], 200);
    }

    public function logout(Request $request)
{
    // Obtener el usuario autenticado
    $user = $request->user();

    if (!$user) {
        return response()->json([
            'message' => 'No autenticado',
            'status' => false
        ], 401);
    }

    // Revocar el token actual
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
        // Validar los datos
        // $request->validate([
        //     'name' => 'required|string|max:255',
        //     'email' => 'required|email|unique:users,email',
        //     'password' => 'required|string|min:8',
        // ]);

        // Crear el usuario (encriptar la contraseña)
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'status' => 1,
            'role_id' => 1,
            'password' => Hash::make($request->password),
        ]);

        // Generar un token de acceso para el usuario recién creado
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'data' => [
                'user' => $user,
                'token' => $token
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
