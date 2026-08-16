<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('system_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('action'); // e.g., 'Recorded Sale', 'Restocked Inventory'
            $table->text('description'); // e.g., 'John sold 2x Desk Fans for ₱1,200'
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('system_logs');
    }
};