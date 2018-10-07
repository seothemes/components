<?php
/**
 * Load theme scripts and stylesheets through configuration.
 *
 * @package   SeoThemes\Core
 * @author    Lee Anthony <seothemeswp@gmail.com>
 * @author    Craig Simpson <craig@craigsimpson.scot>
 * @copyright 2018, D2 Themes
 * @license   GPL-3.0-or-later
 */

namespace SeoThemes\Core;

/**
 * Load theme scripts and stylesheets through configuration.
 *
 * Example config (usually located at config/defaults.php):
 *
 * ```
 * use SeoThemes\Core\AssetLoader;
 *
 * $core_assets = [
 *      AssetLoader::SCRIPTS => [
 *         [
 *            AssetLoader::HANDLE   => 'text-domain',
 *            AssetLoader::URL      => AssetLoader::path( '/assets/js/menus.js' ),
 *            AssetLoader::DEPS     => [ 'jquery' ],
 *            AssetLoader::VERSION  => CHILD_THEME_VERSION,
 *            AssetLoader::FOOTER   => true,
 *            AssetLoader::ENQUEUE  => true,
 *            AssetLoader::LOCALIZE => [
 *                AssetLoader::LOCALIZEVAR  => 'genesis_responsive_menu',
 *                AssetLoader::LOCALIZEDATA => [
 *                    'mainMenu'    => __( 'Toggle Menu', 'text-domain' ),
 *                    'subMenu'     => __( 'Toggle Submenu', 'text-domain' ),
 *                    'menuClasses' => [
 *                        'combine' => [
 *                            '.nav-primary',
 *                        ],
 *                        'others'  => [],
 *                    ],
 *                ]
 *            ],
 *         ],
 *      ],
 *      AssetLoader::STYLES => [
 *         [
 *            AssetLoader::HANDLE   => 'fontawesome',
 *            AssetLoader::URL      =>
 *            'https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css',
 *            AssetLoader::VERSION  => '4.7.0',
 *       ],
 *    ],
 * ];
 *
 * return [
 *     AssetLoader::class => $core_assets,
 * ];
 * ```
 *
 * @package SeoThemes\Core
 */
class AssetLoader extends Component {

	const CONDITIONAL  = 'conditional';
	const DEPS         = 'deps';
	const ENQUEUE      = 'enqueue';
	const FOOTER       = 'footer';
	const HANDLE       = 'handle';
	const LOCALIZE     = 'localize';
	const LOCALIZEDATA = 'l10ndata';
	const LOCALIZEVAR  = 'l10var';
	const MEDIA        = 'media';
	const SCRIPTS      = 'scripts';
	const STYLES       = 'styles';
	const URL          = 'src';
	const VERSION      = 'version';

	public function init() {
		if ( array_key_exists( self::SCRIPTS, $this->config ) ) {
			add_action( 'wp_enqueue_scripts', [ $this, 'process_scripts' ] );
		}

		if ( array_key_exists( self::STYLES, $this->config ) ) {
			add_action( 'wp_enqueue_scripts', [ $this, 'process_styles' ] );
		}
	}

	/**
	 * Enqueue or register scripts passed through config, and implement l10n if required.
	 *
	 * @return void
	 */
	public function process_scripts() {
		foreach ( $this->config[ self::SCRIPTS ] as $asset ) {
			$deps        = $this->get_deps( $asset );
			$version     = $this->get_version( $asset );
			$footer      = $this->get_footer( $asset );
			$conditional = array_key_exists( self::CONDITIONAL, $asset ) ? $asset[ self::CONDITIONAL ] : '__return_true';
			$function    = true === $asset[ self::ENQUEUE ] ? 'wp_enqueue_script' : 'wp_register_script';

			// Either enqueue or register the script.
			if ( is_callable( $conditional ) && $conditional() ) {
				$function( $asset[ self::HANDLE ], $asset[ self::URL ], $deps, $version, $footer );

				if ( array_key_exists( self::LOCALIZE, $asset ) ) {
					$name = $asset[ self::LOCALIZE ][ self::LOCALIZEVAR ];
					$data = $asset[ self::LOCALIZE ][ self::LOCALIZEDATA ];
					wp_localize_script( $asset[ self::HANDLE ], $name, $data );
				}
			}
		}
	}

	/**
	 * Enqueue or register stylesheets passed through config.
	 *
	 * @return void
	 */
	public function process_styles() {
		foreach ( $this->config[ self::STYLES ] as $asset ) {
			$deps        = $this->get_deps( $asset );
			$version     = $this->get_version( $asset );
			$media       = $this->get_media( $asset );
			$conditional = array_key_exists( self::CONDITIONAL, $asset ) ? $asset[ self::CONDITIONAL ] : '__return_true';
			$function    = true === $asset[ self::ENQUEUE ] ? 'wp_enqueue_style' : 'wp_register_style';

			// Either enqueue or register the stylesheet.
			if ( is_callable( $conditional ) && $conditional() ) {
				$function( $asset[ self::HANDLE ], $asset[ self::URL ], $deps, $version, $media );
			}
		}
	}

	/**
	 * Get asset dependencies, or fall back to empty array.
	 *
	 * @param array $asset
	 *
	 * @return array
	 */
	protected function get_deps( array $asset ) {
		return isset( $asset[ self::DEPS ] ) ? $asset[ self::DEPS ] : [];
	}

	/**
	 * Get asset version, or fall back to false.
	 *
	 * @param array $asset
	 *
	 * @return string|bool
	 */
	protected function get_version( array $asset ) {
		return isset( $asset[ self::VERSION ] ) ? $asset[ self::VERSION ] : false;
	}

	/**
	 * Determine if asset should be loaded in the footer.
	 *
	 * @param array $asset
	 *
	 * @return bool
	 */
	protected function get_footer( array $asset ) {
		return isset( $asset[ self::FOOTER ] ) ? $asset[ self::FOOTER ] : false;
	}

	/**
	 * Determine media type, or fall back to 'all'.
	 *
	 * @param array $asset
	 *
	 * @return string
	 */
	protected function get_media( array $asset ) {
		return isset( $asset[ self::MEDIA ] ) ? $asset[ self::MEDIA ] : 'all';
	}

	/**
	 * Return the path to the file.
	 *
	 * If a minified version of the file exists and SCRIPT_DEBUG
	 * is not enabled then AssetLoader will return the URL of the
	 * minified file.
	 *
	 * @param string $path Path to the file relative to theme root.
	 *
	 * @return string
	 */
	public static function path( $path ) {
		if ( ! strpos( $path, '.min.' ) ) {
			$filename           = pathinfo( $path, PATHINFO_FILENAME );
			$extension          = pathinfo( $path, PATHINFO_EXTENSION );
			$directory          = pathinfo( $path, PATHINFO_DIRNAME );
			$minified_file      = trailingslashit( $directory ) . $filename . '.min.' . $extension;
			$minified_file_path = get_stylesheet_directory() . $minified_file;

			if ( file_exists( $minified_file_path ) && ! ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ) {
				$path = $minified_file;
			}
		}

		return esc_url( get_stylesheet_directory_uri() . $path );
	}
}
