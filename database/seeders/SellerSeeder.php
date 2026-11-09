<?php

// Alumnos: Cristian Navarrete - Alejandro Ponce

namespace Database\Seeders;

use App\Models\Seller;
use Illuminate\Database\Seeder;

class SellerSeeder extends Seeder
{
    public function run(): void
    {
        Seller::create([
            'name' => 'Tienda Oficial Gamer',
            'email' => 'ventas@gamerstore.com',
            'store_name' => 'GamerStore HQ'
        ]);

        Seller::create([
            'name' => 'Pixel Digital',
            'email' => 'contacto@pixeldigital.com',
            'store_name' => 'Pixel Digital Keys'
        ]);
    }
}