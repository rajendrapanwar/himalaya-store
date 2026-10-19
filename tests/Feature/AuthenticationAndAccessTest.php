<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use App\Notifications\AdminLoginCode;
use App\Services\AdminTwoFactorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
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

    public function test_customer_can_upload_and_replace_a_profile_photo(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();

        $this->actingAs($user)->put(route('profile.update'), [
            'name' => $user->name,
            'email' => $user->email,
            'avatar' => UploadedFile::fake()->image('profile.jpg', 500, 500)->size(300),
        ])->assertRedirect();

        $firstAvatar = $user->fresh()->avatar_path;
        $this->assertNotNull($firstAvatar);
        Storage::disk('public')->assertExists($firstAvatar);
        $this->assertSame(asset('storage/'.$firstAvatar), $user->fresh()->avatar_url);

        $this->put(route('profile.update'), [
            'name' => $user->name,
            'email' => $user->email,
            'avatar' => UploadedFile::fake()->image('updated-profile.png', 600, 600)->size(350),
        ])->assertRedirect();

        $updatedUser = $user->fresh();
        $this->assertNotSame($firstAvatar, $updatedUser->avatar_path);
        Storage::disk('public')->assertMissing($firstAvatar);
        Storage::disk('public')->assertExists($updatedUser->avatar_path);
        $this->get(route('profile'))->assertOk()->assertSee('profile photo preview');
    }

    public function test_guest_cart_is_preserved_when_customer_registers(): void
    {
        $product = Product::factory()->create(['name' => 'Mountain Rajma', 'stock_quantity' => 10]);

        $this->post(route('cart.add', $product), ['quantity' => 2])
            ->assertRedirect(route('cart.index'));

        $guestCart = Cart::query()->whereNull('user_id')->firstOrFail();
        $previousSessionId = $guestCart->session_id;

        $this->post(route('register'), [
            'name' => 'Meera Rawat',
            'email' => 'meera@example.test',
            'password' => 'Secure-password1',
            'password_confirmation' => 'Secure-password1',
        ])->assertRedirect(route('home'));

        $user = User::query()->where('email', 'meera@example.test')->firstOrFail();

        $this->assertAuthenticatedAs($user);
        $this->assertDatabaseHas('carts', [
            'id' => $guestCart->id,
            'user_id' => $user->id,
        ]);
        $this->assertDatabaseMissing('carts', ['session_id' => $previousSessionId]);
        $this->assertDatabaseHas('cart_items', [
            'cart_id' => $guestCart->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);
        $this->get(route('cart.index'))->assertOk()->assertSee('Mountain Rajma');
    }

    public function test_guest_cart_is_merged_with_customer_cart_when_signing_in(): void
    {
        $product = Product::factory()->create(['name' => 'Himalayan Honey', 'stock_quantity' => 10, 'price' => 450]);
        $user = User::factory()->create([
            'email' => 'returning@example.test',
            'password' => Hash::make('Secure-password1'),
        ]);
        $userCart = Cart::create(['session_id' => 'previous-customer-session', 'user_id' => $user->id]);
        CartItem::create(['cart_id' => $userCart->id, 'product_id' => $product->id, 'quantity' => 1, 'price' => 450]);

        $this->post(route('cart.add', $product), ['quantity' => 2]);
        $guestCart = Cart::query()->whereNull('user_id')->firstOrFail();

        $this->post(route('login'), [
            'email' => 'returning@example.test',
            'password' => 'Secure-password1',
        ])->assertRedirect(route('home'));

        $this->assertAuthenticatedAs($user);
        $this->assertDatabaseMissing('carts', ['id' => $guestCart->id]);
        $this->assertDatabaseHas('cart_items', [
            'cart_id' => $userCart->id,
            'product_id' => $product->id,
            'quantity' => 3,
        ]);
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
