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
            $query = BlogPost::orderBy('created_at', 'desc');

            // Filtros opcionales
            if ($request->has('categoria')) {
                $query->where('categoria', $request->categoria);
            }

            if ($request->has('activo')) {
                $query->where('activo', $request->activo);
            }

            if ($request->has('es_destacado')) {
                $query->where('es_destacado', true);
            }

            if ($request->has('es_publicado')) {
                $query->where('es_publicado', true);
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

            return response()->json($posts, 200);

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
            $post = BlogPost::findOrFail($id);
            
            // Incrementar vistas
            $post->incrementarVistas();

            return response()->json($post, 200);

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
            'resumen' => 'required|string',
            'contenido' => 'required|string',
            'categoria' => 'required|in:Somos su Voz,Noticias,Humor,Plan Convive',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
            'es_destacado' => 'nullable|boolean',
            'es_publicado' => 'nullable|boolean',
            'activo' => 'nullable|boolean',
        ], [
            'titulo.required' => 'El título es obligatorio',
            'resumen.required' => 'El resumen es obligatorio',
            'contenido.required' => 'El contenido es obligatorio',
            'categoria.required' => 'La categoría es obligatoria',
            'categoria.in' => 'La categoría seleccionada no es válida',
            'imagen.image' => 'El archivo debe ser una imagen',
            'imagen.max' => 'La imagen no debe superar los 10MB',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Generar slug automáticamente desde el título
            $slug = Str::slug($request->titulo);
            
            // Asegurar que el slug sea único
            $count = BlogPost::where('slug', 'like', $slug . '%')->count();
            if ($count > 0) {
                $slug = $slug . '-' . ($count + 1);
            }
            
            $data = [
                'titulo' => $request->titulo,
                'slug' => $slug,
                'resumen' => $request->resumen,
                'contenido' => $request->contenido,
                'categoria' => $request->categoria,
                'es_destacado' => $request->boolean('es_destacado', false),
                'es_publicado' => $request->boolean('es_publicado', false),
                'activo' => $request->boolean('activo', true),
                'vistas' => 0,
            ];

            // Manejar la subida de imagen
            if ($request->hasFile('imagen')) {
                $image = $request->file('imagen');
                $imageName = 'blog_' . time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
                $imagePath = $image->storeAs('blog-images', $imageName, 'public');
                // Construir URL completa usando la URL base de la aplicación
                $baseUrl = rtrim(config('app.url'), '/');
                $data['imagen_url'] = $baseUrl . '/storage/' . $imagePath;
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
            'resumen' => 'sometimes|required|string',
            'contenido' => 'sometimes|required|string',
            'categoria' => 'sometimes|required|in:Somos su Voz,Noticias,Humor,Plan Convive',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
            'es_destacado' => 'nullable|boolean',
            'es_publicado' => 'nullable|boolean',
            'activo' => 'nullable|boolean',
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
            $data = [];

            if ($request->has('titulo')) {
                $data['titulo'] = $request->titulo;
                // Regenerar slug si el título cambió
                $slug = Str::slug($request->titulo);
                $count = BlogPost::where('slug', 'like', $slug . '%')->where('id', '!=', $id)->count();
                if ($count > 0) {
                    $slug = $slug . '-' . ($count + 1);
                }
                $data['slug'] = $slug;
            }
            if ($request->has('resumen')) $data['resumen'] = $request->resumen;
            if ($request->has('contenido')) $data['contenido'] = $request->contenido;
            if ($request->has('categoria')) $data['categoria'] = $request->categoria;
            if ($request->has('es_destacado')) $data['es_destacado'] = $request->boolean('es_destacado');
            if ($request->has('es_publicado')) $data['es_publicado'] = $request->boolean('es_publicado');
            if ($request->has('activo')) $data['activo'] = $request->boolean('activo');

            // Manejar la actualización de imagen
            if ($request->hasFile('imagen')) {
                // Eliminar imagen anterior si existe
                if ($post->imagen_url) {
                    $oldPath = str_replace('/storage/', '', parse_url($post->imagen_url, PHP_URL_PATH));
                    if (Storage::disk('public')->exists($oldPath)) {
                        Storage::disk('public')->delete($oldPath);
                    }
                }

                $image = $request->file('imagen');
                $imageName = 'blog_' . time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
                $imagePath = $image->storeAs('blog-images', $imageName, 'public');
                // Construir URL completa usando la URL base de la aplicación
                $baseUrl = rtrim(config('app.url'), '/');
                $data['imagen_url'] = $baseUrl . '/storage/' . $imagePath;
            }

            $post->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Post actualizado exitosamente',
                'data' => $post->fresh()
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
            if ($post->imagen_url) {
                $oldPath = str_replace('/storage/', '', parse_url($post->imagen_url, PHP_URL_PATH));
                if (Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
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

