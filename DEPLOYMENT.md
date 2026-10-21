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

## Continuous deployment to Hostinger (GitHub Actions)

Every push to `main` runs the CI tests and, only if they pass, the `deploy` job in `.github/workflows/ci.yml`:

1. builds the release on GitHub (`composer install --no-dev`, `npm ci && npm run build`);
2. puts the site into maintenance mode (`php artisan down`);
3. syncs the files to Hostinger over SSH with `rsync` — `.env`, `storage/`, `public/storage` and the other paths in `deploy/rsync-exclude.txt` are never uploaded or deleted;
4. runs `deploy/hostinger-post-deploy.sh` on the server (migrations, `storage:link`, cache rebuild, `queue:restart`);
5. brings the site back online, even if a step failed.

Until the secrets below exist, the deploy job is skipped with a warning and CI still passes.

### One-time setup

1. **Enable SSH** in hPanel → *Advanced → SSH Access*. Note the **IP**, **port** (usually `65002`) and **username** (`u123456789`).
2. **Create a deploy key** on your computer (no passphrase, used only by GitHub):
   ```bash
   ssh-keygen -t ed25519 -C "github-deploy" -f hostinger_deploy -N ""
   ```
3. In hPanel → *SSH Access → SSH keys*, **add the public key** (the contents of `hostinger_deploy.pub`).
4. **Find the deploy path**: connect with `ssh -p 65002 u123456789@IP`, `cd` into the folder that contains `artisan` (normally `domains/<your-domain>/public_html`) and run `pwd`.
5. In GitHub → repository *Settings → Secrets and variables → Actions*, add these **repository secrets**:

   | Secret | Value |
   |---|---|
   | `HOSTINGER_SSH_HOST` | the SSH IP from step 1 |
   | `HOSTINGER_SSH_PORT` | `65002` |
   | `HOSTINGER_SSH_USER` | `u123456789` |
   | `HOSTINGER_SSH_KEY` | the full contents of the private key file `hostinger_deploy` |
   | `HOSTINGER_DEPLOY_PATH` | the path from step 4, e.g. `/home/u123456789/domains/<your-domain>/public_html` |
   | `HOSTINGER_SSH_KNOWN_HOSTS` | *(optional, recommended)* output of `ssh-keyscan -p 65002 IP`, to pin the server's identity |

   If `php -v` over SSH shows a version older than the website's PHP 8.2+, also add a repository **variable** `HOSTINGER_PHP_BIN` set to the matching binary, e.g. `/opt/alt/php82/usr/bin/php`.
6. Delete the local private key file once it is stored in GitHub.
7. Optional: create a GitHub **environment** named `production` with required reviewers to approve each deployment.

### Server prerequisites

- A production `.env` already exists in the deploy path (it is never overwritten). Keep `APP_ENV=production` and `APP_DEBUG=false`.
- In hPanel → *Advanced → Cron Jobs*, run the scheduler every minute, and — because emails and notifications are queued — a short-lived queue worker:
  ```bash
  cd /home/u123456789/domains/<your-domain>/public_html && php artisan schedule:run >> /dev/null 2>&1
  ```
  ```bash
  cd /home/u123456789/domains/<your-domain>/public_html && php artisan queue:work --stop-when-empty --tries=3 --max-time=55 >> /dev/null 2>&1
  ```
  (Shared hosting has no Redis, so use `QUEUE_CONNECTION=database` and `CACHE_STORE=database` there.)

### Shared-hosting document root

On Hostinger the project root is the web root and the store is served from `/public/`. The root `.htaccess` redirects the bare domain to `/public/` and returns 403 for every other path — `storage/logs`, `vendor/`, `composer.json`, deploy scripts — so application files are never downloadable.

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
