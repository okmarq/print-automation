#!/usr/bin/env bash
echo "Install NPM Packages"
npm install

echo "Build View"
npm run build

echo "Running composer"
composer install --no-dev --working-dir=/var/www/html/print-automation

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
