#!/bin/bash
set -e

echo "Starting Laravel setup..."

# Wait for PostgreSQL to be ready
until php -r "new PDO('pgsql:host=' . getenv('DB_HOST') . ';dbname=' . getenv('DB_DATABASE'), getenv('DB_USERNAME'), getenv('DB_PASSWORD'))"; do
  echo "Waiting for database..."
  sleep 2
done

# Run Laravel commands
php artisan storage:link || true
php artisan migrate --force

# Start Apache
apache2-foreground
