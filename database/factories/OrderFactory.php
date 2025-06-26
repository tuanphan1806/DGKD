<?php

namespace Database\Factories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition()
    {
        $paymentMethods = ['COD', 'Prepaid'];
        $paymentGateways = ['COD', 'Paypal', 'VNPay'];
        $orderStatuses = ['New', 'Pending', 'Processing', 'Completed', 'Cancelled'];

        return [
            'user_id'           => $this->faker->numberBetween(1, 50),
            'name'              => $this->faker->name,
            'address'           => $this->faker->streetAddress,
            'state'             => $this->faker->state,
            'city'              => $this->faker->city,
            'country'           => $this->faker->country,
            'zipcode'           => $this->faker->postcode,
            'mobile'            => $this->faker->numerify('0#########'),
            'email'             => $this->faker->safeEmail,
            'shipping_charges'  => $this->faker->randomFloat(2, 0, 50),
            'coupon_code'       => $this->faker->lexify('COUPON???'),
            'coupon_amount'     => $this->faker->randomFloat(2, 1, 100000),
            'order_status'      => $this->faker->randomElement($orderStatuses),
            'payment_method'    => $method = $this->faker->randomElement($paymentMethods),
            'payment_gateway'   => $method === 'COD' ? 'COD' : $this->faker->randomElement($paymentGateways),
            'grand_total'       => $this->faker->randomFloat(2, 10000, 500000),
            'courier_name'      => $this->faker->company,
            'tracking_number'   => $this->faker->bothify('TRK########'),
            'created_at'        => now(),
            'updated_at'        => now(),
        ];
    }
}
