<?php

// Alumnos: Cristian Navarrete - Alejandro Ponce

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\OrderController;

// Categorías
Route::get('/categories', [CategoryController::class, 'index']);
Route::post('/categories', [CategoryController::class, 'store']);
Route::get('/categories/{id}', [CategoryController::class, 'show'])->whereNumber('id');

// Productos
Route::get('/products', [ProductController::class, 'index']);
Route::post('/products', [ProductController::class, 'store']);
Route::get('/products/{id}', [ProductController::class, 'show'])->whereNumber('id');
Route::put('/products/{id}', [ProductController::class, 'update'])->whereNumber('id');
Route::patch('/products/{id}', [ProductController::class, 'update'])->whereNumber('id');

// Pedidos
Route::get('/orders', [OrderController::class, 'index']);
Route::post('/orders', [OrderController::class, 'store']);
Route::get('/orders/{id}', [OrderController::class, 'show'])->whereNumber('id');
Route::put('/orders/{id}', [OrderController::class, 'update'])->whereNumber('id');
Route::patch('/orders/{id}', [OrderController::class, 'update'])->whereNumber('id');
