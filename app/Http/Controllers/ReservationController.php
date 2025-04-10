<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Products;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class ReservationController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'product_id'      => 'required|exists:products,id',
            'fecha'           => 'required|date|after_or_equal:today',
            'direccion'       => 'required|string|max:255',
            'cantidad'        => 'required|integer|min:1',
            'mensaje'         => 'nullable|string',
        ]);

        $user = Auth::user();
        $product = Products::findOrFail($request->product_id);

        $reserva = Reservation::create([
            'user_id'         => $user->id,
            'product_id'      => $request->product_id,
            'provider_id'     => $product->provider_id,
            'nombre_cliente'  => $user->name ?? 'Sin nombre',
            'email_cliente'   => $user->email ?? 'Sin email',
            'telefono_cliente'=> $user->phone ?? null,
            'cantidad'        => $request->cantidad,
            'mensaje'         => $request->mensaje,
            'direccion'       => $request->direccion,
            'fecha'           => $request->fecha,
            'estado'          => 'pendiente',
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Reserva creada con éxito.',
            'data' => $reserva
        ], 201);
    }
    public function index()
    {
        $user = Auth::user();
    
        if ($user->role_id !== 2) {
            return response()->json(['status' => false, 'message' => 'No autorizado'], 403);
        }
    
        $proveedor = $user->proveedor;
    
        if (!$proveedor) {
            return response()->json(['status' => false, 'message' => 'No es proveedor'], 403);
        }
    
        $reservas = Reservation::with('product')
            ->where('provider_id', $proveedor->id)
            ->get();
    
        return response()->json([
            'status' => true,
            'reservations' => $reservas
        ]);
    }

}

