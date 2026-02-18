<?php
/**
 * Test the backslash escaping of prefixes is right.
 *
 * @package CloudCatch\Resend
 *
 * phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
 * phpcs:disable WordPress.Security.EscapeOutput.ExceptionNotEscaped
 * phpcs:disable WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
 */

declare(strict_types=1);

$root = dirname( __DIR__ );

$autoload = $root . '/vendor-prefixed/autoload.php';
if ( ! is_readable( $autoload ) ) {
	throw new RuntimeException( 'Missing prefixed autoloader: ' . $autoload );
}

require $autoload;

$checks = array(
	array( 'ResendWP\\Monolog\\Logger', 'class' ),
	array( 'ResendWP\\Monolog\\Handler\\StreamHandler', 'class' ),
	array( 'ResendWP\\Psr\\Log\\LoggerInterface', 'interface' ),
	array( 'ResendWP\\Resend\\Client', 'class' ),
);

foreach ( $checks as [$symbol, $symbol_type] ) {
	$exists = 'interface' === $symbol_type ? interface_exists( $symbol ) : class_exists( $symbol );

	if ( ! $exists ) {
		$static_map_file       = $root . '/vendor-prefixed/composer/autoload_static.php';
		$static_map            = is_readable( $static_map_file ) ? file_get_contents( $static_map_file ) : false;
		$has_expected_prefix   = is_string( $static_map )
			? str_contains( $static_map, "'ResendWP\\\\Monolog\\\\'" )
			: false;
		$has_unexpected_prefix = is_string( $static_map )
			? str_contains( $static_map, "'Monolog\\\\'" )
			: false;

		throw new RuntimeException(
			sprintf(
				'Missing %s %s. autoload_static.php has ResendWP Monolog prefix: %s; has unprefixed Monolog prefix: %s',
				$symbol_type,
				$symbol,
				$has_expected_prefix ? 'yes' : 'no',
				$has_unexpected_prefix ? 'yes' : 'no'
			)
		);
	}
}

echo "PASS: Expected prefixed active\n";
