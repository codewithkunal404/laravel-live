#!/bin/bash
set -e

echo "Starting Laravel setup..."

# Wait for PostgreSQL to be ready
until php -r "
try {
    \$pdo = new PDO(
        'pgsql:host=' . getenv('DB_HOST') . ';port=' . getenv('DB_PORT') . ';dbname=' . getenv('DB_DATABASE'),
        getenv('DB_USERNAME'),
        getenv('DB_PASSWORD')
    );
} catch (PDOException \$e) {
    exit(1);
}
"; do
    echo "Waiting for database..."
    sleep 2
done

echo "Database is ready!"

# Run Laravel commands
php artisan storage:link || true
php artisan migrate --force

# Start Apache
apache2-foreground
