<?php

namespace App\Services;

use App\Models\User;
use App\Notifications\AdminLoginCode;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;

class AdminTwoFactorService
{
    public function send(User $user): string
    {
        $code = (string) random_int(100000, 999999);
        Cache::put($this->key($user), Hash::make($code), now()->addMinutes(10));
        $user->notifyNow(new AdminLoginCode($code));

        return $code;
    }

    public function verify(User $user, string $code): bool
    {
        $hash = Cache::get($this->key($user));
        if (! $hash || ! Hash::check($code, $hash)) {
            return false;
        }
        Cache::forget($this->key($user));

        return true;
    }

    private function key(User $user): string
    {
        return "admin-2fa:{$user->id}";
    }
}
