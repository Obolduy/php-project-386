#!/bin/sh
set -e

if [ ! -f .env ]; then
    cp .env.example .env
fi

if [ -z "$APP_KEY" ] && ! grep -q '^APP_KEY=base64:' .env; then
    php artisan key:generate --force
fi

touch "$DB_DATABASE"
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache

exec php artisan serve --host=0.0.0.0 --port="$PORT" --no-reload
