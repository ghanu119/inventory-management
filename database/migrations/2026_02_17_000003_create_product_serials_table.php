<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_serials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string('serial_number', 100);
            $table->enum('status', ['available', 'sold'])->default('available');
            $table->foreignId('invoice_item_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamps();

            $table->unique(['product_id', 'serial_number']);
            $table->index(['product_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_serials');
    }
};
