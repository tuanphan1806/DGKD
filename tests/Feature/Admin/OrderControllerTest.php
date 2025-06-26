<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\Order;
use App\Models\OrdersLog;
use App\Models\OrdersProduct;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderControllerTest extends TestCase
{
    use RefreshDatabase;

    protected Admin $admin;
    protected Admin $vendor;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = Admin::factory()->create(['type' => 'superadmin']);
        $this->vendor = Admin::factory()->create(['type' => 'vendor', 'status' => 1]);

        $this->actingAs($this->admin, 'admin');
    }

    public function test_admin_can_view_orders_page(): void
    {
        $response = $this->get('admin/orders');

        $response->assertStatus(200)
            ->assertViewIs('admin.orders.orders')
            ->assertViewHas('orders');
    }

    public function test_vendor_with_inactive_status_redirects(): void
    {
        $this->vendor->update(['status' => 0]);
        $this->actingAs($this->vendor, 'admin');

        $response = $this->get('admin/orders');

        $response->assertRedirect('admin/update-vendor-details/personal')
            ->assertSessionHas('error_message');
    }



    public function test_admin_can_update_order_status(): void
    {
        $order = Order::factory()->create(['order_status' => 'Pending']);

        $data = [
            'order_id' => $order->id,
            'order_status' => 'Completed',
            'courier_name' => 'GHTK',
            'tracking_number' => 'TRACK123456'
        ];

        $response = $this->post('admin/update-order-status', $data);

        $response->assertRedirect()
            ->assertSessionHas('success_message');

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'order_status' => 'Completed',
            'courier_name' => 'GHTK',
            'tracking_number' => 'TRACK123456'
        ]);

        $this->assertDatabaseHas('orders_logs', [
            'order_id' => $order->id,
            'order_status' => 'Completed'
        ]);
    }



    public function test_admin_can_view_order_invoice(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $user->id]);
        OrdersProduct::factory()->count(2)->create(['order_id' => $order->id]);

        $response = $this->get("admin/orders/invoice/{$order->id}");

        $response->assertStatus(200)
            ->assertViewIs('admin.orders.order_invoice')
            ->assertViewHasAll(['orderDetails', 'userDetails']);
    }
}
