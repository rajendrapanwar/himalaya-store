<?php

namespace App\Providers;

use App\Models\Cart;
use App\Models\Category;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
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
        RateLimiter::for('login', fn (Request $request) => Limit::perMinute(5)->by(strtolower((string) $request->input('email')).'|'.$request->ip()));
        RateLimiter::for('registration', fn (Request $request) => Limit::perHour(10)->by($request->ip()));
        RateLimiter::for('contact', fn (Request $request) => Limit::perHour(5)->by($request->ip()));
        RateLimiter::for('commerce-actions', fn (Request $request) => Limit::perMinute(20)->by($request->user()?->id ?: $request->ip()));
        RateLimiter::for('cart-actions', fn (Request $request) => Limit::perMinute(60)->by($request->user()?->id ?: $request->ip()));
        RateLimiter::for('reviews', fn (Request $request) => Limit::perHour(5)->by($request->user()?->id ?: $request->ip()));
        RateLimiter::for('checkout', fn (Request $request) => Limit::perMinute(5)->by($request->user()?->id ?: $request->ip()));

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
    }
}
