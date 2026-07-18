<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AuditCommerceConfiguration extends Command
{
    protected $signature = 'commerce:audit';

    protected $description = 'Check production-critical commerce configuration and connectivity';

    public function handle(): int
    {
        $checks = [
            'Application key' => filled(config('app.key')),
            'Debug disabled outside local' => app()->environment('local', 'testing') || ! config('app.debug'),
            'Stripe secret' => filled(config('services.stripe.secret')) && ! str_contains((string) config('services.stripe.secret'), 'replace'),
            'Stripe webhook secret' => str_starts_with((string) config('services.stripe.webhook_secret'), 'whsec_'),
            'Support email' => filter_var(config('services.support_email'), FILTER_VALIDATE_EMAIL) !== false,
            'Queue is asynchronous in production' => ! app()->environment('production') || config('queue.default') !== 'sync',
            'HTTPS application URL in production' => ! app()->environment('production') || str_starts_with(config('app.url'), 'https://'),
            'Production mailer sends real email' => ! app()->environment('production') || ! in_array(config('mail.default'), ['array', 'log'], true),
            'Secure session cookie in production' => ! app()->environment('production') || config('session.secure') === true,
            'Session encryption in production' => ! app()->environment('production') || config('session.encrypt') === true,
            'Public storage is writable' => is_writable(storage_path('app/public')),
            'Log storage is writable' => is_writable(storage_path('logs')),
        ];
        try {
            DB::select('select 1');
            $checks['Database connection'] = true;
        } catch (\Throwable) {
            $checks['Database connection'] = false;
        }
        $this->table(['Check', 'Result'], collect($checks)->map(fn ($ok, $name) => [$name, $ok ? 'PASS' : 'FAIL'])->values()->all());

        return in_array(false, $checks, true) ? self::FAILURE : self::SUCCESS;
    }
}
