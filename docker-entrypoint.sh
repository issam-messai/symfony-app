#!/bin/sh
set -e

echo "Waiting for MySQL..."
until php -r "mysqli_report(MYSQLI_REPORT_ERROR); new mysqli('mysql', 'appuser', 'apppass', 'effios');"; do
  sleep 1
done

echo "Running migrations..."
php bin/console doctrine:migrations:migrate --no-interaction

echo "Starting Apache..."
exec apache2-foreground
