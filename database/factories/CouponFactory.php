<?php

namespace Database\Factories;

use App\Models\Coupon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CouponFactory extends Factory
{
    protected $model = Coupon::class;

    public function definition()
    {
        return [
            'vendor_id'     => $this->faker->numberBetween(0, 50),
            'coupon_option' => $this->faker->randomElement(['Automatic', 'Manual']),
            'coupon_code'   => strtoupper(Str::random(8)),
            'categories'    => implode(',', $this->faker->randomElements(range(1, 20), rand(2, 6))),
            // 'brands'        => implode(',', $this->faker->randomElements(range(1, 5), rand(1, 3))),
            'users'         => implode(',', $this->faker->unique()->randomElements([
                'user1@test.com', 'test1@test.com', 'test2@test.com', 'test3@test.com', 'admin@test.com'
            ], rand(1, 3))),
            'coupon_type'   => $this->faker->randomElement(['Single Time', 'Multiple Times']),
            'amount_type'   => $this->faker->randomElement(['Fixed', 'Percentage']),
            'amount'        => $this->faker->randomFloat(2, 5, 50000),
            'expiry_date'   => $this->faker->dateTimeBetween('now', '+6 months')->format('Y-m-d'),
            'status'        => $this->faker->boolean,
            'created_at'    => now(),
            'updated_at'    => now(),
        ];
    }
}
