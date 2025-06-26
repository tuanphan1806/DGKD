<?php

namespace Tests\Feature\Front;

use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Session;
use Tests\TestCase;

class PaypalControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Session::start();
    }

    public function test_redirects_to_cart_if_no_order_in_session()
    {
        $response = $this->get('/paypal');

        $response->assertRedirect('/cart');
    }

    public function test_show_paypal_view_if_order_id_exists_in_session()
    {
        Session::put('order_id', 1);
        $response = $this->get('/paypal');

        $response->assertStatus(200)
            ->assertViewIs('front.paypal.paypal');
    }

    public function test_pay_method_handles_redirect()
    {
        // Không test trực tiếp Paypal API, nên cần mock Gateway nếu muốn test sâu hơn
        // Ở đây chỉ kiểm tra có lỗi không khi gọi route này

        Session::put('grand_total', 4800000); // ~200 USD

        $response = $this->post('/pay');

        // Không kiểm tra được redirect thực tế của gateway
        // Chỉ đảm bảo không lỗi server
        $this->assertTrue($response->isSuccessful() || $response->status() < 500);
    }

    public function test_error_method()
    {
        $response = $this->get('/error');

        $response->assertStatus(200)
            ->assertSeeText('Khách hàng từ chối thanh toán!');
    }

    // Không thể test success method chính xác nếu không mock Paypal gateway
    // Bạn có thể dùng Mockery hoặc Omnipay mock để kiểm tra giao dịch thành công
}
