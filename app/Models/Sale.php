<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    // Allow mass assignment for our simple sales form
    protected $guarded = [];

    // Optional but helpful: Let Laravel know a Sale belongs to a Product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}