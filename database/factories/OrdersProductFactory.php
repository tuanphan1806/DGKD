<?php

namespace Database\Factories;

use App\Models\OrdersProduct;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrdersProductFactory extends Factory
{
    protected $model = OrdersProduct::class;

    public function definition()
    {
        $statuses = ['Pending', 'In Process', 'Shipped', 'Delivered', 'Cancelled', null];

        return [
            'order_id'         => $this->faker->numberBetween(1, 50),
            'user_id'          => $this->faker->numberBetween(1, 30),
            'vendor_id'        => $this->faker->numberBetween(0, 5),
            'admin_id'         => 1,
            'product_id'       => $this->faker->numberBetween(1, 20),
            'product_code'     => 'AS' . $this->faker->unique()->numerify('##'),
            'product_name'     => 'Asus 128GB',
            'product_color'    => $this->faker->safeColorName(),
            'product_size'     => $this->faker->randomElement(['128GB', '256GB', '512GB']),
            'product_price'    => $this->faker->randomFloat(0, 10000, 20000),
            'product_qty'      => $this->faker->numberBetween(1, 5),
            // 'item_status'      => $this->faker->randomElement($statuses),
            // 'courier_name'     => $this->faker->randomElement(['GHN', 'GHTK', 'VNPost', 'J&T']),
            // 'tracking_number'  => $this->faker->numerify('########'),
            'created_at'       => now(),
            'updated_at'       => now(),
        ];
    }
}
