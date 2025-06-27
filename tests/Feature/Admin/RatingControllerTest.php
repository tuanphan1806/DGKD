<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\Rating;
use App\Models\User;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RatingControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function authenticateAsAdmin()
    {
        $admin = Admin::factory()->create();
        $this->actingAs($admin, 'admin');
        return $admin;
    }

    public function test_admin_can_view_ratings_list()
    {
        $this->authenticateAsAdmin();

        $user = User::factory()->create();
        $product = Product::factory()->create();
        Rating::factory()->create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'review' => 'Tốt',
            'rating' => 5,
            'status' => 1
        ]);

        $response = $this->get('/admin/ratings');

        $response->assertStatus(200);
        $response->assertViewIs('admin.ratings.ratings');
        $response->assertViewHas('ratings');
    }

    public function test_admin_can_update_rating_status()
    {
        $this->authenticateAsAdmin();

        $rating = Rating::factory()->create(['status' => 1]);

        $response = $this->postJson('/admin/update-rating-status', [
            'rating_id' => $rating->id,
            'status' => 'Active'
        ], ['X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(200);
        $response->assertJson(['status' => 0]);

        $this->assertDatabaseHas('ratings', [
            'id' => $rating->id,
            'status' => 0
        ]);
    }

    public function test_admin_can_delete_rating()
    {
        $this->authenticateAsAdmin();

        $rating = Rating::factory()->create();

        $response = $this->get('/admin/delete-rating/' . $rating->id);

        $response->assertRedirect();
        $this->assertDatabaseMissing('ratings', [
            'id' => $rating->id
        ]);
    }
    public function test_guest_cannot_add_rating()
    {
        $product = Product::factory()->create();

        $response = $this->post('/add-rating', [
            'product_id' => $product->id,
            'rating' => 5,
            'review' => 'Great product!',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error_message', 'Bạn phải đăng nhập để đánh giá sản phẩm !');
    }

    public function test_user_can_add_rating_successfully()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $this->actingAs($user);

        $response = $this->post('/add-rating', [
            'product_id' => $product->id,
            'rating' => 4,
            'review' => 'Nice quality',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success_message', 'Cảm ơn bạn đã đánh giá cho sản phẩm này !');
        $this->assertDatabaseHas('ratings', [
            'user_id' => $user->id,
            'product_id' => $product->id,
            'rating' => 4,
            'review' => 'Nice quality',
        ]);
    }

    public function test_user_cannot_rate_product_twice()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        Rating::factory()->create([
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);

        $this->actingAs($user);

        $response = $this->post('/add-rating', [
            'product_id' => $product->id,
            'rating' => 5,
            'review' => 'Second rating',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error_message', 'Bạn đã đánh giá sản phẩm này rồi !');
    }

    public function test_rating_fails_when_rating_field_is_missing()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $this->actingAs($user);

        $response = $this->post('/add-rating', [
            'product_id' => $product->id,
            'review' => 'Missing rating',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error_message', 'Hãy thêm xếp hạng cho sản phẩm này !');
    }
    public function test_guest_cannot_submit_rating_and_is_redirected()
    {
        $product = Product::factory()->create();

        $response = $this->post('/add-rating', [
            'product_id' => $product->id,
            'rating' => 4,
            'review' => 'Great product!'
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error_message', 'Bạn phải đăng nhập để đánh giá sản phẩm !');
    }

    public function test_user_cannot_rate_same_product_twice()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();
        Rating::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'rating' => 5,
            'review' => 'Initial review',
            'status' => 1
        ]);

        $this->actingAs($user);
        $response = $this->post('/add-rating', [
            'product_id' => $product->id,
            'rating' => 4,
            'review' => 'Another review'
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error_message', 'Bạn đã đánh giá sản phẩm này rồi !');
    }

    public function test_user_cannot_submit_rating_without_rating_value()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $this->actingAs($user);
        $response = $this->post('/add-rating', [
            'product_id' => $product->id,
            'review' => 'Missing rating value'
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error_message', 'Hãy thêm xếp hạng cho sản phẩm này !');
    }

    public function test_user_can_submit_valid_rating()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $this->actingAs($user);
        $response = $this->post('/add-rating', [
            'product_id' => $product->id,
            'rating' => 5,
            'review' => 'Excellent!'
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success_message', 'Cảm ơn bạn đã đánh giá cho sản phẩm này !');

        $this->assertDatabaseHas('ratings', [
            'user_id' => $user->id,
            'product_id' => $product->id,
            'rating' => 5,
            'review' => 'Excellent!'
        ]);
    }
}
