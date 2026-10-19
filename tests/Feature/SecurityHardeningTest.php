<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\EmailAddressChangedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class SecurityHardeningTest extends TestCase
{
    use RefreshDatabase;

    public function test_every_state_changing_web_route_is_rate_limited(): void
    {
        $mutationMethods = ['POST', 'PUT', 'PATCH', 'DELETE'];
        $unprotected = [];

        foreach (app('router')->getRoutes() as $route) {
            if (! array_intersect($mutationMethods, $route->methods())) {
                continue;
            }

            $hasThrottle = collect($route->gatherMiddleware())
                ->contains(fn (string $middleware) => str_starts_with($middleware, 'throttle:'));

            if (! $hasThrottle) {
                $unprotected[] = implode('|', $route->methods()).' /'.$route->uri();
            }
        }

        $this->assertSame([], $unprotected, 'Mutation routes without a rate limiter: '.implode(', ', $unprotected));
    }

    public function test_login_is_throttled_after_repeated_failures(): void
    {
        $this->withServerVariables(['REMOTE_ADDR' => '198.51.100.10']);

        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->post('/login', [
                'email' => 'attacker@example.test',
                'password' => 'incorrect-password',
            ])->assertRedirect();
        }

        $this->post('/login', [
            'email' => 'attacker@example.test',
            'password' => 'incorrect-password',
        ])->assertTooManyRequests();
    }

    public function test_ajax_delivery_checks_are_throttled_with_json_response(): void
    {
        $this->withServerVariables(['REMOTE_ADDR' => '198.51.100.20']);

        for ($attempt = 0; $attempt < 30; $attempt++) {
            $this->postJson(route('pincode.check'), ['pincode' => '248001'])
                ->assertSuccessful();
        }

        $this->postJson(route('pincode.check'), ['pincode' => '248001'])
            ->assertTooManyRequests()
            ->assertJsonStructure(['message']);
    }

    public function test_password_reset_request_does_not_disclose_account_existence(): void
    {
        Notification::fake();
        User::factory()->create(['email' => 'customer@example.test']);

        foreach (['customer@example.test', 'missing@example.test'] as $email) {
            $this->from(route('password.request'))
                ->post(route('password.email'), ['email' => $email])
                ->assertRedirect(route('password.request'))
                ->assertSessionHas('success', 'If an account exists for that email address, a password reset link has been sent.')
                ->assertSessionDoesntHaveErrors();
        }
    }

    public function test_changing_the_password_ends_other_sessions_but_keeps_this_one(): void
    {
        Notification::fake();
        $user = User::factory()->create(['password' => Hash::make('Original-password1')]);

        $this->actingAs($user)->put(route('profile.update'), [
            'name' => $user->name,
            'email' => $user->email,
            'current_password' => 'Original-password1',
            'password' => 'Replacement-password2',
            'password_confirmation' => 'Replacement-password2',
        ])->assertRedirect()->assertSessionHasNoErrors();

        $this->get(route('profile'))->assertOk();
        $this->assertTrue(Hash::check('Replacement-password2', $user->fresh()->password));
    }

    public function test_a_session_holding_a_stale_password_hash_is_signed_out(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->withSession(['password_hash_web' => 'hash-from-before-the-password-changed'])
            ->get(route('profile'))
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }

    public function test_changing_the_email_alerts_the_previous_address(): void
    {
        Notification::fake();
        $user = User::factory()->create(['email' => 'original@example.test']);

        $this->actingAs($user)->put(route('profile.update'), [
            'name' => $user->name,
            'email' => 'replacement@example.test',
        ])->assertRedirect()->assertSessionHasNoErrors();

        Notification::assertSentOnDemand(
            EmailAddressChangedNotification::class,
            fn ($notification, array $channels, AnonymousNotifiable $notifiable) => array_key_exists('original@example.test', (array) $notifiable->routes['mail'])
                && $notification->newEmail === 'replacement@example.test',
        );
    }

    public function test_script_policy_uses_a_per_response_nonce_instead_of_unsafe_inline(): void
    {
        $response = $this->get(route('home'))->assertOk();

        $this->assertMatchesRegularExpression('/script-src ([^;]+)/', (string) $response->headers->get('Content-Security-Policy'));
        preg_match('/script-src ([^;]+)/', (string) $response->headers->get('Content-Security-Policy'), $scriptPolicy);
        $this->assertStringNotContainsString("'unsafe-inline'", $scriptPolicy[1]);
        $this->assertSame(1, preg_match("/'nonce-([^']+)'/", $scriptPolicy[1], $nonce));
        $response->assertSee('nonce="'.$nonce[1].'"', false);
    }

    public function test_views_use_no_inline_event_handler_attributes(): void
    {
        $offenders = [];
        $views = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(resource_path('views')));
        foreach ($views as $file) {
            if ($file->isFile() && preg_match('/\son[a-z]+\s*=\s*["\']/i', file_get_contents($file->getPathname()))) {
                $offenders[] = $file->getFilename();
            }
        }

        $this->assertSame([], $offenders, 'Inline handlers are blocked by the CSP; use data-confirm, data-auto-submit, data-dismiss or data-print.');
    }

    public function test_private_pages_disable_caching_and_all_pages_emit_security_headers(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('X-Frame-Options', 'DENY')
            ->assertHeader('Cross-Origin-Opener-Policy', 'same-origin')
            ->assertHeader('Cross-Origin-Resource-Policy', 'same-origin')
            ->assertHeader('X-Permitted-Cross-Domain-Policies', 'none')
            ->assertHeader('Content-Security-Policy');

        $response = $this->get(route('cart.index'))
            ->assertOk()
            ->assertHeader('X-Robots-Tag', 'noindex, nofollow')
            ->assertHeader('Pragma', 'no-cache');

        foreach (['no-store', 'private', 'max-age=0', 'must-revalidate'] as $directive) {
            $this->assertStringContainsString($directive, (string) $response->headers->get('Cache-Control'));
        }
    }
}
