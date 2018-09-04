<?php
/**
 * Define child theme constants through configuration.
 *
 * @package   SeoThemes\Core
 * @author    Lee Anthony <seothemeswp@gmail.com>
 * @copyright 2018, SEOThemes
 * @license   GPL-3.0-or-later
 */

namespace SeoThemes\Core;

/**
 * Define child theme constants through configuration.
 *
 * Example config (usually located at config/defaults.php):
 *
 * ```
 * use SeoThemes\Core\Constants;
 *
 * $d2_constants = [
 *     Constants::DEFINE => [
 *         'CHILD_THEME_NAME'    => wp_get_theme()->get( 'Name' ),
 *         'CHILD_THEME_URL'     => wp_get_theme()->get( 'ThemeURI' ),
 *         'CHILD_THEME_VERSION' => wp_get_theme()->get( 'Version' ),
 *     ],
 * ];
 *
 * return [
 *     Constants::class => $d2_constants,
 * ];
 * ```
 *
 * @package SeoThemes\Core
 */
class Constants extends Component {

	const DEFINE = 'define';

	/**
	 * Define child theme constants through configuration.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function init() {
		if ( array_key_exists( self::DEFINE, $this->config ) ) {
			$this->define_constants( $this->config[ self::DEFINE ] );
		}
	}

	/**
	 * Define child theme constants.
	 *
	 * @since  0.1.0
	 *
	 * @param  array $constants Array of constants to define.
	 *
	 * @return void
	 */
	protected function define_constants( array $constants ) {
		foreach ( $constants as $name => $value ) {
			if ( ! defined( $name ) ) {
				define( $name, $value );
			}
		}
	}
}
