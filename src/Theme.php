<?php
/**
 * Theme class.
 *
 * Loads other core-compatible components from within
 * our WordPress theme functions.php file.
 *
 * @package   SeoThemes\Core
 * @author    Lee Anthony <seothemeswp@gmail.com>
 * @author    Craig Simpson <craig@craigsimpson.scot>
 * @copyright 2018, D2 Themes
 * @license   GPL-3.0-or-later
 */

namespace SeoThemes\Core;

/**
 * Class Theme
 *
 * Used to load other core-compatible components.
 *
 * @package SeoThemes\Core
 */
final class Theme {

	/**
	 * The setup function will iterate through the theme configuration array,
	 * check for the existence of a customization-specific class (the array
	 * key), then instantiate the class and call the init() method.
	 *
	 * Use it:
	 * ```
	 * add_action( 'after_setup_theme', function() {
	 *     $config = include_once __DIR__ . '/config/defaults.php';
	 *     SeoThemes\Core\Theme::setup( $config );
	 * } );
	 * ```
	 *
	 * @param array $config All theme-specific configuration.
	 *
	 * @return void
	 *
	 * @since 0.0.1
	 */
	public static function setup( array $config ) {
		foreach ( $config as $class_name => $class_specific_config ) {
			if ( class_exists( $class_name ) ) {
				$class = new $class_name( $class_specific_config );
				$class->init();
			}
		}
	}
}
