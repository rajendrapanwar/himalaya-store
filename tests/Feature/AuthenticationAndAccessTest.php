<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\AdminLoginCode;
use App\Services\AdminTwoFactorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AuthenticationAndAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'Asha Sharma',
            'email' => 'asha@example.test',
            'password' => 'Secure-password1',
            'password_confirmation' => 'Secure-password1',
        ]);

        $response->assertRedirect('/');
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', ['email' => 'asha@example.test', 'is_admin' => false]);
    }

    public function test_admin_area_requires_an_administrator(): void
    {
        $customer = User::factory()->create();
        $this->actingAs($customer)->get('/admin/dashboard')->assertForbidden();

        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin)->withSession(['admin_2fa_verified_at' => now()->timestamp])->get('/admin/dashboard')->assertOk();
    }

    public function test_checkout_requires_sign_in(): void
    {
        $this->get('/checkout')->assertRedirect('/login');
    }

    public function test_admin_login_code_is_generated_and_sent_immediately(): void
    {
        Notification::fake();
        $admin = User::factory()->create(['is_admin' => true, 'role' => 'administrator']);

        $code = app(AdminTwoFactorService::class)->send($admin);

        $this->assertMatchesRegularExpression('/^\d{6}$/', $code);
        Notification::assertSentTo($admin, AdminLoginCode::class);
    }
}
