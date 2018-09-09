<?php
/**
 * Register and unregister widgets through configuration.
 *
 * @package   SeoThemes\Core
 * @author    Lee Anthony <seothemeswp@gmail.com>
 * @author    Craig Simpson <craig@craigsimpson.scot>
 * @copyright 2018, D2 Themes
 * @license   GPL-3.0-or-later
 */

namespace SeoThemes\Core;

/**
 * Register and unregister widgets classes.
 *
 * Example config (usually located at config/defaults.php):
 *
 * ```
 * use SeoThemes\Core\Widgets;
 *
 * $core_widgets = [
 *     Widgets::UNREGISTER => [
 *         \WP_Widget_Search::class,
 *     ],
 * ];
 *
 * return [
 *     Widgets::class => $core_widgets,
 * ];
 * ```
 *
 * @link    https://gist.github.com/seothemes/42bc4fe80b9c03a6450d1f28d8663631
 * @package SeoThemes\Core
 */
class Widgets extends Component {

	const REGISTER = 'register';
	const UNREGISTER = 'unregister';

	/**
	 * Initialize class.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function init() {
		if ( array_key_exists( self::UNREGISTER, $this->config ) ) {
			add_action( 'widgets_init', [ $this, 'unregister' ], 15 );
		}
		if ( array_key_exists( self::REGISTER, $this->config ) ) {
			add_action( 'widgets_init', [ $this, 'register' ], 15 );
		}
	}

	/**
	 * Register widgets.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function register() {
		array_map( 'register_widget', $this->config[ self::REGISTER ] );
	}

	/**
	 * Unregister widgets.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function unregister() {
		array_map( 'unregister_widget', $this->config[ self::UNREGISTER ] );
	}
}
