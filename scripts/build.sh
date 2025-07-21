#!/bin/bash

set -e

echo "Building Purchase Cart Service..."

# Install dependencies
composer install --no-dev --optimize-autoloader

# Clear cache
php bin/console cache:clear --env=prod

# Create database
php bin/console doctrine:database:create --if-not-exists --env=prod

# Run migrations
php bin/console doctrine:migrations:migrate --no-interaction --env=prod

echo "Build completed successfully!"