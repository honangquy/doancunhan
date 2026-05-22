#!/bin/sh
set -e

APP_USER="${APP_USER:-laravel}"
APP_GROUP="${APP_GROUP:-laravel}"

# Ensure runtime directories exist before fixing ownership/permissions.
mkdir -p /var/www/storage/framework/{sessions,views,cache} \
         /var/www/storage/logs \
         /var/www/storage/app/public \
         /var/www/bootstrap/cache

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
        } catch (Throwable \$e) {
            exit(1);
        }
    "; do
        sleep 2
    done
    echo "MySQL is ready."
fi

# Run DB migrations in non-interactive mode for containerized deployments.
php /var/www/artisan migrate --force

# Create the storage symlink if it doesn't exist yet.
php /var/www/artisan storage:link 2>/dev/null || true

# Production optimizations: cache config, routes, and views.
if [ "${APP_ENV}" = "production" ]; then
    echo "Running production optimizations..."
    php /var/www/artisan config:cache
    php /var/www/artisan route:cache
    php /var/www/artisan view:cache
    echo "Optimization complete."
fi

exec "$@"
