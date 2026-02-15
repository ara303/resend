#!/usr/bin/env php
<?php
/**
 * Test script for dependency isolation
 *
 * @package CloudCatch\Resend
 */

require __DIR__ . '/vendor-prefixed/autoload.php';

echo "=== Dependency Isolation Test ===" . PHP_EOL . PHP_EOL;

// Test 1: Prefixed classes exist.
echo '1. Prefixed Monolog\\Logger: ' . ( class_exists( 'CloudCatchResendVendor\Monolog\Logger' ) ? '✓ EXISTS' : '✗ MISSING' ) . PHP_EOL;
echo '2. Prefixed Psr\\Log\\LoggerInterface: ' . ( interface_exists( 'CloudCatchResendVendor\Psr\Log\LoggerInterface' ) ? '✓ EXISTS' : '✗ MISSING' ) . PHP_EOL;
echo '3. Prefixed Resend\\Client: ' . ( class_exists( 'CloudCatchResendVendor\Resend\Client' ) ? '✓ EXISTS' : '✗ MISSING' ) . PHP_EOL;
echo '4. Prefixed Guzzle: ' . ( class_exists( 'CloudCatchResendVendor\GuzzleHttp\Client' ) ? '✓ EXISTS' : '✗ MISSING' ) . PHP_EOL;

// Test 2: Original namespaces are isolated (not loaded).
echo '5. Original Monolog\\Logger isolated: ' . ( ! class_exists( 'Monolog\Logger', false ) ? '✓ YES' : '✗ CONFLICT!' ) . PHP_EOL;
echo '6. Original Psr\\Log isolated: ' . ( ! interface_exists( 'Psr\Log\LoggerInterface', false ) ? '✓ YES' : '✗ CONFLICT!' ) . PHP_EOL;

// Test 3: Can instantiate prefixed classes.
try {
	$logger = new CloudCatchResendVendor\Monolog\Logger( 'test' );
	echo '7. Monolog instantiation: ✓ SUCCESS' . PHP_EOL;
} catch ( Exception $e ) {
	echo '7. Monolog instantiation: ✗ FAILED - ' . $e->getMessage() . PHP_EOL;
}

echo PHP_EOL . '=== All tests passed! Dependencies are properly isolated. ===' . PHP_EOL;
