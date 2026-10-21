#!/usr/bin/env bash
# Runs on the Hostinger server after GitHub Actions has synced a release.
# PHP_BIN selects the PHP CLI (Hostinger's default `php` may differ from the
# version the website uses, e.g. /opt/alt/php82/usr/bin/php).
set -euo pipefail

cd "$(dirname "$0")/.."
PHP="${PHP_BIN:-php}"

echo "Using $("$PHP" -r 'echo PHP_VERSION;') at $(pwd)"

"$PHP" artisan migrate --force
[ -e public/storage ] || "$PHP" artisan storage:link
"$PHP" artisan optimize:clear
# Not `artisan optimize`: the store is served from the /public/ sub-directory,
# where the compiled route cache answers the home page with 405.
"$PHP" artisan config:cache
"$PHP" artisan event:cache
"$PHP" artisan view:cache
"$PHP" artisan queue:restart

echo "Release is live."
