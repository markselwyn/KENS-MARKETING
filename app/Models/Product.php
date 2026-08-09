<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Product extends Model
{
    use HasFactory, LogsActivity;

    // ADDED REORDER_POINT AND STATUS SO LARAVEL ALLOWS THEM TO UPDATE!
    protected $fillable = [
        'sku',
        'product_name',
        'category',
        'unit_price',
        'in_stock',
        'reorder_point', 
        'status'
    ];

    /**
     * This configures Spatie to automatically log changes to all fields 
     * and shows a clean note in the Admin dashboard.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll() // Track changes on all columns (sku, price, stock, etc.)
            ->logOnlyDirty() // Only log fields that actually changed
            ->setDescriptionForEvent(fn(string $eventName) => "Product has been {$eventName}");
    }
}