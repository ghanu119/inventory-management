<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->decimal('opening_cash_balance', 14, 2)->default(0)->after('invoice_terms_and_conditions');
            $table->decimal('opening_bank_balance', 14, 2)->default(0)->after('opening_cash_balance');
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn(['opening_cash_balance', 'opening_bank_balance']);
        });
    }
};

