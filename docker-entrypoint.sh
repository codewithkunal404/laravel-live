#!/bin/bash
set -e

echo "Starting Laravel setup..."

# Optional: wait for PostgreSQL to be ready
until php -r "new PDO('pgsql:host=' . env('DB_HOST') . ';dbname=' . env('DB_DATABASE'), env('DB_USERNAME'), env('DB_PASSWORD'))"; do
  echo "Waiting for database..."
  sleep 2
done

# Run Laravel commands
php artisan storage:link || true
php artisan migrate --force

# Start Apache
apache2-foreground
