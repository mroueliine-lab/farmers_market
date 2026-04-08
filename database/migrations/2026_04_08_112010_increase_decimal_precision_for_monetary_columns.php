<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up(): void
{
    Schema::table('farmers', function (Blueprint $table) {
        $table->decimal('credit_limit', 15, 2)->default(0)->change();
    });

    Schema::table('products', function (Blueprint $table) {
        $table->decimal('price_fcfa', 15, 2)->change();
    });

    Schema::table('transactions', function (Blueprint $table) {
        $table->decimal('total_price_fcfa', 15, 2)->change();
        $table->decimal('credited_amount_fcfa', 15, 2)->default(0)->change();
    });

    Schema::table('transaction_items', function (Blueprint $table) {
        $table->decimal('unit_price_fcfa', 15, 2)->change();
    });

    Schema::table('debts', function (Blueprint $table) {
        $table->decimal('original_amount_fcfa', 15, 2)->change();
        $table->decimal('remaining_amount_fcfa', 15, 2)->change();
    });

    Schema::table('repayments', function (Blueprint $table) {
        $table->decimal('commodity_rate', 15, 2)->change();
        $table->decimal('fcfa_value', 15, 2)->change();
    });
}

public function down(): void
{
    Schema::table('farmers', function (Blueprint $table) {
        $table->decimal('credit_limit', 8, 2)->default(0)->change();
    });

    Schema::table('products', function (Blueprint $table) {
        $table->decimal('price_fcfa', 10, 2)->change();
    });

    Schema::table('transactions', function (Blueprint $table) {
        $table->decimal('total_price_fcfa', 8, 2)->change();
        $table->decimal('credited_amount_fcfa', 8, 2)->default(0)->change();
    });

    Schema::table('transaction_items', function (Blueprint $table) {
        $table->decimal('unit_price_fcfa', 10, 2)->change();
    });

    Schema::table('debts', function (Blueprint $table) {
        $table->decimal('original_amount_fcfa', 8, 2)->change();
        $table->decimal('remaining_amount_fcfa', 8, 2)->change();
    });

    Schema::table('repayments', function (Blueprint $table) {
        $table->decimal('commodity_rate', 8, 2)->change();
        $table->decimal('fcfa_value', 8, 2)->change();
    });
}

};
