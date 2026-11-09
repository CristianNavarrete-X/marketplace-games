<?php

// Alumnos: Cristian Navarrete - Alejandro Ponce

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with('products')->get();

        return response()->json([
            'status' => 'success',
            'data' => $orders
        ], 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name'  => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'items'          => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity'   => 'required|integer|min:1',
        ]);

        try {
            // Usamos una transacción para garantizar integridad en el stock
            $order = DB::transaction(function () use ($validated) {
                $totalAmount = 0;
                $itemsToAttach = [];

                foreach ($validated['items'] as $item) {
                    $product = Product::findOrFail($item['product_id']);

                    // Regla de negocio: Validar stock disponible
                    if ($product->stock < $item['quantity']) {
                        throw new \Exception("Stock insuficiente para el producto: {$product->title}. Stock actual: {$product->stock}");
                    }

                    // Descontar inventario automáticamente
                    $product->decrement('stock', $item['quantity']);

                    $subtotal = $product->price * $item['quantity'];
                    $totalAmount += $subtotal;

                    $itemsToAttach[$product->id] = [
                        'quantity' => $item['quantity'],
                        'unit_price' => $product->price,
                    ];
                }

                // Crear la orden principal
                $order = Order::create([
                    'customer_name'  => $validated['customer_name'],
                    'customer_email' => $validated['customer_email'],
                    'total_amount'   => $totalAmount,
                    'status'         => 'completed',
                ]);

                // Asociar los productos en la tabla pivot order_items
                $order->products()->attach($itemsToAttach);

                return $order;
            });

            return response()->json([
                'status'  => 'success',
                'message' => 'Pedido realizado con éxito y stock actualizado',
                'data'    => $order->load('products')
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function show($id)
    {
        $order = Order::with('products')->find($id);

        if (!$order) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Pedido no encontrado'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data'   => $order
        ], 200);
    }
}