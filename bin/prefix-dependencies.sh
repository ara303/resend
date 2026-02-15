#!/bin/bash
# Build script to prefix vendor dependencies with PHP-Scoper
# This resolves conflicts with other WordPress plugins (like WPForms)

set -e

echo "=================================="
echo "Building with prefixed dependencies"
echo "=================================="

# Clean up previous builds
echo "Cleaning up..."
rm -rf vendor-prefixed

# Make sure all dependencies are installed (including dev for php-scoper)
echo "Installing all dependencies..."
composer install --quiet

# Temporarily save vendor to vendor-all
echo "Preparing production dependencies..."
mv vendor vendor-all

# Install only production dependencies
composer install --no-dev --optimize-autoloader --quiet

# Run PHP-Scoper to prefix production dependencies
echo "Running PHP-Scoper to prefix production dependencies..."
php -d memory_limit=512M vendor-all/bin/php-scoper add-prefix --output-dir=./vendor-prefixed --force --quiet

# Restore all dependencies
mv vendor-all vendor

echo "=================================="
echo "Build complete! Dependencies prefixed with 'CloudCatchResendVendor'"
echo "=================================="
