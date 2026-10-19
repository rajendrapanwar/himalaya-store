<?php

namespace Tests\Feature;

use App\Mail\ContactMessageAcknowledgement;
use App\Mail\ContactMessageReceived;
use App\Models\Coupon;
use App\Models\InventoryReservation;
use App\Models\Order;
use App\Models\Product;
use App\Models\StripeWebhookEvent;
use App\Models\User;
use App\Notifications\OrderUpdateNotification;
use App\Services\CouponService;
use App\Services\InventoryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class ProductionReadinessTest extends TestCase
{
    use RefreshDatabase;

    public function test_signed_stripe_webhook_is_idempotent_and_commits_inventory(): void
    {
        Notification::fake();
        config(['services.stripe.webhook_secret' => 'whsec_local_test']);
        $user = User::factory()->create();
        $product = Product::factory()->create(['stock_quantity' => 5, 'reserved_quantity' => 0]);
        $order = Order::create(['order_number' => 'HS-WEBHOOK-1', 'user_id' => $user->id, 'subtotal' => 500, 'total_amount' => 500, 'currency' => 'INR', 'payment_method' => 'stripe', 'payment_status' => 'pending', 'order_status' => 'pending', 'stripe_checkout_session_id' => 'cs_test_1', 'payment_expires_at' => now()->addMinutes(30)]);
        app(InventoryService::class)->reserve($order, [$product->id => 2], now()->addMinutes(30));

        $payload = json_encode(['id' => 'evt_checkout_paid', 'object' => 'event', 'type' => 'checkout.session.completed', 'data' => ['object' => ['id' => 'cs_test_1', 'object' => 'checkout.session', 'payment_status' => 'paid', 'payment_intent' => 'pi_test_1', 'amount_total' => 50000, 'currency' => 'inr']]], JSON_THROW_ON_ERROR);
        $signature = $this->stripeSignature($payload, 'whsec_local_test');
        $this->call('POST', route('stripe.webhook'), [], [], [], ['HTTP_STRIPE_SIGNATURE' => $signature, 'CONTENT_TYPE' => 'application/json'], $payload)->assertOk();
        $this->call('POST', route('stripe.webhook'), [], [], [], ['HTTP_STRIPE_SIGNATURE' => $signature, 'CONTENT_TYPE' => 'application/json'], $payload)->assertOk();

        $this->assertSame('paid', $order->fresh()->payment_status);
        $this->assertSame(3, $product->fresh()->stock_quantity);
        $this->assertSame(0, $product->fresh()->reserved_quantity);
        $this->assertSame(1, StripeWebhookEvent::count());
        Notification::assertSentTo($user, OrderUpdateNotification::class, fn ($notification) => $notification->event === 'paid');
    }

    public function test_tampered_stripe_total_fails_without_committing_stock(): void
    {
        config(['services.stripe.webhook_secret' => 'whsec_local_test']);
        $user = User::factory()->create();
        $product = Product::factory()->create(['stock_quantity' => 5, 'reserved_quantity' => 0]);
        $order = Order::create(['order_number' => 'HS-WEBHOOK-2', 'user_id' => $user->id, 'subtotal' => 500, 'total_amount' => 500, 'currency' => 'INR', 'payment_method' => 'stripe', 'payment_status' => 'pending', 'order_status' => 'pending', 'stripe_checkout_session_id' => 'cs_test_2', 'payment_expires_at' => now()->addMinutes(30)]);
        app(InventoryService::class)->reserve($order, [$product->id => 1], now()->addMinutes(30));
        $payload = json_encode(['id' => 'evt_bad_amount', 'object' => 'event', 'type' => 'checkout.session.completed', 'data' => ['object' => ['id' => 'cs_test_2', 'object' => 'checkout.session', 'payment_status' => 'paid', 'payment_intent' => 'pi_bad', 'amount_total' => 1, 'currency' => 'inr']]], JSON_THROW_ON_ERROR);
        $this->call('POST', route('stripe.webhook'), [], [], [], ['HTTP_STRIPE_SIGNATURE' => $this->stripeSignature($payload, 'whsec_local_test'), 'CONTENT_TYPE' => 'application/json'], $payload)->assertStatus(500);
        $this->assertSame('pending', $order->fresh()->payment_status);
        $this->assertSame(1, $product->fresh()->reserved_quantity);
        $this->assertDatabaseHas('stripe_webhook_events', ['stripe_event_id' => 'evt_bad_amount', 'status' => 'failed']);
    }

    public function test_expiry_command_releases_pending_stripe_reservation(): void
    {
        Notification::fake();
        $user = User::factory()->create();
        $product = Product::factory()->create(['stock_quantity' => 4, 'reserved_quantity' => 0]);
        $order = Order::create(['order_number' => 'HS-EXPIRED', 'user_id' => $user->id, 'subtotal' => 200, 'total_amount' => 200, 'payment_method' => 'stripe', 'payment_status' => 'pending', 'order_status' => 'pending', 'payment_expires_at' => now()->subMinute()]);
        app(InventoryService::class)->reserve($order, [$product->id => 2], now()->subMinute());
        $this->artisan('commerce:expire-reservations')->assertSuccessful();
        $this->assertSame('failed', $order->fresh()->payment_status);
        $this->assertSame(0, $product->fresh()->reserved_quantity);
        $this->assertSame('released', InventoryReservation::first()->status);
    }

    public function test_contact_message_is_persisted_and_email_is_queued(): void
    {
        Mail::fake();
        $this->post(route('contact.send'), ['name' => 'Mountain Buyer', 'email' => 'buyer@example.com', 'subject' => 'Product question', 'message' => 'Please tell me about the producer and batch.'])->assertRedirect();
        $this->assertDatabaseHas('contact_messages', ['email' => 'buyer@example.com', 'status' => 'new']);
        Mail::assertQueued(ContactMessageReceived::class);
        // A guest-typed address is never mailed, so the form cannot relay text to strangers.
        Mail::assertNotQueued(ContactMessageAcknowledgement::class);
    }

    public function test_contact_acknowledgement_goes_only_to_a_verified_account_address(): void
    {
        Mail::fake();
        $customer = User::factory()->create(['email' => 'owner@example.com', 'email_verified_at' => now()]);

        $this->actingAs($customer)
            ->post(route('contact.send'), ['name' => 'Mountain Buyer', 'email' => 'someone-else@example.com', 'subject' => 'Product question', 'message' => 'Please tell me about the producer and batch.'])
            ->assertRedirect();

        Mail::assertQueued(ContactMessageAcknowledgement::class, fn (ContactMessageAcknowledgement $mail) => $mail->hasTo('owner@example.com') && ! $mail->hasTo('someone-else@example.com'));
    }

    public function test_coupon_per_user_limit_is_enforced(): void
    {
        $user = User::factory()->create();
        $coupon = Coupon::create(['code' => 'ONCE', 'type' => 'fixed', 'value' => 50, 'minimum_amount' => 0, 'per_user_limit' => 1, 'used_count' => 1, 'is_active' => true]);
        $order = Order::create(['order_number' => 'HS-OLD', 'user_id' => $user->id, 'subtotal' => 500, 'discount_amount' => 50, 'total_amount' => 450, 'payment_method' => 'cod', 'payment_status' => 'paid', 'order_status' => 'delivered']);
        $coupon->redemptions()->create(['user_id' => $user->id, 'order_id' => $order->id, 'discount_amount' => 50, 'status' => 'redeemed']);
        $this->expectException(ValidationException::class);
        app(CouponService::class)->validateForCart($coupon, 500, $user);
    }

    public function test_catalogue_staff_cannot_access_team_security(): void
    {
        $staff = User::factory()->create(['role' => 'catalogue', 'is_admin' => false]);
        $this->actingAs($staff)->withSession(['admin_2fa_verified_at' => now()->timestamp])->get(route('admin.products.index'))->assertOk();
        $this->get(route('admin.team.index'))->assertForbidden();
    }

    public function test_is_admin_compatibility_flag_does_not_escalate_a_granular_staff_role(): void
    {
        $staff = User::factory()->create(['role' => 'support', 'is_admin' => true]);
        $this->actingAs($staff)->withSession(['admin_2fa_verified_at' => now()->timestamp]);
        $this->get(route('admin.customers.index'))->assertOk();
        $this->get(route('admin.products.index'))->assertForbidden();
        $this->get(route('admin.team.index'))->assertForbidden();
    }

    public function test_delivered_order_has_a_guarded_thirty_day_return_workflow(): void
    {
        $customer = User::factory()->create();
        $order = Order::create(['order_number' => 'HS-RETURN-1', 'user_id' => $customer->id, 'subtotal' => 500, 'total_amount' => 500, 'payment_method' => 'cod', 'payment_status' => 'paid', 'order_status' => 'delivered', 'delivered_at' => now()->subDays(5)]);
        $this->actingAs($customer)->post(route('returns.store', $order), ['reason' => 'quality', 'details' => 'The sealed product has an unexpected quality issue.'])->assertRedirect();
        $this->assertDatabaseHas('return_requests', ['order_id' => $order->id, 'status' => 'requested']);

        $manager = User::factory()->create(['role' => 'manager']);
        $return = $order->returnRequest()->firstOrFail();
        $this->actingAs($manager)->withSession(['admin_2fa_verified_at' => now()->timestamp])->patch(route('admin.returns.update', $return), ['status' => 'approved', 'admin_note' => 'Approved after reviewing the supplied details.'])->assertRedirect();
        $this->assertSame('approved', $return->fresh()->status);
        $this->patch(route('admin.returns.update', $return), ['status' => 'completed', 'admin_note' => 'Attempting completion.'])->assertStatus(422);
    }

    public function test_return_window_closes_after_thirty_days(): void
    {
        $customer = User::factory()->create();
        $order = Order::create(['order_number' => 'HS-RETURN-OLD', 'user_id' => $customer->id, 'subtotal' => 500, 'total_amount' => 500, 'payment_method' => 'cod', 'payment_status' => 'paid', 'order_status' => 'delivered', 'delivered_at' => now()->subDays(31)]);
        $this->actingAs($customer)->post(route('returns.store', $order), ['reason' => 'other', 'details' => 'This request is outside the permitted return period.'])->assertStatus(422);
        $this->assertDatabaseMissing('return_requests', ['order_id' => $order->id]);
    }

    private function stripeSignature(string $payload, string $secret): string
    {
        $timestamp = time();

        return 't='.$timestamp.',v1='.hash_hmac('sha256', $timestamp.'.'.$payload, $secret);
    }
}
