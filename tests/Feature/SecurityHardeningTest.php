<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
