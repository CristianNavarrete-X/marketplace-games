<?php

// Alumnos: Cristian Navarrete - Alejandro Ponce

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\OrderController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/health', function () {
    try {
        $tables = DB::select('SHOW TABLES');
        return response()->json([
            'status' => 'success',
            'message' => 'Base de datos MySQL conectada e inicializada correctamente',
            'database' => DB::connection()->getDatabaseName(),
            'tablas_detectadas' => array_map('current', $tables)
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage()
        ], 500);
    }
});

// Rutas API para el Marketplace
Route::prefix('api')->group(function () {
    // Categorías
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::post('/categories', [CategoryController::class, 'store']);
    Route::get('/categories/{id}', [CategoryController::class, 'show']);

    // Productos
    Route::get('/products', [ProductController::class, 'index']);
    Route::post('/products', [ProductController::class, 'store']);
    Route::get('/products/{id}', [ProductController::class, 'show']);

    // Pedidos / Órdenes
    Route::get('/orders', [OrderController::class, 'index']);
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);
});