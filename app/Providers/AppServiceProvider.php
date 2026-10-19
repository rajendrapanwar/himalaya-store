<?php

namespace App\Providers;

use App\Models\Cart;
use App\Models\Category;
use App\Services\MaintenanceModeService;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /** Signed-in traffic from one IP may use this many times an account's daily allowance. */
    private const SHARED_NETWORK_MULTIPLIER = 10;

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureRateLimiters();

        view()->composer('layouts.app', function ($view) {
            $count = 0;
            if (session()->isStarted()) {
                $cart = Cart::query()->where(function ($query) {
                    $query->where('session_id', session()->getId());
                    if (auth()->check()) {
                        $query->orWhere('user_id', auth()->id());
                    }
                })->with(['items:id,cart_id,quantity', 'bundleItems:id,cart_id,quantity'])->latest('id')->first();
                $count = $cart ? (int) $cart->items->sum('quantity') + (int) $cart->bundleItems->sum('quantity') : 0;
            }
            $view->with([
                'cartCount' => $count,
                'navigationCategories' => Cache::remember('navigation_categories', now()->addMinutes(15), fn () => Category::query()
                    ->where('status', true)
                    ->whereHas('products', fn ($query) => $query->where('status', true))
                    ->orderBy('name')
                    ->get(['name', 'slug'])),
            ]);
        });

        view()->composer(['layouts.app', 'layouts.admin'], function ($view) {
            $view->with('unreadNotificationCount', auth()->check()
                ? auth()->user()->unreadNotifications()->count()
                : 0);
        });

        view()->composer('layouts.admin', function ($view) {
            $view->with('maintenanceStatus', app(MaintenanceModeService::class)->status());
        });
    }

    private function configureRateLimiters(): void
    {
        RateLimiter::for('login', function (Request $request) {
            $credential = $this->credentialKey($request);
            $ip = $this->ipKey($request);

            return [
                Limit::perMinute(5)->by("login:credential-minute:{$credential}:{$ip}"),
                Limit::perHour(30)->by("login:credential-hour:{$credential}:{$ip}"),
                Limit::perHour(150)->by("login:ip-hour:{$ip}"),
            ];
        });

        RateLimiter::for('admin-login', function (Request $request) {
            $credential = $this->credentialKey($request);
            $ip = $this->ipKey($request);

            return [
                Limit::perMinute(5)->by("admin-login:credential-minute:{$credential}:{$ip}"),
                Limit::perHour(25)->by("admin-login:credential-hour:{$credential}:{$ip}"),
                Limit::perHour(60)->by("admin-login:ip-hour:{$ip}"),
            ];
        });

        RateLimiter::for('registration', fn (Request $request) => [
            Limit::perHour(10)->by('registration:hour:'.$this->ipKey($request)),
            Limit::perDay(50)->by('registration:day:'.$this->ipKey($request)),
        ]);

        RateLimiter::for('password-reset', function (Request $request) {
            $credential = $this->credentialKey($request);
            $ip = $this->ipKey($request);

            return [
                Limit::perHour(5)->by("password-reset:credential-hour:{$credential}:{$ip}"),
                Limit::perHour(20)->by("password-reset:ip-hour:{$ip}"),
            ];
        });

        RateLimiter::for('password-update', function (Request $request) {
            $credential = $this->credentialKey($request);
            $ip = $this->ipKey($request);

            return [
                Limit::perHour(5)->by("password-update:credential-hour:{$credential}:{$ip}"),
                Limit::perHour(15)->by("password-update:ip-hour:{$ip}"),
            ];
        });

        RateLimiter::for('contact', fn (Request $request) => $this->actorLimits('contact', $request, 2, 5, 10));
        RateLimiter::for('referrals', fn (Request $request) => $this->actorLimits('referrals', $request, 20, 100, 300));
        RateLimiter::for('verification', fn (Request $request) => $this->actorLimits('verification', $request, 3, 10, 25));
        RateLimiter::for('logout', fn (Request $request) => $this->actorLimits('logout', $request, 10, 30, 100));
        RateLimiter::for('admin-2fa', fn (Request $request) => $this->actorLimits('admin-2fa', $request, 6, 20, 50));
        RateLimiter::for('admin-2fa-resend', fn (Request $request) => $this->actorLimits('admin-2fa-resend', $request, 2, 6, 12));
        RateLimiter::for('commerce-actions', fn (Request $request) => $this->actorLimits('commerce-actions', $request, 30, 300, 1200));
        RateLimiter::for('cart-actions', fn (Request $request) => $this->actorLimits('cart-actions', $request, 60, 500, 2000));
        RateLimiter::for('wishlist', fn (Request $request) => $this->actorLimits('wishlist', $request, 30, 200, 600));
        RateLimiter::for('pincode', fn (Request $request) => $this->actorLimits('pincode', $request, 30, 200, 800));
        RateLimiter::for('checkout-quote', fn (Request $request) => $this->actorLimits('checkout-quote', $request, 30, 200, 500));
        RateLimiter::for('checkout', fn (Request $request) => $this->actorLimits('checkout', $request, 5, 20, 50));
        RateLimiter::for('payment', fn (Request $request) => $this->actorLimits('payment', $request, 10, 40, 100));
        RateLimiter::for('reviews', fn (Request $request) => $this->actorLimits('reviews', $request, 2, 5, 10));
        RateLimiter::for('returns', fn (Request $request) => $this->actorLimits('returns', $request, 2, 5, 10));
        RateLimiter::for('profile', fn (Request $request) => $this->actorLimits('profile', $request, 5, 15, 50));
        RateLimiter::for('notifications', fn (Request $request) => $this->actorLimits('notifications', $request, 60, 500, 1500));
        RateLimiter::for('admin', fn (Request $request) => $this->actorLimits('admin', $request, 180, 2000, 6000));
        RateLimiter::for('admin-sensitive', fn (Request $request) => $this->actorLimits('admin-sensitive', $request, 10, 50, 150));
        RateLimiter::for('stripe-webhooks', fn (Request $request) => [
            Limit::perMinute(300)->by('stripe-webhooks:minute:'.$this->ipKey($request)),
            Limit::perHour(3000)->by('stripe-webhooks:hour:'.$this->ipKey($request)),
        ]);
    }

    /**
     * Apply both short-burst and sustained limits. Authenticated limits are keyed
     * to the account so customers who share a carrier-grade NAT or office IP do
     * not exhaust each other's allowance; a much wider per-IP ceiling still
     * contains one network driving many accounts.
     *
     * @return array<int, Limit>
     */
    private function actorLimits(string $name, Request $request, int $perMinute, int $perHour, int $perDay): array
    {
        $actor = $this->actorKey($request);
        $limits = [
            Limit::perMinute($perMinute)->by("{$name}:actor-minute:{$actor}"),
            Limit::perHour($perHour)->by("{$name}:actor-hour:{$actor}"),
            Limit::perDay($perDay)->by("{$name}:actor-day:{$actor}"),
        ];

        if ($request->user()) {
            $limits[] = Limit::perDay($perDay * self::SHARED_NETWORK_MULTIPLIER)->by("{$name}:ip-day:{$this->ipKey($request)}");
        }

        return $limits;
    }

    private function actorKey(Request $request): string
    {
        return $request->user()
            ? 'user:'.$request->user()->getAuthIdentifier()
            : 'ip:'.$this->ipKey($request);
    }

    private function ipKey(Request $request): string
    {
        return hash('sha256', (string) ($request->ip() ?: 'unknown'));
    }

    private function credentialKey(Request $request): string
    {
        return hash('sha256', mb_strtolower(trim((string) $request->input('email'))));
    }
}
