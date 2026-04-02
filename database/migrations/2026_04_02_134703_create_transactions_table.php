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
            $table->timestamps();
            $table->decimal('total_price_fcfa', 8, 2);
            $table->enum('payment_method', ['cash', 'credit']);
            $table->decimal('interest_rate', 5, 2)->default(0);
            $table->decimal('credited_amount_fcfa', 8, 2)->default(0);
            $table->foreignId('farmer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('operator_id')->constrained('users')->cascadeOnDelete();});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
