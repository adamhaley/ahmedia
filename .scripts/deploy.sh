#!/bin/bash
set -e

echo "Deploying .."

git pull origin master

composer install --no-dev --optimize-autoloader --no-interaction

php artisan migrate --force
php artisan storage:link --force

php artisan config:cache
php artisan route:cache
php artisan view:cache

# .env lives only on the server (gitignored) and persists across deploys
# untouched -- never written by this script.
