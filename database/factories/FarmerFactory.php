<?php

namespace Database\Factories;

use App\Models\Farmer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Farmer>
 */
class FarmerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'firstname'    => $this->faker->firstName(),
            'lastname'     => $this->faker->lastName(),
            'email'        => $this->faker->unique()->safeEmail(),
            'phone_number' => $this->faker->unique()->numerify('07########'),
            'credit_limit' => 100000,
            'identifier'   => $this->faker->unique()->bothify('FM-###'),
        ];
    }
}
