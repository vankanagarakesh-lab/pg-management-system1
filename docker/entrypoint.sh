#!/bin/sh

# Ensure storage directories exist
mkdir -p /var/www/html/storage/framework/sessions
mkdir -p /var/www/html/storage/framework/views
mkdir -p /var/www/html/storage/framework/cache
mkdir -p /var/www/html/database

# Ensure correct permissions
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database

# Create SQLite DB if DB_CONNECTION is sqlite and file doesn't exist
if [ "$DB_CONNECTION" = "sqlite" ] || [ -z "$DB_CONNECTION" ]; then
    if [ ! -f /var/www/html/database/database.sqlite ]; then
        touch /var/www/html/database/database.sqlite
        chown www-data:www-data /var/www/html/database/database.sqlite
        chmod 664 /var/www/html/database/database.sqlite
    fi
fi

# Run database migrations and seed default data automatically
echo "Running migrations..."
php artisan migrate --force
echo "Seeding initial database content..."
php artisan db:seed --force

# Optimize Laravel
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Start background queue worker for mail & queued notifications processing
echo "Starting background queue worker..."
php artisan queue:work --tries=3 --timeout=90 --sleep=3 > /var/www/html/storage/logs/worker.log 2>&1 &

echo "Starting server..."
exec "$@"
