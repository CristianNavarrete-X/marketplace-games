<?php

// Alumnos: Cristian Navarrete - Alejandro Ponce

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        Product::create([
            'seller_id' => 1,
            'category_id' => 1,
            'title' => 'Elden Ring',
            'description' => 'Juego de acción y RPG de mundo abierto.',
            'price' => 59.99,
            'stock' => 15
        ]);

        Product::create([
            'seller_id' => 1,
            'category_id' => 2,
            'title' => 'Cyberpunk 2077',
            'description' => 'RPG de aventura en Night City.',
            'price' => 49.99,
            'stock' => 20
        ]);

        Product::create([
            'seller_id' => 2,
            'category_id' => 4,
            'title' => 'The Legend of Zelda: Tears of the Kingdom',
            'description' => 'Aventura épica para Nintendo Switch.',
            'price' => 69.99,
            'stock' => 10
        ]);
    }
}