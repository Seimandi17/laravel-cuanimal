<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Illuminate\Support\Facades\Auth;

class HistorialController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->role_id !== 2) {
            return response()->json(['status' => false, 'message' => 'No autorizado'], 403);
        }

        $historial = Reservation::with('product')
            ->where('provider_id', $user->id)
            ->where('estado', 'completado')
            ->orderBy('fecha', 'desc')
            ->get();

        return response()->json([
            'status' => true,
            'historial' => $historial
        ]);
    }
}
