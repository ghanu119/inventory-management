<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoice_items', function (Blueprint $table) {
            $table->decimal('warranty_years', 4, 2)->nullable()->after('serial_no');
            $table->text('custom_short_text')->nullable()->after('warranty_years');
        });
    }

    public function down(): void
    {
        Schema::table('invoice_items', function (Blueprint $table) {
            $table->dropColumn(['warranty_years', 'custom_short_text']);
        });
    }
};
