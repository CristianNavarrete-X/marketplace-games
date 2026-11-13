<?php

// Alumnos: Cristian Navarrete - Alejandro Ponce

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

// Página principal
Route::get('/', function () {
    return view('welcome');
});

// Comprobar la conexión con MySQL
Route::get('/health', function () {
    try {
        $tables = DB::select('SHOW TABLES');

        return response()->json([
            'status' => 'success',
            'message' => 'Base de datos conectada correctamente',
            'database' => DB::connection()->getDatabaseName(),
            'tablas_detectadas' => array_map('current', $tables)
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Error al conectar con la base de datos'
        ], 500);
    }
});
