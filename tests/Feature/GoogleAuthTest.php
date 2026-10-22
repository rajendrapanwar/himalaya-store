<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\GoogleProvider;
use Laravel\Socialite\Two\User as SocialiteUser;
use Mockery;
use Tests\TestCase;

class GoogleAuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['services.google.client_id' => 'test-client-id', 'services.google.client_secret' => 'test-client-secret']);
    }

    public function test_google_button_and_routes_are_hidden_until_configured(): void
    {
        $this->get(route('login'))->assertOk()->assertSee('Continue with Google');
        $this->get(route('register'))->assertOk()->assertSee('Sign up with Google');

        config(['services.google.client_id' => null]);

        $this->get(route('login'))->assertOk()->assertDontSee('Continue with Google');
        $this->get(route('register'))->assertOk()->assertDontSee('Sign up with Google');
        $this->get(route('auth.google'))->assertNotFound();
        $this->get(route('auth.google.callback'))->assertNotFound();
    }

    public function test_redirect_sends_customers_to_google_account_chooser(): void
    {
        $location = $this->get(route('auth.google'))->assertRedirect()->headers->get('Location');

        $this->assertStringStartsWith('https://accounts.google.com/o/oauth2/auth?', $location);
        parse_str((string) parse_url($location, PHP_URL_QUERY), $query);
        $this->assertSame('test-client-id', $query['client_id']);
        $this->assertSame(route('auth.google.callback'), $query['redirect_uri']);
        $this->assertSame('select_account', $query['prompt']);
        $this->assertStringContainsString('email', $query['scope']);
    }

    public function test_new_google_customer_gets_a_verified_account_and_is_signed_in(): void
    {
        Notification::fake();
        $this->fakeGoogleUser(['id' => 'google-new', 'name' => 'Asha Rawat', 'email' => 'Asha@Example.test']);

        $this->get(route('auth.google.callback', ['state' => 'x', 'code' => 'y']))
            ->assertRedirect(route('home'))
            ->assertSessionHas('success');

        $user = User::where('email', 'asha@example.test')->firstOrFail();
        $this->assertAuthenticatedAs($user);
        $this->assertSame('google-new', $user->google_id);
        $this->assertSame('Asha Rawat', $user->name);
        $this->assertNotNull($user->email_verified_at);
        $this->assertNotEmpty($user->referral_code);
        Notification::assertNothingSentTo($user);
    }

    public function test_existing_customer_is_linked_by_verified_email(): void
    {
        $customer = User::factory()->create(['email' => 'mohan@example.test', 'email_verified_at' => null]);
        $this->fakeGoogleUser(['id' => 'google-mohan', 'email' => 'mohan@example.test']);

        $this->withSession(['url.intended' => route('wishlist.index')])
            ->get(route('auth.google.callback'))
            ->assertRedirect(route('wishlist.index'));

        $this->assertAuthenticatedAs($customer);
        $customer->refresh();
        $this->assertSame('google-mohan', $customer->google_id);
        $this->assertNotNull($customer->email_verified_at);
        $this->assertSame(1, User::count());
    }

    public function test_staff_unverified_and_mismatched_google_accounts_are_refused(): void
    {
        User::factory()->create(['email' => 'admin@example.test', 'is_admin' => true, 'role' => 'administrator']);
        $this->fakeGoogleUser(['email' => 'admin@example.test']);
        $this->get(route('auth.google.callback'))->assertRedirect(route('login'))->assertSessionHasErrors('google');
        $this->assertGuest();

        $this->fakeGoogleUser(['email' => 'new@example.test', 'email_verified' => false]);
        $this->get(route('auth.google.callback'))->assertRedirect(route('login'))->assertSessionHasErrors('google');
        $this->assertDatabaseMissing('users', ['email' => 'new@example.test']);

        User::factory()->create(['email' => 'linked@example.test'])->forceFill(['google_id' => 'google-original'])->save();
        $this->fakeGoogleUser(['id' => 'google-other', 'email' => 'linked@example.test']);
        $this->get(route('auth.google.callback'))->assertRedirect(route('login'))->assertSessionHasErrors('google');
        $this->assertGuest();
    }

    public function test_cancelled_or_failed_google_sign_in_returns_to_login(): void
    {
        $this->get(route('auth.google.callback', ['error' => 'access_denied']))
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('google');

        $provider = Mockery::mock(GoogleProvider::class);
        $provider->shouldReceive('user')->andThrow(new \RuntimeException('Invalid state'));
        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

        $this->get(route('auth.google.callback'))->assertRedirect(route('login'))->assertSessionHasErrors('google');
        $this->assertGuest();
    }

    /**
     * @param  array{id?: string, name?: string, email?: string, email_verified?: bool}  $attributes
     */
    private function fakeGoogleUser(array $attributes): void
    {
        $googleUser = (new SocialiteUser)
            ->setRaw(['email_verified' => $attributes['email_verified'] ?? true])
            ->map([
                'id' => $attributes['id'] ?? 'google-123',
                'name' => $attributes['name'] ?? 'Google Customer',
                'email' => $attributes['email'] ?? 'customer@example.test',
            ]);

        $provider = Mockery::mock(GoogleProvider::class);
        $provider->shouldReceive('user')->andReturn($googleUser);
        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);
    }
}
