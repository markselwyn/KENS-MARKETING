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
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            // Links the sale directly to the specific item in the inventory
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            
            // What happened?
            $table->integer('quantity_sold');
            $table->decimal('total_amount', 10, 2);
            
            // Laravel's timestamps will automatically record the exact Date and Time of the sale!
            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
