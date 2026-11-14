<?php

// Alumnos: Cristian Navarrete - Alejandro Ponce

namespace Database\Seeders;

use App\Models\Seller;
use Illuminate\Database\Seeder;

class SellerSeeder extends Seeder
{
    public function run(): void
    {
        Seller::updateOrCreate(
            ['store_name' => 'GameStop'],
            [
                'name' => 'GameStop',
                'email' => 'gamestop@example.com',
                'store_name' => 'GameStop'
            ]
        );

        Seller::updateOrCreate(
            ['store_name' => 'Best Buy'],
            [
                'name' => 'Best Buy',
                'email' => 'bestbuy@example.com',
                'store_name' => 'Best Buy'
            ]
        );
    }
}
