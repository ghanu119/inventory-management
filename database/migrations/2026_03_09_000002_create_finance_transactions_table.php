<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('finance_transactions', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->enum('type', ['income', 'expense', 'transfer']);
            $table->decimal('amount', 14, 2);
            $table->enum('account_from', ['cash', 'bank'])->nullable();
            $table->enum('account_to', ['cash', 'bank'])->nullable();
            $table->text('note')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['date', 'type']);
            $table->index(['account_from', 'account_to']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('finance_transactions');
    }
};

