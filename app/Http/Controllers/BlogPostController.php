<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class BlogPostController extends Controller
{
    /**
     * Listar todos los posts del blog
     */
    public function index(Request $request)
    {
        try {
            $query = BlogPost::with('autor:id,name')->recientes();

            // Filtros opcionales
            if ($request->has('categoria')) {
                $query->categoria($request->categoria);
            }

            if ($request->has('activo')) {
                $query->where('activo', $request->activo);
            }

            if ($request->has('destacado')) {
                $query->destacado();
            }

            if ($request->has('busqueda')) {
                $search = $request->busqueda;
                $query->where(function($q) use ($search) {
                    $q->where('titulo', 'like', "%{$search}%")
                      ->orWhere('resumen', 'like', "%{$search}%")
                      ->orWhere('contenido', 'like', "%{$search}%");
                });
            }

            $posts = $query->get();

            return response()->json([
                'success' => true,
                'data' => $posts
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los posts del blog',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mostrar un post específico
     */
    public function show($id)
    {
        try {
            $post = BlogPost::with('autor:id,name')->findOrFail($id);
            
            // Incrementar vistas
            $post->incrementarVistas();

            return response()->json([
                'success' => true,
                'data' => $post
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Post no encontrado',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Crear un nuevo post
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'titulo' => 'required|string|max:255',
            'resumen' => 'nullable|string|max:500',
            'contenido' => 'required|string',
            'categoria' => 'required|in:Somos su Voz,Noticias,Humor,Plan Convive',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'destacado' => 'nullable|boolean',
            'activo' => 'nullable|boolean',
            'fecha_publicacion' => 'nullable|date',
        ], [
            'titulo.required' => 'El título es obligatorio',
            'contenido.required' => 'El contenido es obligatorio',
            'categoria.required' => 'La categoría es obligatoria',
            'categoria.in' => 'La categoría seleccionada no es válida',
            'imagen.image' => 'El archivo debe ser una imagen',
            'imagen.max' => 'La imagen no debe superar los 2MB',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $data = $request->all();
            $data['autor_id'] = auth()->id();

            // Manejar la subida de imagen
            if ($request->hasFile('imagen')) {
                $image = $request->file('imagen');
                $imageName = time() . '_' . Str::slug($request->titulo) . '.' . $image->getClientOriginalExtension();
                $imagePath = $image->storeAs('blog', $imageName, 'public');
                $data['imagen'] = $imagePath;
            }

            $post = BlogPost::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Post creado exitosamente',
                'data' => $post
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el post',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Actualizar un post existente
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'titulo' => 'sometimes|required|string|max:255',
            'resumen' => 'nullable|string|max:500',
            'contenido' => 'sometimes|required|string',
            'categoria' => 'sometimes|required|in:Somos su Voz,Noticias,Humor,Plan Convive',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'destacado' => 'nullable|boolean',
            'activo' => 'nullable|boolean',
            'fecha_publicacion' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $post = BlogPost::findOrFail($id);
            $data = $request->all();

            // Manejar la actualización de imagen
            if ($request->hasFile('imagen')) {
                // Eliminar imagen anterior si existe
                if ($post->imagen && Storage::disk('public')->exists($post->imagen)) {
                    Storage::disk('public')->delete($post->imagen);
                }

                $image = $request->file('imagen');
                $imageName = time() . '_' . Str::slug($request->titulo ?? $post->titulo) . '.' . $image->getClientOriginalExtension();
                $imagePath = $image->storeAs('blog', $imageName, 'public');
                $data['imagen'] = $imagePath;
            }

            $post->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Post actualizado exitosamente',
                'data' => $post
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el post',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar un post
     */
    public function destroy($id)
    {
        try {
            $post = BlogPost::findOrFail($id);

            // Eliminar imagen asociada si existe
            if ($post->imagen && Storage::disk('public')->exists($post->imagen)) {
                Storage::disk('public')->delete($post->imagen);
            }

            $post->delete();

            return response()->json([
                'success' => true,
                'message' => 'Post eliminado exitosamente'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el post',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Subir/actualizar imagen de un post
     */
    public function uploadImage(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'imagen' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $post = BlogPost::findOrFail($id);

            if ($request->hasFile('imagen')) {
                // Eliminar imagen anterior
                if ($post->imagen && Storage::disk('public')->exists($post->imagen)) {
                    Storage::disk('public')->delete($post->imagen);
                }

                $image = $request->file('imagen');
                $imageName = time() . '_' . Str::slug($post->titulo) . '.' . $image->getClientOriginalExtension();
                $imagePath = $image->storeAs('blog', $imageName, 'public');
                
                $post->imagen = $imagePath;
                $post->save();

                return response()->json([
                    'success' => true,
                    'message' => 'Imagen subida exitosamente',
                    'data' => [
                        'imagen' => $imagePath,
                        'url' => Storage::url($imagePath)
                    ]
                ], 200);
            }

            return response()->json([
                'success' => false,
                'message' => 'No se proporcionó ninguna imagen'
            ], 400);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al subir la imagen',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener categorías disponibles
     */
    public function categorias()
    {
        $categorias = [
            'Somos su Voz',
            'Noticias',
            'Humor',
            'Plan Convive'
        ];

        return response()->json([
            'success' => true,
            'data' => $categorias
        ], 200);
    }

    /**
     * Obtener posts por categoría (para frontend público)
     */
    public function porCategoria($categoria)
    {
        try {
            $posts = BlogPost::activo()
                ->categoria($categoria)
                ->recientes()
                ->get();

            return response()->json([
                'success' => true,
                'data' => $posts
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los posts',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

