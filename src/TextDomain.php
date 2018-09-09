<?php
/**
 * Load the theme’s translated strings.
 *
 * @package   SeoThemes\Core
 * @author    Lee Anthony <seothemeswp@gmail.com>
 * @author    Craig Simpson <craig@craigsimpson.scot>
 * @copyright 2018, D2 Themes
 * @license   GPL-3.0-or-later
 */

namespace SeoThemes\Core;

/**
 * Load the theme’s translated strings.
 *
 * Example config (usually located at config/defaults.php):
 *
 * The TextDomain::PATH should only be set if it's different from the
 * default path of `get_stylesheet_directory() . '/languages'`.
 *
 * ```
 * use SeoThemes\Core\TextDomain;
 *
 * $core_textdomain = [
 *     TextDomain::DOMAIN => 'example-textdomain',
 *     TextDomain::PATH   => get_stylesheet_directory() . '/assets/langauges',
 * ];
 *
 * return [
 *     TextDomain::class => $core_textdomain,
 * ];
 * ```
 *
 * @package SeoThemes\Core
 */
class TextDomain extends Component {

	const DOMAIN = 'domain';
	const PATH   = 'path';

	/**
	 * Load the theme’s translated strings.
	 *
	 * @return void
	 */
	public function init() {
		if ( array_key_exists( self::DOMAIN, $this->config ) ) {
			$load_function = $this->get_load_function();
			$path          = $this->get_path();
			$load_function( $this->config[ self::DOMAIN ], $path );
		}
	}

	/**
	 * Return the appropriate load function.
	 *
	 * If this component is being used in a child theme, then we should
	 * use load_child_theme_textdomain(), or if it's a standalone theme
	 * then it should be load_theme_textdomain().
	 *
	 * @link https://developer.wordpress.org/reference/functions/load_theme_textdomain/
	 * @link https://developer.wordpress.org/reference/functions/load_child_theme_textdomain/
	 *
	 * @return string
	 */
	protected function get_load_function() {
		return is_child_theme() ? 'load_child_theme_textdomain' : 'load_theme_textdomain';
	}

	/**
	 * Return the path to language files.
	 *
	 * The default path for language files is /languages. If a different
	 * path has been defined in config then we'll use that, otherwise fall
	 * back to the default.
	 *
	 * @return string
	 */
	protected function get_path() {
		return isset( $this->config[ self::PATH ] ) ?
			$this->config[ self::PATH ] : get_stylesheet_directory() . '/languages';
	}
}
