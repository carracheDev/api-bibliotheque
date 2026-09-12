#!/bin/sh
set -e

php artisan migrate:fresh --force
php artisan serve --host=0.0.0.0 --port="${PORT:-8000}"