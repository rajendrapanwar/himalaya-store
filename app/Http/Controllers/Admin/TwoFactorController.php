<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AdminTwoFactorService;
use Illuminate\Http\Request;

class TwoFactorController extends Controller
{
    public function show()
    {
        return view('admin.auth.two-factor');
    }

    public function verify(Request $request, AdminTwoFactorService $service)
    {
        $data = $request->validate(['code' => 'required|digits:6']);
        if (! $service->verify($request->user(), $data['code'])) {
            return back()->withErrors(['code' => 'The code is invalid or has expired.']);
        }
        $request->session()->forget('admin_2fa_local_code');
        $request->session()->put('admin_2fa_verified_at', now()->timestamp);

        return redirect()->intended(route('admin.dashboard'))->with('success', 'Administrator verification completed.');
    }

    public function resend(Request $request, AdminTwoFactorService $service)
    {
        $code = $service->send($request->user());
        if (app()->environment('local')) {
            $request->session()->put('admin_2fa_local_code', $code);
        }

        return back()->with('success', 'A new code has been sent to your administrator email.');
    }
}
