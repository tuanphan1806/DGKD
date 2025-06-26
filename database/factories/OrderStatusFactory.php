<?php

namespace Database\Factories;

use App\Models\OrderStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderStatusFactory extends Factory
{
    protected $model = OrderStatus::class;

    public function definition()
    {
        return [
            'name'       => $this->faker->unique()->randomElement([
                'New',
                'Pending',
                'Cancelled',
                'In Process',
                'Shipped',
                'Partially Shipped',
                'Delivered',
                'Partially Delivered',
                'Paid',
            ]),
            'status'     => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
