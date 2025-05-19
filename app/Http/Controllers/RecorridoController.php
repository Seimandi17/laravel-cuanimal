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
        $data = $request->validate([
            'titulo' => 'required|string|max:255',
            'resumen' => 'required|string|max:255',
            'provincia' => 'required|string|max:255',
            'contenido' => 'required|string',
            'imagen' => 'nullable|image',
            'video' => 'nullable|file|mimes:mp4,avi,mov|max:20480'
        ]);

        if ($request->hasFile('imagen')) {
            $data['imagen'] = $request->file('imagen')->store('recorridos', 'public');
        }

        if ($request->hasFile('video')) {
            $data['video'] = $request->file('video')->store('recorridos', 'public');
        }

        $recorrido = Recorrido::create($data);

        return response()->json(['success' => true, 'data' => $recorrido]);
    }
}
