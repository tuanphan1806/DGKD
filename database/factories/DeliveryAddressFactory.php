<?php

namespace Database\Factories;

use App\Models\DeliveryAddress;
use Illuminate\Database\Eloquent\Factories\Factory;

class DeliveryAddressFactory extends Factory
{
    protected $model = DeliveryAddress::class;

    public function definition()
    {
        return [
            'user_id'   => $this->faker->numberBetween(1, 50),
            'name'      => $this->faker->name,
            'address'   => $this->faker->streetAddress,
            'city'      => $this->faker->city,
            'state'     => $this->faker->state,
            'country'   => $this->faker->country,
            'zipcode'   => $this->faker->postcode,
            'mobile'    => $this->faker->numerify('0#########'),
            'status'    => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
