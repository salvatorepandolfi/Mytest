#!/bin/bash

set -e

echo "Running tests for Purchase Cart Service..."

# Install dev dependencies for testing
composer install

# Clear cache for test environment
php bin/console cache:clear --env=test

# Create test database
php bin/console doctrine:database:create --if-not-exists --env=test

# Run migrations for test environment
php bin/console doctrine:migrations:migrate --no-interaction --env=test

# Run PHPUnit tests
php bin/phpunit

echo "All tests completed successfully!"