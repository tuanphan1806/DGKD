<?php

namespace Database\Factories;

use App\Models\Vendor;
use Illuminate\Database\Eloquent\Factories\Factory;

class VendorFactory extends Factory
{
    protected $model = Vendor::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->company(),
            'address' => $this->faker->streetAddress(),
            'city' => $this->faker->city(),
            'state' => $this->faker->state(),
            'country' => $this->faker->country(),
            'zipcode' => $this->faker->postcode(),
            'mobile' => $this->faker->phoneNumber(),
            'email' => $this->faker->unique()->safeEmail(),
            // 'confirm' => $this->faker->randomElement(['Yes', 'No']),
            // 'commission' => $this->faker->randomFloat(2, 0, 50),
            'status' => $this->faker->numberBetween(0, 1),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
