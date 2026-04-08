<?php

namespace Database\Factories;

use App\Models\Debt;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Debt>
 */
class DebtFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'farmer_id'             => \App\Models\Farmer::factory(),
            'transaction_id'        => \App\Models\Transaction::factory(),
            'original_amount_fcfa'  => 10000,
            'remaining_amount_fcfa' => 10000,
            'status'                => \App\Enums\DebtStatus::Pending,
        ];
    }
}
