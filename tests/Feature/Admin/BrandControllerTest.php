<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\Brand;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BrandControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function actingAsAdmin()
    {
        $admin = Admin::factory()->create();
        return $this->actingAs($admin, 'admin');
    }

    public function test_brands_view_displays_all_brands()
    {
        Brand::factory()->count(3)->create();
        $response = $this->actingAsAdmin()->get('/admin/brands');

        $response->assertStatus(200);
        $response->assertViewHas('brands');
        $this->assertCount(3, $response->viewData('brands'));
    }

    public function test_update_brand_status_ajax()
    {
        $brand = Brand::factory()->create(['status' => 1]);

        $response = $this->actingAsAdmin()
            ->withHeaders(['X-Requested-With' => 'XMLHttpRequest'])
            ->postJson('/admin/update-brand-status', [
                'brand_id' => $brand->id,
                'status' => 'Active'
            ]);

        $response->assertJson([
            'status' => 0,
            'brand_id' => $brand->id
        ]);

        $this->assertEquals(0, $brand->fresh()->status);
    }

    public function test_delete_brand()
    {
        $brand = Brand::factory()->create();

        $response = $this->actingAsAdmin()->get('/admin/delete-brand/' . $brand->id);
        $response->assertRedirect();
        $response->assertSessionHas('success_message');
        $this->assertDatabaseMissing('brands', ['id' => $brand->id]);
    }

    public function test_add_new_brand()
    {
        $response = $this->actingAsAdmin()->post('/admin/add-edit-brand', [
            'brand_name' => 'Thương hiệu A'
        ]);

        $response->assertRedirect('/admin/brands');
        $response->assertSessionHas('success_message');
        $this->assertDatabaseHas('brands', ['name' => 'Thương hiệu A']);
    }

    public function test_edit_existing_brand()
    {
        $brand = Brand::factory()->create(['name' => 'Cũ']);

        $response = $this->actingAsAdmin()->post('/admin/add-edit-brand/' . $brand->id, [
            'brand_name' => 'Mới'
        ]);

        $response->assertRedirect('/admin/brands');
        $response->assertSessionHas('success_message');
        $this->assertDatabaseHas('brands', [
            'id' => $brand->id,
            'name' => 'Mới'
        ]);
    }

    public function test_brand_name_validation()
    {
        $response = $this->actingAsAdmin()->post('/admin/add-edit-brand', [
            'brand_name' => '###123'
        ]);

        $response->assertSessionHasErrors(['brand_name']);
    }
}
