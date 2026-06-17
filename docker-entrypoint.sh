#!/bin/bash
set -e

# Install composer dependencies if vendor folder doesn't exist
if [ ! -d /var/www/html/vendor ]; then
    echo "Installing composer dependencies..."
    composer install --no-interaction --optimize-autoloader
fi

# Generate application key if not set
if ! grep -q "APP_KEY=base64:" /var/www/html/.env 2>/dev/null; then
    echo "Generating application key..."
    php artisan key:generate --ansi
fi

# Run database migrations if needed
# php artisan migrate --force

# Execute the main command
exec "$@"
