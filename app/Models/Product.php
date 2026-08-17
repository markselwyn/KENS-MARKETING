<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    // ADDED REORDER_POINT, STATUS, AND PROMO_APPLIED_AT SO LARAVEL ALLOWS THEM TO UPDATE!
    protected $fillable = [
        'sku',
        'product_name',
        'category',
        'unit_price',
        'in_stock',
        'reorder_point', 
        'status',
        'promo_applied_at' // <--- ADDED THIS LINE
    ];

}
