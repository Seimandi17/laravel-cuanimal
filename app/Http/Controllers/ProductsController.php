<?php

namespace App\Http\Controllers;

use App\Models\Products;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Storage;

class ProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $user = Auth::user();
    
            if ($user) {
                // Usuario autenticado
                $role = $user->role_id;
    
                switch ($role) {
                    case 1: // Admin
                        $products = Products::with('provider')->get();
                        break;
    
                    case 2: // Proveedor
                        $products = Products::where('provider_id', $user->id)->get();
                        break;
    
                    case 3: // Cliente
                    default:
                        $products = Products::all();
                        break;
                }
            } else {
                // Usuario NO autenticado (público)
                $products = Products::all();
            }
    
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
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'required|string',
                'price' => 'required|numeric|min:0',
                'contact' => 'nullable|string',
                'coverImg' => 'required|image|mimes:jpeg,png,jpg,gif',
                'extraImg' => 'nullable|image|mimes:jpeg,png,jpg,gif',
                'province' => 'required|string',
                'pet' => 'required|in:perros,gatos,ambos',
                'category' => 'required|string|max:255', 
                'status' => 'nullable|string',
                'address' => 'required|string',
                'city' => 'required|string',
                'provider_id' => 'required|exists:proveedors,id',
            ]);

            $data = $validated;

            if ($request->hasFile('coverImg')) {
                $path = $request->file('coverImg')->store('products', 'public');
                $data['coverImg'] = $path;
            }

            if ($request->hasFile('extraImg')) {
                $path = $request->file('extraImg')->store('products', 'public');
                $data['extraImg'] = $path;
            }

            $product = Products::create($data);

            $product->coverImg = Storage::url($product->coverImg);
            $product->extraImg = $product->extraImg ? Storage::url($product->extraImg) : null;

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
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() === '23000') {
                return response()->json([
                    'data' => [],
                    'status' => false,
                    'message' => 'El category_id proporcionado no existe en la tabla categories.',
                ], 422);
            }

            return response()->json([
                'data' => [],
                'status' => false,
                'message' => 'Error al crear el producto: ' . $e->getMessage(),
            ], 500);
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
            $product = Products::findOrFail($id);
            $product->coverImg = Storage::url($product->coverImg);
            $product->extraImg = $product->extraImg ? Storage::url($product->extraImg) : null;

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
                'coverImg' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
                'extraImg' => 'sometimes|nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'province' => 'sometimes|required|string',
                'address' => 'sometimes|required|string',
                'city' => 'sometimes|required|string',
                'category_id' => 'sometimes|required|exists:categories,id',
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

            $product->coverImg = Storage::url($product->coverImg);
            $product->extraImg = $product->extraImg ? Storage::url($product->extraImg) : null;

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