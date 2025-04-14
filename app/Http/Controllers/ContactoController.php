<?php

namespace App\Http\Controllers;

use App\Mail\ContactoAdmin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactoController extends Controller
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

        Mail::to(env('MAIL_ADMIN_ADDRESS'))->send(new ContactoAdmin($validated));

        return response()->json(['status' => true, 'message' => 'Mensaje enviado correctamente.']);
    }
}

