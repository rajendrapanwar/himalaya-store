# Security operations

The application implements CSRF protection, authorization middleware, administrator two-factor verification, Stripe signature validation and webhook idempotency, upload type/size validation, escaped template output, private-page cache controls, restrictive browser headers, trusted-host validation, and named rate limits on every state-changing web route.

## Production requirements

- Point the domain document root at `public/`. Never expose the project root, `.env`, `storage/`, `vendor/`, database exports, logs, or deployment archives through the web server.
- Set `APP_ENV=production`, `APP_DEBUG=false`, the exact HTTPS `APP_URL`, a unique generated `APP_KEY`, `SESSION_SECURE_COOKIE=true`, and `SESSION_ENCRYPT=true`.
- Redirect HTTP to HTTPS at the host or proxy. Keep TLS certificates renewed. HSTS is emitted only for HTTPS requests.
- Use Redis for `CACHE_STORE`, `SESSION_DRIVER`, and `QUEUE_CONNECTION` where available. At minimum, use a shared database cache in a multi-server deployment so rate-limit counters are consistent across workers.
- Configure the real Stripe webhook secret and restrict Stripe dashboard events to those handled by the application. Rotate any key that has appeared in source control, chat, screenshots, support tickets, or logs.
- Use a dedicated, least-privilege database account. Do not grant schema administration privileges to the normal web process after migrations are complete.
- Keep `storage/` and `bootstrap/cache/` writable by the PHP process; application source and `.env` should not be world-writable. Disable directory listing and PHP execution inside public upload directories at the server level.
- Run the queue worker under a process manager and the Laravel scheduler every minute. Monitor failed jobs, mail delivery, application errors, admin audit activity, payment failures, and repeated HTTP 429 responses.
- Back up the database and uploaded media off-host, encrypt backups, test restores, and define retention and incident-response procedures.

## Release checks

Run these before each deployment:

```bash
composer install --no-dev --prefer-dist --optimize-autoloader
composer audit --locked
npm audit --omit=dev
npm run build
php artisan test
php artisan optimize
```

After deployment, verify `/up`, login, password reset email, checkout, a signed Stripe test webhook, uploads, queues, and HTTPS response headers. Never cache account, cart, checkout, order, wishlist, notification, authentication, or admin responses at a CDN.

Report suspected vulnerabilities privately to the address configured by `SUPPORT_EMAIL`; do not include passwords, API keys, payment details, or personal data in a report.
