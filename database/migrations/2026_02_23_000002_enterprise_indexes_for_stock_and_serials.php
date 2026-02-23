<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Indexes for enterprise-scale: faster history lists and serial search.
     */
    public function up(): void
    {
        Schema::table('stock_histories', function (Blueprint $table) {
            $table->index('created_at');
        });

        Schema::table('product_serials', function (Blueprint $table) {
            $table->index('serial_number');
        });
    }

    public function down(): void
    {
        Schema::table('stock_histories', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
        });

        Schema::table('product_serials', function (Blueprint $table) {
            $table->dropIndex(['serial_number']);
        });
    }
};
