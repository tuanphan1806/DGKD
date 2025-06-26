<?php

namespace Tests\Feature\Front;

use App\Models\User;
use App\Models\DeliveryAddress;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AddressControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Tạo user và đăng nhập
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    public function test_get_delivery_address()
    {
        $address = DeliveryAddress::factory()->create([
            'user_id' => $this->user->id,
        ]);
        $response = $this->actingAs($this->user)
            ->withHeaders(['X-Requested-With' => 'XMLHttpRequest'])
            ->postJson('/get-delivery-address', [
                'addressid' => $address->id,
            ]);

        $response->assertStatus(200)
            ->assertJsonFragment(['id' => $address->id]);
    }

    public function test_update_delivery_address()
    {
        $address = DeliveryAddress::factory()->create([
            'user_id' => $this->user->id,
            'name' => 'Old Name'
        ]);
        $response = $this->actingAs($this->user)
            ->withHeaders(['X-Requested-With' => 'XMLHttpRequest'])
            ->postJson('/save-delivery-address', [
                'delivery_id' => $address->id,
                'delivery_name' => 'Jane Smith',
                'delivery_address' => '456 Avenue',
                'delivery_state' => 'NewState',
                'delivery_city' => 'NewCity',
                'delivery_country' => 'NewCountry',
                'delivery_zipcode' => '54321',
                'delivery_mobile' => '0987654321',
            ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('delivery_addresses', [
            'id' => $address->id,
            'name' => 'Jane Smith',
        ]);
    }

    public function test_remove_delivery_address()
    {
        $address = DeliveryAddress::factory()->create([
            'user_id' => $this->user->id,
        ]);
        $response = $this->actingAs($this->user)
            ->withHeaders(['X-Requested-With' => 'XMLHttpRequest'])
            ->postJson('/remove-delivery-address', [
                'addressid' => $address->id,
            ]);

        $response->assertStatus(200);

        $this->assertDatabaseMissing('delivery_addresses', [
            'id' => $address->id,
        ]);
    }
}
