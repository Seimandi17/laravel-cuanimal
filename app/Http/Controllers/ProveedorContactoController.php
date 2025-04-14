<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactoProveedorAdmin;

class ProveedorContactoController extends Controller
{
    public function enviar(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'email' => 'required|email',
            'telefono' => 'nullable|string|max:20',
            'servicio' => 'required|string|max:255',
            'ubicacion' => 'required|string|max:255',
            'mensaje' => 'required|string',
        ]);

        // Agregamos "asunto" si lo necesita la vista
        $validated['asunto'] = 'Solicitud de proveedor desde Cuanimal';

        // Enviar a la dirección configurada del administrador
        $adminEmail = env('MAIL_ADMIN_ADDRESS', 'admin@cuanimal.com');

        Mail::to($adminEmail)->send(new ContactoProveedorAdmin($validated));

        return response()->json([
            'status' => true,
            'message' => 'Mensaje enviado con éxito.'
        ]);
    }
}
