#!/bin/bash

set -e

echo "Starting Purchase Cart Service..."

# Clear cache
php bin/console cache:clear --env=prod

# Start the built-in PHP server on port 9090
php -S 0.0.0.0:9090 -t public