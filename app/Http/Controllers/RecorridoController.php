<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Recorrido;
use Illuminate\Support\Facades\Storage;

class RecorridoController extends Controller
{
    public function index(Request $request)
    {
        $query = Recorrido::query();

        if ($request->has('provincia')) {
            $query->where('provincia', $request->provincia);
        }

        if ($request->get('orden') === 'antiguos') {
            $query->orderBy('created_at', 'asc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        return response()->json($query->get());
    }

    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'titulo' => 'required|string|max:255',
                'resumen' => 'required|string|max:255',
                'provincia' => 'required|string|max:255',
                'contenido' => 'required|string',
                'imagen' => 'nullable|image',
                'video' => 'nullable|string|url'
            ]);

            if ($request->hasFile('imagen')) {
                $data['imagen'] = $request->file('imagen')->store('recorridos', 'public');
            }

            $recorrido = Recorrido::create($data);

            return response()->json(['success' => true, 'data' => $recorrido]);

        } catch (\Throwable $e) {
            \Log::error('Error al guardar recorrido: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error interno',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}

