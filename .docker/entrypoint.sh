#!/bin/sh
set -e

# Opsional: Jalankan optimasi saat startup
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Jalankan supervisord sebagai proses utama
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf