#!/bin/bash
# Build script to prefix vendor dependencies with PHP-Scoper
# This resolves conflicts with other WordPress plugins (like WPForms)

set -e

# Skip if called from post-install-cmd to prevent recursion
if [ "$COMPOSER_SCRIPT_NAME" = "post-install-cmd" ] || [ "$COMPOSER_SCRIPT_NAME" = "post-update-cmd" ]; then
    # Only run if vendor-prefixed doesn't exist (first install)
    if [ -d "vendor-prefixed" ]; then
        echo "Skipping prefix-dependencies (already exists)"
        exit 0
    fi
fi

echo "=================================="
echo "Building with prefixed dependencies"
echo "=================================="

# Clean up previous builds
echo "Cleaning up..."
rm -rf vendor-prefixed

# Make sure all dependencies are installed (including dev for php-scoper)
echo "Installing all dependencies..."
composer install --quiet --no-scripts

# Temporarily save vendor to vendor-all
echo "Preparing production dependencies..."
mv vendor vendor-all

# Install only production dependencies
composer install --no-dev --optimize-autoloader --quiet --no-scripts

# Run PHP-Scoper to prefix production dependencies
echo "Running PHP-Scoper to prefix production dependencies..."
php -d memory_limit=512M vendor-all/bin/php-scoper add-prefix --output-dir=./vendor-prefixed --force --quiet

# Restore all dependencies
mv vendor-all vendor

echo "=================================="
echo "Build complete! Dependencies prefixed with 'ResendWP'"
echo "=================================="
