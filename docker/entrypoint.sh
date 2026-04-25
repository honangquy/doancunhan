#!/bin/sh
set -e

APP_USER="${APP_USER:-laravel}"
APP_GROUP="${APP_GROUP:-laravel}"

# Ensure runtime directories exist before fixing ownership/permissions.
mkdir -p /var/www/storage /var/www/bootstrap/cache

chown -R "${APP_USER}:${APP_GROUP}" /var/www/storage /var/www/bootstrap/cache
chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Wait for MySQL if DB_HOST is set (best effort for local startup ordering).
if [ -n "${DB_HOST}" ] && [ -n "${DB_PORT}" ]; then
    echo "Waiting for MySQL at ${DB_HOST}:${DB_PORT}..."
    until php -r "
        try {
            new PDO(
                'mysql:host=' . getenv('DB_HOST') . ';port=' . getenv('DB_PORT') . ';dbname=' . getenv('DB_DATABASE'),
                getenv('DB_USERNAME'),
                getenv('DB_PASSWORD')
            );
            exit(0);
        } catch (Throwable $e) {
            exit(1);
        }
    "; do
        sleep 2
    done
fi

# Run DB migrations in non-interactive mode for containerized deployments.
php /var/www/artisan migrate --force

exec "$@"
