<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactServiceProvider;

class MessageController extends Controller
{
    public function sendMessage(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email',
            'phone'   => 'nullable|string|max:20',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'file'    => 'nullable|file|max:2048',
            'to'      => 'required|email', // el correo del proveedor
        ]);

        $data = $request->only(['name', 'email', 'phone', 'subject', 'message']);

        // Si se adjunta archivo
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            Mail::to($request->to)->send(new ContactServiceProvider($data, $file));
        } else {
            Mail::to($request->to)->send(new ContactServiceProvider($data));
        }

        return response()->json([
            'status' => true,
            'message' => 'Mensaje enviado correctamente.'
        ]);
    }
}
