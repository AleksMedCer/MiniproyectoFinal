#!/usr/bin/env bash
set -euo pipefail

php artisan config:clear
php artisan migrate --force
php artisan db:seed --force
php artisan demo:seed-users
php artisan mail:diagnose
php artisan config:cache
