<?php

namespace App\Http\Controllers;

use App\Models\Products;
use Illuminate\Http\Request;
use App\Models\Proveedor;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Storage;

class ProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $query = Products::query();
    
            // Filtro: experiencia de viaje
            if ($request->filled('experiencia_viaje')) {
                $query->where('experiencia_viaje', $request->experiencia_viaje);
            }
    
            // Filtro: categoría (solo para explorer, no para "Viajar con ellos")
            if ($request->filled('categoria')) {
                $query->where('category', $request->categoria);
            }
            if ($request->filled('codigo_postal')) {
                $query->where('codigo_postal', $request->codigo_postal);
            }
            // Filtro: provincia
            if ($request->filled('provincia')) {
                $query->where('province', $request->provincia);
            }
    
            // Filtro: tipo de mascota
            if ($request->filled('mascotas')) {
                $query->where('pet', $request->mascotas);
            }
    
            $products = $query->with('provider')->get();
    
            return response()->json([
                'data' => $products,
                'status' => true,
                'message' => 'Productos obtenidos con éxito',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'data' => [],
                'status' => false,
                'message' => 'Error al obtener los productos: ' . $e->getMessage(),
            ], 500);
        }
    }
    

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $user = Auth::user();
    
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'required|string',
                'price' => 'required|numeric|min:0',
                'contact' => 'nullable|string',
                'codigo_postal' => 'nullable|string|max:20',
                'facebook' => 'nullable|url',
                'instagram' => 'nullable|url',
                'x' => 'nullable|url',
                'linkedin' => 'nullable|url',
                'coverImg' => 'required|image|mimes:jpeg,png,jpg,gif,avif,webp',
                'extraImg' => 'nullable|image|mimes:jpeg,png,jpg,gif,avif,webp',
                'province' => 'required|string',
                'pet' => 'required|in:perros,gatos,ambos',
                'category' => 'required|string|max:255',
                'status' => 'nullable|string',
                'address' => 'required|string',
                'city' => 'required|string',
                'experiencia_viaje' => 'nullable|string|max:255',
                'from_province' => 'nullable|string|max:255',
                'to_province' => 'nullable|string|max:255',
            ]);
            

            if ($request->category === 'Alojamiento') {
                $request->validate([
                    'subcategoria_alojamiento' => 'required|string|max:255',
                ]);
                $validated['category'] = 'Alojamiento - ' . $request->subcategoria_alojamiento;
            }
    
            $data = $validated;
    
            $provider = Proveedor::where('user_id', $user->id)->first();
    
            if (!$provider) {
                return response()->json(['error' => 'Proveedor no encontrado para este usuario'], 404);
            }
    
            $data['provider_id'] = $provider->id;
    
            if ($request->hasFile('coverImg')) {
                $path = $request->file('coverImg')->store('products', 'public');
                $data['coverImg'] = $path;
            }
    
            if ($request->hasFile('extraImg')) {
                $path = $request->file('extraImg')->store('products', 'public');
                $data['extraImg'] = $path;
            }
    
            $product = Products::create($data);
    
            return response()->json([
                'data' => $product,
                'status' => true,
                'message' => 'Producto creado con éxito',
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'data' => [],
                'status' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'data' => [],
                'status' => false,
                'message' => 'Error al crear el producto: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $product = Products::with('provider.user')->findOrFail($id);
    

    
            return response()->json([
                'data' => $product,
                'status' => true,
                'message' => 'Producto encontrado con éxito',
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'data' => [],
                'status' => false,
                'message' => 'Producto no encontrado',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'data' => [],
                'status' => false,
                'message' => 'Error al obtener el producto: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $validated = $request->validate([
                'name' => 'sometimes|required|string|max:255',
                'description' => 'sometimes|required|string',
                'price' => 'sometimes|required|numeric|min:0',
                'contact' => 'sometimes|required|string',
                'codigo_postal' => 'sometimes|nullable|string|max:20',
                'facebook' => 'sometimes|nullable|url',
                'instagram' => 'sometimes|nullable|url',
                'x' => 'sometimes|nullable|url',
                'linkedin' => 'sometimes|nullable|url',
                'coverImg' => 'sometimes|image|mimes:jpeg,png,jpg,gif,avif,webp|max:2048',
                'extraImg' => 'sometimes|nullable|image|mimes:jpeg,png,jpg,gif,avif,webp|max:2048',
                'province' => 'sometimes|required|string',
                'pet' => 'sometimes|in:perros,gatos,ambos',
                'address' => 'sometimes|required|string',
                'city' => 'sometimes|required|string',
                'category' => 'sometimes|required|string|max:255',
                'experiencia_viaje' => 'nullable|string|max:255', 
                'from_province' => 'nullable|string|max:255',    
                'to_province' => 'nullable|string|max:255',
            ]);
    
            $product = Products::findOrFail($id);
    
            $data = $validated;
    
            if ($request->hasFile('coverImg')) {
                if ($product->coverImg) {
                    Storage::disk('public')->delete($product->coverImg);
                }
                $path = $request->file('coverImg')->store('products', 'public');
                $data['coverImg'] = $path;
            }
    
            if ($request->hasFile('extraImg')) {
                if ($product->extraImg) {
                    Storage::disk('public')->delete($product->extraImg);
                }
                $path = $request->file('extraImg')->store('products', 'public');
                $data['extraImg'] = $path;
            }
    
            $product->update($data);
    
            return response()->json([
                'data' => $product,
                'status' => true,
                'message' => 'Producto actualizado con éxito',
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'data' => [],
                'status' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'data' => [],
                'status' => false,
                'message' => 'Error al actualizar el producto: ' . $e->getMessage(),
            ], 500);
        }
    }
    

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $product = Products::findOrFail($id);

            if ($product->coverImg) {
                Storage::disk('public')->delete($product->coverImg);
            }
            if ($product->extraImg) {
                Storage::disk('public')->delete($product->extraImg);
            }

            $product->delete();

            return response()->json([
                'data' => [],
                'status' => true,
                'message' => 'Producto eliminado con éxito',
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'data' => [],
                'status' => false,
                'message' => 'Producto no encontrado',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'data' => [],
                'status' => false,
                'message' => 'Error al eliminar el producto: ' . $e->getMessage(),
            ], 500);
        }
    }
}