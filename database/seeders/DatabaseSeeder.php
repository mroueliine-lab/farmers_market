<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
USE App\Models\Setting;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'role' => 'admin',
        ]);

        Setting::create([
            'key' => 'interest_rate',
            'value' => '30', // 30% interest rate for credit transactions,
        ]);

        Setting::create([
            'key' => 'commodity_rate',
            'value' => '1000', // 1 kg 1000 fcfa
        ]);
    }
}
