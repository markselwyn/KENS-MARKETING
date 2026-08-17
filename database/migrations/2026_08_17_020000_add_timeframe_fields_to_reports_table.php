<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->string('timeframe')->nullable()->after('report_type');
            $table->timestamp('period_start')->nullable()->after('timeframe');
            $table->timestamp('period_end')->nullable()->after('period_start');
        });
    }

    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropColumn(['timeframe', 'period_start', 'period_end']);
        });
    }
};
