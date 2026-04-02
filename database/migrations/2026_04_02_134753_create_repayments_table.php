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
        Schema::create('repayments', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->decimal('kg_received', 8, 2);
            $table->decimal('commodity_rate', 8, 2);
            $table->decimal('fcfa_value', 8, 2);
            $table->foreignId('farmer_id')->constrained()->cascadeOnDelete(); 
$table->foreignId('operator_id')->constrained('users')->cascadeOnDelete(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('repayments');
    }
};
