<?php

namespace Database\Factories;

use App\Models\VendorsBusinessDetail;
use App\Models\Vendor;
use Illuminate\Database\Eloquent\Factories\Factory;

class VendorsBusinessDetailFactory extends Factory
{
    protected $model = VendorsBusinessDetail::class;

    public function definition(): array
    {
        return [
            'vendor_id' => Vendor::factory(),
            'shop_name' => $this->faker->company(),
            'shop_address' => $this->faker->streetAddress(),
            'shop_city' => $this->faker->city(),
            'shop_state' => $this->faker->state(),
            'shop_country' => $this->faker->country(),
            'shop_zipcode' => $this->faker->postcode(),
            'shop_mobile' => $this->faker->phoneNumber(),
            'shop_website' => $this->faker->url(),
            'shop_email' => $this->faker->companyEmail(),
            'address_proof' => $this->faker->randomElement(['CCCD', 'SHK', 'Passport']),
            'address_proof_image' => $this->faker->imageUrl(300, 300, 'business'),
            'business_license_number' => $this->faker->numerify('########'),
            'gst_number' => $this->faker->numerify('############'),
            'pan-number' => $this->faker->bothify('????######'),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
