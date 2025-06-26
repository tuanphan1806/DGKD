<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Section;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class CouponsControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function actingAsAdmin()
    {
        $admin = Admin::factory()->create(['type' => 'admin']);
        $this->actingAs($admin, 'admin');
        return $admin;
    }

    public function test_admin_can_view_all_coupons()
    {
        $this->actingAsAdmin();
        Coupon::factory()->count(2)->create();

        $response = $this->get('/admin/coupons');

        $response->assertStatus(200);
        $response->assertViewIs('admin.coupons.coupons');
        $response->assertViewHas('coupons');
    }

    public function test_update_coupon_status_from_active_to_inactive()
    {
        $this->actingAsAdmin();
        $coupon = Coupon::factory()->create(['status' => 1]);

        $response = $this->postJson('/admin/update-coupon-status', [
            'coupon_id' => $coupon->id,
            'status' => 'Active',
        ], [
            'X-Requested-With' => 'XMLHttpRequest'
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 0,
                'coupon_id' => $coupon->id,
            ]);

        $this->assertDatabaseHas('coupons', [
            'id' => $coupon->id,
            'status' => 0,
        ]);
    }


    public function test_update_coupon_status_from_inactive_to_active()
    {
        $this->actingAsAdmin();
        $coupon = Coupon::factory()->create(['status' => 0]);

        $response = $this->postJson('/admin/update-coupon-status', [
            'coupon_id' => $coupon->id,
            'status' => 'Inactive',
        ], [
            'X-Requested-With' => 'XMLHttpRequest'
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 1,
                'coupon_id' => $coupon->id,
            ]);

        $this->assertDatabaseHas('coupons', [
            'id' => $coupon->id,
            'status' => 1,
        ]);
    }

    public function test_delete_coupon()
    {
        $this->actingAsAdmin();
        $coupon = Coupon::factory()->create();

        $response = $this->get('/admin/delete-coupon/' . $coupon->id);
        $response->assertRedirect();

        $this->assertDatabaseMissing('coupons', [
            'id' => $coupon->id,
        ]);
    }
}
