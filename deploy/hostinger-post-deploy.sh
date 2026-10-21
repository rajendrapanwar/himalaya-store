#!/usr/bin/env bash
# Runs on the Hostinger server after GitHub Actions has synced a release.
# PHP_BIN selects the PHP CLI (Hostinger's default `php` may differ from the
# version the website uses, e.g. /opt/alt/php82/usr/bin/php).
set -euo pipefail

cd "$(dirname "$0")/.."
PHP="${PHP_BIN:-php}"

echo "Using $("$PHP" -r 'echo PHP_VERSION;') at $(pwd)"

"$PHP" artisan migrate --force
"$PHP" artisan storage:link 2>/dev/null || true
"$PHP" artisan optimize:clear
"$PHP" artisan optimize
"$PHP" artisan queue:restart

echo "Release is live."
