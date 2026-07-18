# Production deployment and operations

This runbook assumes the web server document root points to `public/`, HTTPS is terminated before Laravel, and secrets are supplied by the host rather than committed files.

## Before the first release

1. Copy `.env.production.example` outside source control and replace every placeholder. Generate `APP_KEY` once with `php artisan key:generate --show`; keep it stable and backed up.
2. Create a least-privilege MySQL user, production SMTP credentials, Stripe live keys and a live webhook signing secret for `https://your-domain/stripe/webhook`.
3. Set `APP_ENV=production`, `APP_DEBUG=false`, an HTTPS `APP_URL`, `SESSION_SECURE_COOKIE=true`, and `SESSION_ENCRYPT=true`. For sustained traffic use Redis for `SESSION_DRIVER`, `QUEUE_CONNECTION`, and `CACHE_STORE`; keep separate Redis databases/prefixes per environment.
4. Configure the web server to deny dotfiles, serve only `public/`, redirect HTTP to HTTPS, and allow Laravel's front controller fallback.
5. Back up both the database and `storage/app/public`. Test restoring both before launch.

## Release sequence

```powershell
composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction
npm ci
npm run build
php artisan migrate --force
php artisan storage:link
php artisan optimize
php artisan commerce:audit
php artisan queue:restart
```

Use maintenance mode for migrations that cannot safely run while traffic is active. Keep the previous release and its compatible assets available for rollback.

## Required background processes

- Run `php artisan schedule:run` every minute. It releases expired 30-minute Stripe inventory reservations and performs scheduled maintenance.
- Keep `php artisan queue:work --sleep=2 --tries=3 --max-time=3600` under a process supervisor. Restart workers after each release.
- Alert on failed jobs, repeated Stripe signature/payment failures, elevated application errors, low inventory and disk usage.
- The storefront caches catalogue queries behind a versioned key. Product, category, image, bundle and review changes automatically advance that version, so stale catalogue entries expire without wildcard cache scans.
- Put a CDN or reverse proxy in front of versioned Vite assets and product images. Apache cache/compression rules are included in `public/.htaccess`; mirror them if Nginx or a platform proxy serves static files.

## Launch verification

Run a customer journey in Stripe test mode first: register and verify email, add a product and bundle, apply a coupon, redeem loyalty points, pay, receive the order email, open the invoice, moderate a review, request a return and process a refund. Separately test COD serviceability, admin OTP delivery, contact mail, the sitemap, robots file, queue processing, scheduler and backups.

## Rollback

Point the web server back to the previous release, restore the previous environment/cache, run `php artisan optimize`, and restart queue workers. Do not automatically roll back a destructive database migration; restore the verified database backup or apply a reviewed forward fix. Reconcile payments received during the incident against Stripe before changing order states.

## Routine security and maintenance

- Rotate staff credentials and Stripe/API secrets on a documented schedule and immediately after exposure.
- Review administrator roles, failed logins, activity logs, refunds and coupon abuse.
- Run `composer audit`, `npm audit`, the full test suite and static analysis before every release.
- Apply framework and dependency security updates promptly after testing them in staging.
