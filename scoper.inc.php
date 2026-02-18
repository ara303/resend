<?php
/**
 * PHP-Scoper configuration.
 *
 * @package CloudCatch\Resend
 */

declare(strict_types=1);

use Isolated\Symfony\Component\Finder\Finder;

return [
	'prefix'                     => 'ResendWP',
	'output-dir'                 => 'vendor-prefixed',
	'finders'                    => [
		Finder::create()
			->files()
			->ignoreVCS( true )
			->notName( '/.*\\.md|.*\\.dist|Makefile|composer\\.json|composer\\.lock/' )
			->exclude(
				[
					'doc',
					'test',
					'test_old',
					'tests',
					'Tests',
					'vendor-bin',
					'phpstan',
					'phpunit',
					'phpcs',
					'php-cs-fixer',
					'php-scoper',
				]
			)
			->in( 'vendor' ),
	],
	'patchers'                   => [
		static function ( string $file_path, string $prefix, string $contents ): string {
			// Fix Composer autoloader unregister issue.
			if ( strpos( $file_path, 'composer/autoload_real.php' ) !== false ) {
				return str_replace(
					"spl_autoload_unregister(array('ComposerAutoloaderInit",
					"spl_autoload_unregister(array('" . $prefix . "\\\\ComposerAutoloaderInit",
					$contents
				);
			}
			// Fix PSR-4 prefix map strings in autoload_static.php.
			// PHP-Scoper scopes the source files but does not rewrite the array
			// key strings (e.g. 'Monolog\\') in the Composer-generated static
			// autoload map. We prefix them here so the ClassLoader can resolve
			// the already-scoped namespaces to their file-system paths.
			if ( strpos( $file_path, 'composer/autoload_static.php' ) !== false ) {
				// Match string keys that look like a namespace root and are not yet prefixed.
				// In the raw PHP file, namespace separator is ONE backslash and the
				// trailing marker is TWO backslashes, e.g. 'Psr\Log\\' or 'Monolog\\'.
				$contents = preg_replace_callback(
					"/'([A-Z][a-zA-Z0-9]*(?:\\\\[A-Za-z0-9]+)*\\\\\\\\)'/",
					static function ( array $match ) use ( $prefix ): string {
						// Skip if already prefixed or is a Composer internal namespace.
						if (
							strpos( $match[1], $prefix . '\\' ) === 0 ||
							strpos( $match[1], 'Composer\\' ) === 0
						) {
							return $match[0];
						}
						return "'" . $prefix . '\\' . $match[1] . "'";
					},
					$contents
				);
			}
			return $contents;
		},
	],
	'exclude-namespaces'         => [
		'CloudCatch',
		'CloudCatch\Resend',
		'Composer\Autoload',
	],
	'exclude-classes'            => [
		'Composer\InstalledVersions',
	],
];
