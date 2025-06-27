<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\Section;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SectionControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function authenticateAsAdmin()
    {
        $admin = Admin::factory()->create();
        $this->actingAs($admin, 'admin');
        return $admin;
    }

    public function test_admin_can_view_sections_list()
    {
        $this->authenticateAsAdmin();

        Section::factory()->create(['name' => 'Điện thoại']);

        $response = $this->get('/admin/sections');

        $response->assertStatus(200);
        $response->assertViewIs('admin.sections.sections');
        $response->assertViewHas('sections');
    }

    public function test_admin_can_update_section_status()
    {
        $this->authenticateAsAdmin();

        $section = Section::factory()->create(['status' => 1]);

        $response = $this->postJson('/admin/update-section-status', [
            'section_id' => $section->id,
            'status' => 'Active'
        ], ['X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(200);
        $response->assertJson(['status' => 0]);

        $this->assertDatabaseHas('sections', [
            'id' => $section->id,
            'status' => 0
        ]);
    }

    public function test_admin_can_delete_section()
    {
        $this->authenticateAsAdmin();

        $section = Section::factory()->create();

        $response = $this->get('/admin/delete-section/' . $section->id);

        $response->assertRedirect();
        $this->assertDatabaseMissing('sections', [
            'id' => $section->id
        ]);
    }

    public function test_admin_can_add_section()
    {
        $this->authenticateAsAdmin();

        $response = $this->post('/admin/add-edit-section', [
            'section_name' => 'Máy tính'
        ]);

        $response->assertRedirect('/admin/sections');
        $this->assertDatabaseHas('sections', [
            'name' => 'Máy tính'
        ]);
    }

    public function test_admin_can_edit_section()
    {
        $this->authenticateAsAdmin();

        $section = Section::factory()->create(['name' => 'Cũ']);

        $response = $this->post('/admin/add-edit-section/' . $section->id, [
            'section_name' => 'Mới'
        ]);

        $response->assertRedirect('/admin/sections');
        $this->assertDatabaseHas('sections', [
            'id' => $section->id,
            'name' => 'Mới'
        ]);
    }
}
