<?php
/**
 * Add or remove theme support for given features.
 *
 * @package   SeoThemes\Core
 * @author    Lee Anthony <seothemeswp@gmail.com>
 * @copyright 2018, Lee Anthony
 * @license   GPL-3.0-or-later
 */

namespace SeoThemes\Core;

/**
 * Add or remove theme support for given features.
 *
 * Example config (usually located at config/defaults.php):
 *
 * ```
 * use SeoThemes\Core\ThemeSupport;
 *
 * $d2_theme_support = [
 *     ThemeSupport::ADD => [
 *         'html5', [
 *             'comment-form',
 *             'comment-list',
 *             'gallery',
 *             'caption',
 *         ],
 *     ]
 * ];
 *
 * return [
 *     ThemeSupport::class => $d2_theme_support,
 * ];
 * ```
 *
 * @package SeoThemes\Core
 */
class ThemeSupport extends Component {

	const ADD    = 'add';
	const REMOVE = 'remove';

	/**
	 * Add or remove theme support for given features.
	 *
	 * @return void
	 */
	public function init() {
		if ( array_key_exists( self::REMOVE, $this->config ) ) {
			$this->remove( $this->config[ self::REMOVE ] );
		}

		if ( array_key_exists( self::ADD, $this->config ) ) {
			$this->add( $this->config[ self::ADD ] );
		}
	}

	/**
	 * Add theme support for given features.
	 *
	 * @link https://developer.wordpress.org/reference/functions/add_theme_support/
	 *
	 * @param array $features Array of features and optional extra arguments.
	 *
	 * @return void
	 */
	protected function add( array $features ) {
		foreach ( $features as $feature => $args ) {
			\add_theme_support( $feature, $args );
		}
	}

	/**
	 * Remove theme support for given features.
	 *
	 * @link https://developer.wordpress.org/reference/functions/remove_theme_support/
	 *
	 * @param array $features Array of features.
	 *
	 * @return void
	 */
	protected function remove( array $features ) {
		foreach ( $features as $feature ) {
			\remove_theme_support( $feature );
		}
	}
}
