<?php

namespace Database\Factories;

use App\Models\Transaction;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'farmer_id'            => \App\Models\Farmer::factory(),
            'operator_id'          => \App\Models\User::factory()->create(['role' => \App\Enums\UserRole::Operator])->id,
            'total_price_fcfa'     => 10000,
            'payment_method'       => \App\Enums\PaymentMethod::Cash,
            'interest_rate'        => 0,
            'credited_amount_fcfa' => 0,
        ];
    }
}
