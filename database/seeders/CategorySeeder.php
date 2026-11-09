<?php

// Alumnos: Cristian Navarrete - Alejandro Ponce

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'PC Games', 'slug' => 'pc-games'],
            ['name' => 'PlayStation 5', 'slug' => 'ps5'],
            ['name' => 'Xbox Series X/S', 'slug' => 'xbox-series'],
            ['name' => 'Nintendo Switch', 'slug' => 'nintendo-switch'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}