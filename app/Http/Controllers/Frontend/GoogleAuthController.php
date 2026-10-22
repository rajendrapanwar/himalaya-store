<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\CartService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\GoogleProvider;
use Symfony\Component\HttpFoundation\RedirectResponse as SymfonyRedirectResponse;
use Throwable;

/**
 * "Continue with Google" for customers: signs in an existing account (matched
 * by Google ID, then by verified email) or creates one. Staff accounts keep
 * using the administrator portal and its second factor.
 */
class GoogleAuthController extends Controller
{
    public static function enabled(): bool
    {
        return filled(config('services.google.client_id')) && filled(config('services.google.client_secret'));
    }

    public function redirect(): SymfonyRedirectResponse
    {
        abort_unless(self::enabled(), 404);

        return $this->provider()->with(['prompt' => 'select_account'])->redirect();
    }

    public function callback(Request $request): RedirectResponse
    {
        abort_unless(self::enabled(), 404);

        if ($request->filled('error')) {
            return $this->failed('Google sign-in was cancelled.');
        }

        try {
            $google = $this->provider()->user();
        } catch (Throwable $exception) {
            report($exception);

            return $this->failed('We could not sign you in with Google. Please try again.');
        }

        $googleId = (string) $google->getId();
        $email = Str::lower(trim((string) $google->getEmail()));
        if ($googleId === '' || $email === '' || ! filter_var($google->user['email_verified'] ?? false, FILTER_VALIDATE_BOOLEAN)) {
            return $this->failed('Your Google account needs a verified email address to sign in here.');
        }

        $user = User::where('google_id', $googleId)->first() ?? User::where('email', $email)->first();

        if ($user && ($user->is_admin || ! in_array($user->role, [null, 'customer'], true))) {
            return $this->failed('Staff accounts sign in through the administrator portal.');
        }

        if ($user?->google_id && $user->google_id !== $googleId) {
            return $this->failed('This email is linked to a different Google account. Sign in with your password instead.');
        }

        $guestSessionId = $request->session()->getId();
        $guestCartId = $request->session()->get('cart_id');
        $isNew = $user === null;

        if ($isNew) {
            $user = User::create([
                'name' => Str::limit(trim((string) $google->getName()) ?: Str::before($email, '@'), 255, ''),
                'email' => $email,
                // Never shown or emailed; the customer can set one with "Forgot password".
                'password' => Str::password(40),
                'referred_by_id' => session()->pull('referrer_id'),
            ]);
        }

        $user->forceFill([
            'google_id' => $googleId,
            // Google has verified the address, so no verification email is needed.
            'email_verified_at' => $user->email_verified_at ?? now(),
        ])->save();

        if ($isNew) {
            event(new Registered($user));
        }

        Auth::login($user);
        $request->session()->regenerate();
        app(CartService::class)->transferGuestCart($guestSessionId, $user, $guestCartId);

        return $isNew
            ? redirect()->route('home')->with('success', 'Welcome to Pahadi Roots. Your account is ready.')
            : redirect()->intended(route('home'))->with('success', 'Welcome back.');
    }

    private function provider(): GoogleProvider
    {
        /** @var GoogleProvider $provider */
        $provider = Socialite::driver('google');

        return $provider;
    }

    private function failed(string $message): RedirectResponse
    {
        return redirect()->route('login')->withErrors(['google' => $message]);
    }
}
