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
        Schema::create('debts', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->decimal('original_amount_fcfa', 8, 2);
            $table->decimal('remaining_amount_fcfa', 8, 2);
            $table->enum('status', ['pending', 'paid', 'partial'])->default('pending');
            $table->foreignId('transaction_id')->constrained()->cascadeOnDelete();
$table->foreignId('farmer_id')->constrained()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('debts');
    }
};
