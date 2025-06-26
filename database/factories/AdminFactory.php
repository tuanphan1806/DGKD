<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class AdminFactory extends Factory
{
    public function definition(): array
    {
        return [
            'id' => $this->faker->unique()->randomNumber(5, true),
            'name' => $this->faker->name,
            'type' => $this->faker->randomElement(['admin', 'vendor', 'superadmin']),
            'vendor_id' => $this->faker->numberBetween(1, 100),
            'mobile' => $this->faker->phoneNumber,
            'email' => $this->faker->unique()->safeEmail,
            'password' => Hash::make('password'), // hoặc bcrypt('password')
            'image' => $this->faker->imageUrl(200, 200, 'people', true),
            // 'confirm' => $this->faker->randomElement(['Yes', 'No']),
            'status' => $this->faker->numberBetween(0, 1),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
