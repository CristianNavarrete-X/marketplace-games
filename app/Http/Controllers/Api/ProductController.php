<?php

// Alumnos: Cristian Navarrete - Alejandro Ponce

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        // Retorna solo productos con stock disponible y con sus relaciones
        $products = Product::with(['seller', 'category'])
            ->where('stock', '>', 0)
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $products
        ], 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'seller_id'   => 'required|exists:sellers,id',
            'category_id' => 'nullable|exists:categories,id',
            'title' => ['required', 'string', 'max:255', 'not_regex:/[<>]/'],
            'description' => 'nullable|string',
            'price'       => 'required|numeric|min:0.01',
            'stock'       => 'required|integer|min:0',
        ]);

        $product = Product::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Producto publicado con éxito',
            'data' => $product
        ], 201);
    }

    public function show($id)
    {
        $product = Product::with(['seller', 'category'])->find($id);

        if (!$product) {
            return response()->json([
                'status' => 'error',
                'message' => 'Producto no encontrado'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $product
        ], 200);
    }

    
    // Actualizar la información de un producto
    public function update(Request $request, $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'status' => 'error',
                'message' => 'Producto no encontrado'
            ], 404);
        }

        $validated = $request->validate([
            'seller_id'   => 'sometimes|required|exists:sellers,id',
            'category_id' => 'sometimes|nullable|exists:categories,id',
            'title' => ['sometimes', 'required', 'string', 'max:255', 'not_regex:/[<>]/'],
            'description' => 'sometimes|nullable|string',
            'price'       => 'sometimes|required|numeric|min:0.01',
            'stock'       => 'sometimes|required|integer|min:0',
        ]);

        $product->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Producto actualizado correctamente',
            'data' => $product->fresh()
        ], 200);
    }

    

}