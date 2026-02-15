<?php
/**
 * PHP-Scoper configuration.
 *
 * @package CloudCatch\Resend
 */

declare(strict_types=1);

use Isolated\Symfony\Component\Finder\Finder;

return [
	'prefix'                     => 'CloudCatchResendVendor',
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
	'exclude-functions'          => [],
	'exclude-constants'          => [],
	'expose-global-constants'    => true,
	'expose-global-classes'      => false,
	'expose-global-functions'    => false,
	'expose-namespaces'          => [],
	'expose-classes'             => [],
	'expose-functions'           => [],
	'expose-constants'           => [],
];
