<?php
/**
 * Register, unregister and display widget areas through configuration.
 *
 * @package   SeoThemes\Core
 * @author    Lee Anthony <seothemeswp@gmail.com>
 * @author    Craig Simpson <craig@craigsimpson.scot>
 * @copyright 2018, D2 Themes
 * @license   GPL-3.0-or-later
 */

namespace SeoThemes\Core;

/**
 * Register, unregister or display widget areas.
 *
 * Example config (usually located at config/defaults.php):
 *
 * ```
 * use SeoThemes\Core\WidgetArea;
 *
 * $core_widget_areas = [
 *     WidgetArea::REGISTER   => [
 *         [
 *             WidgetArea::ID          => 'utility-bar',
 *             WidgetArea::NAME        => __( 'Utility Bar', 'example-textdomain' ),
 *             WidgetArea::DESCRIPTION => __( 'Utility bar appearing above the site header.', 'example-textdomain' ),
 *             WidgetArea::LOCATION    => 'genesis_before_header',
 *             WidgetArea::BEFORE      => '<div class="utility-bar">',
 *             WidgetArea::AFTER       => '</div>',
 *             WidgetArea::PRIORITY    => 5,
 *             WidgetArea::CONDITIONAL => function () {
 *                 return is_front_page();
 *             },
 *         ],
 *     ],
 *     WidgetArea::UNREGISTER => [
 *         WidgetArea::HEADER_RIGHT,
 *         WidgetArea::SIDEBAR_ALT,
 *     ],
 * ];
 *
 * return [
 *     WidgetArea::class => $core_widget_areas,
 * ];
 * ```
 *
 * @package SeoThemes\Core
 */
class WidgetArea extends Component {

	const REGISTER = 'register';
	const UNREGISTER = 'unregister';
	const ID = 'id';
	const NAME = 'name';
	const DESCRIPTION = 'description';
	const LOCATION = 'location';
	const BEFORE = 'before';
	const AFTER = 'after';
	const BEFORE_TITLE = 'before_title';
	const AFTER_TITLE = 'after_title';
	const PRIORITY = 'priority';
	const CONDITIONAL = 'conditional';
	const HEADER_RIGHT = 'header-right';
	const SIDEBAR = 'sidebar';
	const SIDEBAR_ALT = 'sidebar-alt';

	/**
	 * Register, unregister or display widget areas through configuration.
	 *
	 * @since 0.2.0
	 *
	 * @return void
	 */
	public function init() {
		if ( array_key_exists( self::REGISTER, $this->config ) ) {
			$this->register( $this->config[ self::REGISTER ] );
			$this->display( $this->config[ self::REGISTER ] );
		}

		if ( array_key_exists( self::UNREGISTER, $this->config ) ) {
			$this->unregister( $this->config[ self::UNREGISTER ] );
		}
	}

	/**
	 * Register widget areas.
	 *
	 * @since 0.2.0
	 *
	 * @link  https://codex.wordpress.org/Function_Reference/register_sidebar
	 * @link  genesis/lib/functions/widgetize.php.
	 *
	 * @param array $config Register config.
	 *
	 * @return array
	 */
	protected function register( $config ) {
		$register_function = $this->is_genesis() ? 'genesis_register_widget_area' : 'register_sidebar';

		return array_map( $register_function, $config );
	}

	/**
	 * Unregister widget areas.
	 *
	 * @since 0.2.0
	 *
	 * @param array $config Unregister config.
	 *
	 * @return array
	 */
	protected function unregister( $config ) {
		return array_map( 'unregister_sidebar', $config );
	}

	/**
	 * Displays widget areas.
	 *
	 * @since 0.3.0
	 *
	 * @param array $config Register config.
	 *
	 * @return void
	 */
	protected function display( $config ) {
		foreach ( $config as $widget_area => $args ) {
			if ( ! array_key_exists( self::LOCATION, $args ) ) {
				return;
			}

			add_action( $args[ self::LOCATION ], function () use ( $args ) {
				$function    = $this->is_genesis() ? 'genesis_widget_area' : 'dynamic_sidebar';
				$before      = array_key_exists( self::BEFORE, $args ) ? $args[ self::BEFORE ] : '<div class="' . $args[ self::ID ] . ' widget-area"><div class="wrap">';
				$after       = array_key_exists( self::AFTER, $args ) ? $args[ self::AFTER ] : '</div></div>';
				$conditional = array_key_exists( self::CONDITIONAL, $args ) ? $args[ self::CONDITIONAL ] : '__return_true';

				if ( is_callable( $conditional ) && $conditional() ) {
					$function( $args[ self::ID ], [
						self::BEFORE => is_callable( $before ) ? $before() : $before,
						self::AFTER  => is_callable( $after ) ? $after() : $after,
					] );
				}
			}, array_key_exists( self::PRIORITY, $args ) ? $args[ self::PRIORITY ] : 10 );
		}
	}

	/**
	 * Check for Genesis child theme.
	 *
	 * @since 0.3.0
	 *
	 * @return bool
	 */
	protected function is_genesis() {
		return 'genesis' === wp_get_theme()->get( 'Template' ) ? true : false;
	}
}
