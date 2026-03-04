#!/bin/bash
set -e

# Fix git ownership: files are mounted from host (different uid than www-data).
# Without this, git refuses to run and crashes Rector's parallel workers.
git config --global --add safe.directory /var/www

chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

exec "$@"
