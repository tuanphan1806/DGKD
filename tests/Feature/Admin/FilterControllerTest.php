<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Section;
use App\Models\Category;
use App\Models\ProductsFilter;
use App\Models\ProductsFiltersValue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\View;
use Tests\TestCase;

class FilterControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function actingAsAdmin()
    {
        $admin = Admin::factory()->create(['type' => 'admin']);
        $this->actingAs($admin, 'admin');
        return $admin;
    }

    public function test_admin_can_view_filters_page()
    {
        $this->actingAsAdmin();

        // Tạo Section và Category
        $section = Section::factory()->create();
        $category = Category::factory()->create(['section_id' => $section->id]);

        // Tạo filter thủ công tránh MassAssignment
        $filter = new ProductsFilter();
        $filter->filter_name = 'TestFilter';
        $filter->filter_column = 'test_filter';
        $filter->cat_ids = (string)$category->id;
        $filter->status = 1;
        $filter->save();

        $response = $this->get('/admin/filters');
        $response->assertStatus(200)
            ->assertViewIs('admin.filters.filters')
            ->assertViewHas('filters');
    }

    public function test_admin_can_view_filter_values_page()
    {
        $this->actingAsAdmin();

        // Tạo filter
        $filter = new ProductsFilter();
        $filter->filter_name = 'Temp';
        $filter->filter_column = 'temp';
        $filter->cat_ids = '';
        $filter->status = 1;
        $filter->save();

        // Tạo filter value thủ công
        $value = new ProductsFiltersValue();
        $value->filter_id = $filter->id;
        $value->filter_value = 'Val1';
        $value->status = 1;
        $value->save();

        $response = $this->get('/admin/filters-values');
        $response->assertStatus(200)
            ->assertViewIs('admin.filters.filters_values')
            ->assertViewHas('filters_values');
    }
    public function test_update_filter_status_via_ajax()
    {
        $this->actingAsAdmin();
        $filter = ProductsFilter::factory()->create(['status' => 1]);

        $response = $this->postJson('/admin/update-filter-status', [
            'filter_id' => $filter->id,
            'status'    => 'Active',
        ], ['X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(200)
            ->assertJson(['status' => 0, 'filter_id' => $filter->id]);

        $this->assertDatabaseHas('products_filters', ['id' => $filter->id, 'status' => 0]);
    }

    public function test_update_filter_value_status_via_ajax()
    {
        $this->actingAsAdmin();
        $value = ProductsFiltersValue::factory()->create(['status' => 1]);

        $response = $this->postJson('/admin/update-filter-value-status', [
            'filter_id' => $value->id,
            'status'    => 'Active',
        ], ['X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(200)
            ->assertJson(['status' => 0, 'filter_id' => $value->id]);

        $this->assertDatabaseHas('products_filters_values', ['id' => $value->id, 'status' => 0]);
    }

    public function test_add_new_filter()
    {
        $this->actingAsAdmin();

        // Tạo section và categories
        $section = Section::factory()->create();
        $cats = Category::factory()->count(2)->create(['section_id' => $section->id]);

        $response = $this->post('/admin/add-edit-filter', [
            'cat_ids'       => $cats->pluck('id')->toArray(),
            'filter_name'   => 'Màu sắc',
            'filter_column' => 'color',
        ]);

        $response->assertRedirect('/admin/filters');
        $this->assertDatabaseHas('products_filters', ['filter_name' => 'Màu sắc', 'filter_column' => 'color']);
    }

    public function test_edit_existing_filter()
    {
        $this->actingAsAdmin();
        $filter = ProductsFilter::factory()->create(['filter_name' => 'Kích cỡ']);

        $response = $this->post('/admin/add-edit-filter/' . $filter->id, [
            'cat_ids'       => [],
            'filter_name'   => 'Size',
            'filter_column' => 'size',
        ]);

        $response->assertRedirect('/admin/filters');
        $this->assertDatabaseHas('products_filters', ['id' => $filter->id, 'filter_name' => 'Size']);
    }

    public function test_add_new_filter_value()
    {
        $this->actingAsAdmin();
        $filter = ProductsFilter::factory()->create();

        $response = $this->post('/admin/add-edit-filter-value', [
            'filter_id'    => $filter->id,
            'filter_value' => 'Đỏ',
        ]);

        $response->assertRedirect('/admin/filters-values');
        $this->assertDatabaseHas('products_filters_values', ['filter_id' => $filter->id, 'filter_value' => 'Đỏ']);
    }

    public function test_edit_existing_filter_value()
    {
        $this->actingAsAdmin();
        $value = ProductsFiltersValue::factory()->create(['filter_value' => 'Xanh']);

        $response = $this->post('/admin/add-edit-filter-value/' . $value->id, [
            'filter_id'    => $value->filter_id,
            'filter_value' => 'Xanh lá',
        ]);

        $response->assertRedirect('/admin/filters-values');
        $this->assertDatabaseHas('products_filters_values', ['id' => $value->id, 'filter_value' => 'Xanh lá']);
    }

    public function test_category_filters_ajax_returns_view()
    {
        $this->actingAsAdmin();
        $category = Category::factory()->create();

        $response = $this->postJson('/admin/category-filters', [
            'category_id' => $category->id,
        ], ['X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(200)
            ->assertJsonStructure(['view']);
    }
}
