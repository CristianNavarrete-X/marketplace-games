<?php

// Alumnos: Cristian Navarrete - Alejandro Ponce

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    // Consultar todos los pedidos
    public function index()
    {
        $orders = Order::with('products')->get();

        return response()->json([
            'status' => 'success',
            'data' => $orders
        ], 200);
    }

    // Crear un pedido
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id|distinct',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        // Procesar la compra en una transacción
        $order = DB::transaction(function () use ($validated) {
            $totalAmount = 0;
            $itemsToAttach = [];

            foreach ($validated['items'] as $item) {

                // Bloquear el producto mientras se procesa
                $product = Product::lockForUpdate()
                    ->findOrFail($item['product_id']);

                // Validar existencias
                if ($product->stock < $item['quantity']) {
                    throw ValidationException::withMessages([
                        'items' => "Stock insuficiente para {$product->title}"
                    ]);
                }

                // Descontar el inventario
                $product->decrement('stock', $item['quantity']);

                // Calcular el subtotal
                $subtotal = $product->price * $item['quantity'];
                $totalAmount += $subtotal;

                $itemsToAttach[$product->id] = [
                    'quantity' => $item['quantity'],
                    'unit_price' => $product->price
                ];
            }

            // Guardar el pedido
            $order = Order::create([
                'customer_name' => $validated['customer_name'],
                'customer_email' => $validated['customer_email'],
                'total_amount' => $totalAmount,
                'status' => 'completed'
            ]);

            // Relacionar productos con el pedido
            $order->products()->attach($itemsToAttach);

            return $order;
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Pedido realizado correctamente',
            'data' => $order->load('products')
        ], 201);
    }

    // Consultar un pedido
    public function show($id)
    {
        $order = Order::with('products')->find($id);

        if (!$order) {
            return response()->json([
                'status' => 'error',
                'message' => 'Pedido no encontrado'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $order
        ], 200);
    }

    // Actualizar el estado de un pedido
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,completed,cancelled'
        ]);

        $result = DB::transaction(function () use ($id, $validated) {

            // Buscar y bloquear el pedido
            $order = Order::lockForUpdate()->find($id);

            if (!$order) {
                return null;
            }

            $newStatus = $validated['status'];

            // Evitar cambios en pedidos cancelados
            if ($order->status === 'cancelled' && $newStatus !== 'cancelled') {
                throw ValidationException::withMessages([
                    'status' => 'No se puede reactivar un pedido cancelado'
                ]);
            }

            // Evitar regresar un pedido completado a pendiente
            if ($order->status === 'completed' && $newStatus === 'pending') {
                throw ValidationException::withMessages([
                    'status' => 'Un pedido completado no puede volver a pendiente'
                ]);
            }

            // Devolver existencias al cancelar
            if ($newStatus === 'cancelled' && $order->status !== 'cancelled') {

                foreach ($order->products as $product) {
                    Product::whereKey($product->id)
                        ->increment('stock', $product->pivot->quantity);
                }
            }

            // Guardar el nuevo estado
            $order->update([
                'status' => $newStatus
            ]);

            return $order->load('products');
        });

        if (!$result) {
            return response()->json([
                'status' => 'error',
                'message' => 'Pedido no encontrado'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Estado del pedido actualizado correctamente',
            'data' => $result
        ], 200);
    }
}
