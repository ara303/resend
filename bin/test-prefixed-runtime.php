#!/usr/bin/env php
<?php
/**
 * Runtime smoke test for prefixed dependencies.
 *
 * @package CloudCatch\Resend
 */

declare(strict_types=1);

$root = dirname(__DIR__);

$autoload = $root . '/vendor-prefixed/autoload.php';
if (! is_readable($autoload)) {
	throw new RuntimeException('Missing prefixed autoloader: ' . $autoload);
}

require $autoload;

$checks = [
	['ResendWP\\Monolog\\Logger', 'class'],
	['ResendWP\\Monolog\\Handler\\StreamHandler', 'class'],
	['ResendWP\\Psr\\Log\\LoggerInterface', 'interface'],
	['ResendWP\\Resend\\Client', 'class'],
];

foreach ($checks as [$symbol, $type]) {
	$exists = $type === 'interface' ? interface_exists($symbol) : class_exists($symbol);

	if (! $exists) {
		$staticMapFile = $root . '/vendor-prefixed/composer/autoload_static.php';
		$staticMap = is_readable($staticMapFile) ? file_get_contents($staticMapFile) : false;
		$hasExpectedPrefix = is_string($staticMap)
			? str_contains($staticMap, "'ResendWP\\\\Monolog\\\\'")
			: false;
		$hasUnprefixedPrefix = is_string($staticMap)
			? str_contains($staticMap, "'Monolog\\\\'")
			: false;

		throw new RuntimeException(
			sprintf(
				"Missing %s %s. autoload_static.php has ResendWP Monolog prefix: %s; has unprefixed Monolog prefix: %s",
				$type,
				$symbol,
				$hasExpectedPrefix ? 'yes' : 'no',
				$hasUnprefixedPrefix ? 'yes' : 'no'
			)
		);
	}
}

echo "PASS: Expected prefixed active\n";
