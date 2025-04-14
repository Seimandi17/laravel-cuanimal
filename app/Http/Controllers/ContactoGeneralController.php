<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactoAdmin;

class ContactoGeneralController extends Controller
{
    public function enviar(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'email' => 'required|email',
            'telefono' => 'nullable|string|max:20',
            'asunto' => 'required|string|max:255',
            'mensaje' => 'required|string',
        ]);

        // Asegurar compatibilidad con la plantilla del correo
        $validated['subject'] = $validated['asunto'];
        $validated['name'] = $validated['nombre'];
        $validated['phone'] = $validated['telefono'];
        $validated['message'] = $validated['mensaje'];
        $validated['asunto'] = 'Solicitud de cliente desde Cuanimal';

        // Enviar a la dirección configurada del administrador
        $adminEmail = env('MAIL_ADMIN_ADDRESS', 'admin@cuanimal.com');

        Mail::to($adminEmail)->send(new ContactoAdmin($validated));

        return response()->json([
            'status' => true,
            'message' => 'Mensaje enviado con éxito.'
        ]);
    }
}
