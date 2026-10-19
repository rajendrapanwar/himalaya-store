<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\AdminLoginCode;
use App\Services\AdminTwoFactorService;
use App\Services\MaintenanceModeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AdminLoginAndMaintenanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_portal_has_a_separate_login_and_rejects_customer_accounts(): void
    {
        Notification::fake();
        $customer = User::factory()->create(['email' => 'customer@example.test', 'password' => Hash::make('Secure-password1')]);
        $administrator = User::factory()->create(['email' => 'admin@example.test', 'password' => Hash::make('Secure-password1'), 'is_admin' => true, 'role' => 'administrator']);

        $this->get(route('admin.login'))
            ->assertOk()
            ->assertSee('Administrator access')
            ->assertSee('Customer sign in');

        $this->post(route('admin.login.store'), ['email' => $customer->email, 'password' => 'Secure-password1'])
            ->assertRedirect()
            ->assertSessionHasErrors('email');
        $this->assertGuest();

        $this->post(route('admin.login.store'), ['email' => $administrator->email, 'password' => 'Secure-password1'])
            ->assertRedirect(route('admin.two-factor.show'));
        $this->assertAuthenticatedAs($administrator);
        Notification::assertSentTo($administrator, AdminLoginCode::class);
    }

    public function test_customer_login_rejects_staff_accounts(): void
    {
        $administrator = User::factory()->create(['email' => 'admin@example.test', 'password' => Hash::make('Secure-password1'), 'is_admin' => true, 'role' => 'administrator']);

        $this->post(route('login'), ['email' => $administrator->email, 'password' => 'Secure-password1'])
            ->assertRedirect()
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_admin_is_sent_to_dashboard_after_email_code_verification(): void
    {
        $administrator = User::factory()->create(['is_admin' => true, 'role' => 'administrator']);
        $code = app(AdminTwoFactorService::class)->send($administrator);

        $this->actingAs($administrator)
            ->from(route('admin.maintenance.index'))
            ->post(route('admin.two-factor.verify'), ['code' => $code])
            ->assertRedirect(route('admin.dashboard'));
    }

    public function test_unauthenticated_admin_routes_redirect_to_the_admin_login(): void
    {
        $this->get(route('admin.dashboard'))->assertRedirect(route('admin.login'));
    }

    public function test_administrator_can_enable_and_disable_maintenance_without_losing_admin_access(): void
    {
        $administrator = User::factory()->create(['is_admin' => true, 'role' => 'administrator']);
        $manager = User::factory()->create(['role' => 'manager']);
        $message = 'We are preparing fresh mountain produce and will reopen very shortly.';

        $this->actingAs($manager)
            ->withSession(['admin_2fa_verified_at' => now()->timestamp])
            ->get(route('admin.maintenance.index'))
            ->assertForbidden();

        $this->actingAs($administrator)
            ->withSession(['admin_2fa_verified_at' => now()->timestamp])
            ->patch(route('admin.maintenance.update'), [
                'enabled' => 1,
                'message' => $message,
                'expected_return_at' => now()->addHours(2)->format('Y-m-d H:i:s'),
            ])->assertRedirect();

        $this->assertTrue(app(MaintenanceModeService::class)->enabled());
        $this->assertDatabaseHas('site_settings', ['key' => 'maintenance', 'updated_by' => $administrator->id]);

        auth()->logout();
        $this->get(route('home'))
            ->assertStatus(503)
            ->assertSee('We are tending to the store')
            ->assertSee($message)
            ->assertHeader('Retry-After', '3600')
            ->assertHeader('X-Robots-Tag', 'noindex, nofollow');
        $this->get(route('admin.login'))->assertOk();
        $this->postJson(route('pincode.check'), ['pincode' => '248001'])
            ->assertStatus(503)
            ->assertJson(['maintenance' => true]);

        $this->actingAs($administrator)
            ->withSession(['admin_2fa_verified_at' => now()->timestamp])
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Maintenance mode is active');

        $this->patch(route('admin.maintenance.update'), ['enabled' => 0])->assertRedirect();
        $this->assertFalse(app(MaintenanceModeService::class)->enabled());
        auth()->logout();
        $this->get(route('home'))->assertOk();
    }

    public function test_maintenance_fallback_is_not_cached_when_settings_cannot_be_read(): void
    {
        Cache::flush();
        Schema::drop('site_settings');

        $this->assertFalse(app(MaintenanceModeService::class)->enabled());
        // A transient failure must not pin "disabled" in the cache forever.
        $this->assertFalse(Cache::has('site-setting:maintenance'));
    }
}
