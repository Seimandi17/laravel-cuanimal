<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Conversation;
use App\Models\SupportMessage;
use Illuminate\Support\Facades\Auth;

class ConversationController extends Controller
{
    // ✅ Obtener todas las conversaciones del proveedor autenticado
    public function index()
{
    try {
        $user = Auth::user();

        if ($user->role_id !== 2) {
            return response()->json(['message' => 'No autorizado.'], 403);
        }

        $conversations = Conversation::with('messages.sender')
            ->where('provider_id', $user->id)
            ->orderByDesc('created_at')
            ->get();

        return response()->json(['data' => $conversations]);
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Error al obtener conversaciones',
            'error' => $e->getMessage(),
            'linea' => $e->getLine(),
            'archivo' => $e->getFile()
        ], 500);
    }
}

    public function adminIndex()
    {
        try {
            $admin = Auth::user();
    
            if ($admin->role_id !== 1) {
                return response()->json(['message' => 'No autorizado.'], 403);
            }
    
            $convs = \App\Models\Conversation::with(['messages.sender', 'provider'])
		    ->orderByDesc('updated_at')
		    ->get();
            return response()->json(['data' => $convs]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener conversaciones',
                'error' => $e->getMessage(),         // 👈 Mostrará el mensaje de error real
                'linea' => $e->getLine(),            // 👈 Línea exactta del fallo
                'archivo' => $e->getFile()           // 👈 Archivo donde ocurre
            ], 500);
        }
    }
    
    

    // ✅ Crear nueva conversación
    public function store(Request $request)
    {
        $user = Auth::user();
        $admin = \App\Models\User::where('role_id', 1)->first(); // único admin

        if ($user->role_id !== 2) {
            return response()->json(['message' => 'No autorizado.'], 403);
        }

        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $conversation = Conversation::create([
            'provider_id' => $user->id,
            'admin_id' => $admin->id,
            'subject' => $request->subject,
        ]);
        $message = SupportMessage::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $user->id,
            'message' => $request->message,
        ]);

        return response()->json([
            'message' => 'Conversación creada.',
            'data' => $conversation->load('messages.sender'),
        ]);
    }

    // ✅ Ver una conversación y sus mensajes
    public function show($id)
    {
        $user = Auth::user();
        $conversation = Conversation::with('messages.sender')->findOrFail($id);

        if ($user->id !== $conversation->provider_id && $user->role_id !== 1) {
            return response()->json(['message' => 'No autorizado.'], 403);
        }

        return response()->json(['data' => $conversation]);
    }

    // ✅ Agregar mensaje a conversación existente
    public function sendMessage(Request $request, $id)
    {
        $user = Auth::user();
        $conversation = Conversation::findOrFail($id);

        if ($user->id !== $conversation->provider_id && $user->role_id !== 1) {
            return response()->json(['message' => 'No autorizado.'], 403);
        }

        $request->validate([
            'message' => 'required|string',
        ]);

        $message = SupportMessage::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $user->id,
            'message' => $request->message,
        ]);

        return response()->json([
            'message' => 'Mensaje enviado.',
            'data' => $message->load('sender'),
        ]);
    }

    // ✅ Eliminar conversación (solo proveedor)
    public function destroy($id)
    {
        $user = Auth::user();
        $conversation = Conversation::findOrFail($id);

        if ($user->id !== $conversation->provider_id) {
            return response()->json(['message' => 'No autorizado.'], 403);
        }

        $conversation->delete();

        return response()->json(['message' => 'Conversación eliminada.']);
    }
    public function unassigned()
    {
        $admin = Auth::user();
        if ($admin->role_id !== 1) return response()->json(['message' => 'No autorizado'], 403);

        $convs = Conversation::with('provider')
            ->whereNull('admin_id')
            ->orderByDesc('created_at')
            ->get();

        return response()->json(['data' => $convs]);
    }
    public function assign($id)
    {
        $admin = Auth::user();
        if ($admin->role_id !== 1) return response()->json(['message' => 'No autorizado'], 403);

        $conv = Conversation::where('id', $id)->whereNull('admin_id')->first();

        if (!$conv) {
            return response()->json(['message' => 'Conversación no encontrada o ya asignada'], 404);
        }

        $conv->admin_id = $admin->id;
        $conv->save();

        return response()->json(['message' => 'Conversación asignada con éxito']);
    }


}
