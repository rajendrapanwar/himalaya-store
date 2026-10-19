<?php

namespace Tests\Feature;

use App\Events\OrderLifecycleChanged;
use App\Models\Order;
use App\Models\User;
use App\Notifications\AccountActivityNotification;
use App\Notifications\AdminAlertNotification;
use App\Notifications\AdminLoginCode;
use App\Notifications\OrderUpdateNotification;
use App\Notifications\ResetPasswordNotification;
use App\Notifications\VerifyEmailNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class NotificationSystemTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_account_email_notifications_use_the_branded_template(): void
    {
        $user = User::factory()->create();
        $order = Order::create([
            'order_number' => 'HS-MAIL-1',
            'user_id' => $user->id,
            'subtotal' => 700,
            'total_amount' => 700,
            'payment_method' => 'cod',
            'payment_status' => 'pending',
            'order_status' => 'pending',
        ]);

        $notifications = [
            new AdminLoginCode('123456'),
            new VerifyEmailNotification,
            new ResetPasswordNotification('reset-token'),
            new OrderUpdateNotification($order->id, 'placed'),
            new AccountActivityNotification('Account update', 'Something changed.', route('profile'), sendMail: true),
            new AdminAlertNotification('Admin update', 'Something needs attention.', route('admin.dashboard'), sendMail: true),
        ];

        foreach ($notifications as $notification) {
            $mail = $notification->toMail($user);
            $this->assertSame('mail.branded-message', $mail->view['html']);
            $this->assertSame('mail.branded-message-text', $mail->view['text']);
        }

        $html = view('mail.branded-message', [
            'heading' => 'Test message',
            'intro' => 'A branded email preview.',
        ])->render();
        $this->assertStringContainsString('Pahadi Roots', $html);
        $this->assertStringContainsString('assets/img/logo.png', $html);
    }

    public function test_customer_can_manage_only_their_own_database_notifications(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $user->notifyNow(new AccountActivityNotification('Points added', 'You earned 50 points.', route('profile')));
        $other->notifyNow(new AccountActivityNotification('Private update', 'This belongs to another customer.', route('profile')));

        $notification = $user->notifications()->firstOrFail();
        $otherNotification = $other->notifications()->firstOrFail();

        $this->actingAs($user)->get(route('notifications.index'))
            ->assertOk()
            ->assertSee('Points added')
            ->assertDontSee('Private update');
        $this->patch(route('notifications.update', $notification), ['open' => 0])->assertRedirect();
        $this->assertNotNull($notification->fresh()->read_at);
        $this->patch(route('notifications.update', $otherNotification))->assertNotFound();
    }

    public function test_order_event_notifies_the_customer_and_relevant_staff(): void
    {
        $customer = User::factory()->create();
        $manager = User::factory()->create(['role' => 'manager']);
        $order = Order::create([
            'order_number' => 'HS-EVENT-1',
            'user_id' => $customer->id,
            'subtotal' => 900,
            'total_amount' => 900,
            'payment_method' => 'cod',
            'payment_status' => 'pending',
            'order_status' => 'pending',
        ]);

        OrderLifecycleChanged::dispatch($order->id, 'placed');

        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $customer->id,
            'type' => OrderUpdateNotification::class,
        ]);
        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $manager->id,
            'type' => AdminAlertNotification::class,
        ]);

        $this->actingAs($manager)
            ->withSession(['admin_2fa_verified_at' => now()->timestamp])
            ->get(route('admin.notifications.index'))
            ->assertOk()
            ->assertSee('New COD order');
    }

    public function test_user_overrides_framework_account_emails_with_branded_notifications(): void
    {
        Notification::fake();
        $user = User::factory()->create();

        $user->sendEmailVerificationNotification();
        $user->sendPasswordResetNotification('token');

        Notification::assertSentTo($user, VerifyEmailNotification::class);
        Notification::assertSentTo($user, ResetPasswordNotification::class);
    }
}
