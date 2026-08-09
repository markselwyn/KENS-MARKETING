<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('sku')->unique(); // Ensures no duplicate SKUs
            $table->string('product_name');
            $table->string('category');
            $table->decimal('unit_price', 10, 2); // Handles prices up to ₱99,999,999.99
            $table->integer('in_stock')->default(0);
            $table->integer('reorder_point')->default(5);
            $table->string('status')->default('Healthy'); // Healthy, Low Stock, or Out of Stock
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
