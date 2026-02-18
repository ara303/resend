<?php
/**
 * PHP-Scoper configuration.
 *
 * @package CloudCatch\Resend
 */

declare(strict_types=1);

use Isolated\Symfony\Component\Finder\Finder;

$vendor_dir = getenv( 'SCOPER_VENDOR_DIR' );

if ( ! is_string( $vendor_dir ) || '' === trim( $vendor_dir ) ) {
	$vendor_dir = 'vendor';
}

return [
	'prefix'                     => 'ResendWP',
	'output-dir'                 => 'vendor-prefixed',
	'finders'                    => [
		Finder::create()
			->files()
			->in( __DIR__ . '/' . $vendor_dir )
			->ignoreVCS( true )
			->notName( '/LICENSE|.*\\.md|.*\\.dist|Makefile|composer\\.(json|lock)/' )
			->exclude(
				[
					'doc',
					'test',
					'test_old',
					'tests',
					'Tests',
					'vendor-bin',
					'.github',
				]
			),
	],
	'patchers'                   => [
		static function ( string $file_path, string $prefix, string $contents ): string {
			if ( false === strpos( $file_path, 'composer/autoload_real.php' ) ) {
				return $contents;
			}

			$contents = str_replace(
				"'Composer\\\\Autoload\\\\ClassLoader'",
				"'" . $prefix . "\\\\Composer\\\\Autoload\\\\ClassLoader'",
				$contents
			);

			$contents = preg_replace_callback(
				'/^(\s*\\\\call_user_func\([^\n]+::getInitializer\(\$loader\)\);)$/m',
				static function ( array $matches ): string {
					return $matches[1] . "\n"
						. "        foreach (\$loader->getPrefixesPsr4() as \$namespace => \$paths) {\n"
						. "            if (0 === strpos(\$namespace, \"ResendWP\\\\\") || 0 === strpos(\$namespace, \"Composer\\\\\")) {\n"
						. "                continue;\n"
						. "            }\n"
						. "            \$loader->setPsr4(\"ResendWP\\\\\" . \$namespace, \$paths);\n"
						. "        }";
				},
				$contents,
				1
			);

			return $contents;
		},
	],
	'exclude-namespaces'         => [
		'CloudCatch',
		'CloudCatch\\Resend',
	],
	'exclude-classes'            => [
		'Composer\InstalledVersions',
	],
];
