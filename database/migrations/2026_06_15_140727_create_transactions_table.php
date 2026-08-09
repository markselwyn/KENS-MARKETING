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
    Schema::create('transactions', function (Blueprint $table) {
        $table->id();
        // A unique, readable ID like TRX-0092
        $table->string('transaction_code')->unique(); 
        
        $table->string('customer_name');
        $table->string('location');
        
        // Storing money as a decimal (10 digits total, 2 after the decimal)
        $table->decimal('amount', 10, 2); 
        
        // Automatically adds 'created_at' (for the date) and 'updated_at'
        $table->timestamps(); 
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
