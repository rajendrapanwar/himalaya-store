<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AdminTwoFactorService;
use App\Services\CartService;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;

class AuthController extends Controller
{
    public function create()
    {
        return view('frontend.auth.register');
    }

    public function store(Request $request)
    {
        $guestSessionId = $request->session()->getId();
        $guestCartId = $request->session()->get('cart_id');
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', PasswordRule::min(10)->letters()->mixedCase()->numbers()],
        ]);
        $referrerId = session('referrer_id');
        $user = User::create($data + ['referral_code' => $this->uniqueReferralCode(), 'referred_by_id' => $referrerId]);
        session()->forget('referrer_id');
        event(new Registered($user));
        Auth::login($user);
        $request->session()->regenerate();
        app(CartService::class)->transferGuestCart($guestSessionId, $user, $guestCartId);

        return redirect()->route('home')->with('success', 'Welcome to Pahadi Roots. Please verify your email before checkout.');
    }

    public function login()
    {
        return view('frontend.auth.login');
    }

    public function adminLogin()
    {
        return view('admin.auth.login');
    }

    public function authenticate(Request $request)
    {
        $guestSessionId = $request->session()->getId();
        $guestCartId = $request->session()->get('cart_id');
        $credentials = $request->validate(['email' => ['required', 'email'], 'password' => ['required']]);
        if (! Auth::attempt([
            ...$credentials,
            fn ($query) => $query->where('is_admin', false)->where(function ($roleQuery) {
                $roleQuery->whereNull('role')->orWhere('role', 'customer');
            }),
        ], $request->boolean('remember'))) {
            return back()->withErrors(['email' => 'The supplied email or password is incorrect.'])->onlyInput('email');
        }
        $request->session()->regenerate();
        app(CartService::class)->transferGuestCart($guestSessionId, $request->user(), $guestCartId);

        return redirect()->intended(route('home'))->with('success', 'Welcome back.');
    }

    public function authenticateAdmin(Request $request)
    {
        $credentials = $request->validate(['email' => ['required', 'email'], 'password' => ['required']]);
        if (! Auth::attempt([
            ...$credentials,
            fn ($query) => $query->where(function ($staffQuery) {
                $staffQuery->where('is_admin', true)->orWhereIn('role', ['administrator', 'manager', 'catalogue', 'fulfillment', 'support']);
            }),
        ], $request->boolean('remember'))) {
            return back()->withErrors(['email' => 'The supplied administrator credentials are incorrect.'])->onlyInput('email');
        }

        $request->session()->regenerate();
        $request->session()->forget('admin_2fa_verified_at');
        $code = app(AdminTwoFactorService::class)->send($request->user());
        if (app()->environment('local')) {
            $request->session()->put('admin_2fa_local_code', $code);
        }

        return redirect()->route('admin.two-factor.show');
    }

    public function destroy(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'You have been signed out.');
    }

    public function refer(string $code)
    {
        $referrer = User::where('referral_code', strtoupper($code))->firstOrFail();
        session(['referrer_id' => $referrer->id]);

        return redirect()->route('register')->with('success', 'Referral saved. Create your account to receive your welcome benefit after your first qualifying order.');
    }

    public function forgotPassword()
    {
        return view('frontend.auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        Password::sendResetLink($request->only('email'));

        return back()->with('success', 'If an account exists for that email address, a password reset link has been sent.');
    }

    public function resetPassword(Request $request, string $token)
    {
        return view('frontend.auth.reset-password', ['token' => $token, 'email' => $request->email]);
    }

    public function updatePassword(Request $request)
    {
        $request->validate(['token' => 'required', 'email' => 'required|email', 'password' => ['required', 'confirmed', PasswordRule::min(10)->letters()->mixedCase()->numbers()]]);
        $status = Password::reset($request->only('email', 'password', 'password_confirmation', 'token'), function (User $user, string $password) {
            $user->forceFill(['password' => Hash::make($password), 'remember_token' => Str::random(60)])->save();
            event(new PasswordReset($user));
        });

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('success', __($status))
            : back()->withErrors(['email' => __($status)]);
    }

    public function verificationNotice()
    {
        return view('frontend.auth.verify-email');
    }

    public function verifyEmail(EmailVerificationRequest $request)
    {
        $request->fulfill();

        return redirect()->route('home')->with('success', 'Your email address has been verified.');
    }

    public function sendVerification(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return back()->with('success', 'Your email is already verified.');
        }
        $request->user()->sendEmailVerificationNotification();

        return back()->with('success', 'A new verification link has been sent.');
    }

    private function uniqueReferralCode(): string
    {
        do {
            $code = Str::upper(Str::random(8));
        } while (User::where('referral_code', $code)->exists());

        return $code;
    }
}
