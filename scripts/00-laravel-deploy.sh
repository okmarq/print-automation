#!/usr/bin/env bash
echo "Running composer"
composer install --no-dev

echo "Caching config..."
php artisan config:cache

echo "Caching routes..."
php artisan route:cache

echo "Running migrations..."
php artisan migrate:fresh --force --seed

echo "Link storage"
php artisan storage:link

echo "Running Queue"
php artisan queue:work
