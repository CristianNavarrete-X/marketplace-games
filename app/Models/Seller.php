<?php

// Alumno: Cristian Navarrete - Alejandro Ponce

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seller extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'email', 'store_name'];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}