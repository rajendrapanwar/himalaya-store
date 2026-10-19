# Pahadi Roots

A Laravel 12 commerce application for authentic products from Uttarakhand and the Himalayan region. It includes product and bundle merchandising, inventory reservation, Stripe Checkout (card and UPI), COD, coupons, loyalty/referrals, reviews, wishlist, pincode serviceability, returns, customer support and a role-based admin workspace.

## Local setup (XAMPP / Windows)

Requirements: PHP 8.2+, Composer, Node.js 22+, MySQL and the PHP extensions Laravel requires.

```powershell
composer install
Copy-Item .env.example .env
php artisan key:generate
npm install
php artisan migrate --seed
php artisan storage:link
npm run build
```

Point Apache at the `public` directory, or run `php artisan serve`. Start the queue worker in another terminal with `php artisan queue:work --tries=3`, and run `php artisan schedule:work` locally so expired Stripe reservations are released.

The seeded admin signs in at `/admin/login` using `ADMIN_EMAIL` and is sent through email two-factor verification before `/admin/dashboard`. Customer accounts use `/login` and cannot authenticate through the administrator portal. Set both `ADMIN_EMAIL` and `ADMIN_INITIAL_PASSWORD` before seeding. Never use placeholder credentials in a shared environment.

## Stripe test mode and local webhooks

Put credentials only in `.env`:

```dotenv
STRIPE_KEY=pk_test_...
STRIPE_SECRET=sk_test_...
STRIPE_WEBHOOK_SECRET=whsec_...
```

Install the Stripe CLI, authenticate with `stripe login`, then forward events:

```powershell
stripe listen --forward-to http://127.0.0.1:8000/stripe/webhook
```

Copy the displayed `whsec_...` signing secret to `STRIPE_WEBHOOK_SECRET`, run `php artisan config:clear`, and keep the listener open. Test with Stripe test cards or the supported UPI test flow. The webhook handler validates signatures, amount/currency, event IDs and safely ignores duplicate deliveries.

The Stripe secret previously shared in chat should be rotated in the Stripe Dashboard before any real use. Do not commit `.env`.

## Verification

```powershell
php artisan test
vendor\bin\pint --test
composer analyse
php artisan view:cache
php artisan route:list --except-vendor
npm run build
php artisan commerce:audit
```

Tests use an in-memory SQLite database and cover authentication, permissions, coupons, loyalty, COD, bundles, inventory release/commit, signed Stripe webhooks and idempotency, contact messages, reviews, wishlist and the 30-day return window. GitHub Actions repeats PHP tests, formatting and the production asset build.

## Production runbook

Use `.env.production.example` as a checklist; do not deploy it unchanged.

1. Configure HTTPS, `APP_ENV=production`, `APP_DEBUG=false`, a generated `APP_KEY`, secure database credentials and `SESSION_SECURE_COOKIE=true`.
2. Configure a real SMTP provider and keep a queue worker under Supervisor/systemd. Order, payment, shipping, delivery, return and admin-OTP messages depend on the queue/mail configuration.
3. Run `php artisan migrate --force`, `php artisan storage:link`, `npm ci && npm run build`, then `php artisan optimize` during deployment.
4. Run the scheduler every minute (`php artisan schedule:run`) so 30-minute payment reservations expire. Keep a persistent queue worker and restart it after deployments.
5. Create a Stripe live-mode webhook for `/stripe/webhook`, subscribe to checkout session completion/failure/expiry and refund events, and store its signing secret in the environment.
6. Run `php artisan commerce:audit`, make a test order, verify email delivery, invoice output, inventory, coupon redemption and webhook logs before opening traffic.
7. Back up the database and uploaded storage, monitor `storage/logs`, failed jobs, Stripe webhook failures and low inventory. Use `php artisan queue:failed` and retry only after correcting the cause.

## Important business configuration

- Serviceable pincodes, COD availability, delivery estimates and shipping charges are managed under **Admin → Delivery areas**.
- Coupon eligibility and limits are managed under **Admin → Coupons**.
- Loyalty conversion and referral rewards use the `LOYALTY_*` and `REFERRER_*` environment values.
- Customers may request a return for 30 days after `delivered_at`; staff approve/reject it, issue the refund from the order, then mark the request complete.
- Add a professionally verified GSTIN and invoicing/tax treatment before issuing tax invoices. Product prices are currently treated as final storefront prices; consult an Indian tax professional before launch.

No deployment, commit or push is performed by the application setup.

Catalogue photography sources and license records are maintained in `THIRD_PARTY_ASSETS.md`.
