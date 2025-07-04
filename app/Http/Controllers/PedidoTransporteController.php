<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PedidoTransporte;

class PedidoTransporteController extends Controller
{
    public function index()
    {
        return response()->json([
            'success' => true,
            'data' => PedidoTransporte::latest()->get()->toArray()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre'     => 'required|string|max:255',
            'email'      => 'required|email',
            'telefono'   => 'required|string|max:20',
            'origen'     => 'nullable|string|max:255',
            'destino'    => 'nullable|string|max:255',
            'recogida'   => 'required|string|max:255',
            'entrega'    => 'required|string|max:255',
            'fecha'      => 'required|date',
            'adultos'    => 'required|integer|min:0',
            'ninos'      => 'required|integer|min:0',
            'mascotas'   => 'required|integer|min:0',
        ]);

        $pedido = PedidoTransporte::create($validated);

        return response()->json([
            'success' => true,
            'data' => $pedido
        ]);
    }
}
