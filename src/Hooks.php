<?php
/**
 * Add or remove action and filter hooks through configuration.
 *
 * @package   SeoThemes\Core
 * @author    Lee Anthony <seothemeswp@gmail.com>
 * @author    Lee Anthony <seothemeswp@gmail.com>
 * @copyright 2018, D2 Themes
 * @license   GPL-3.0-or-later
 */

namespace SeoThemes\Core;

/**
 * Add or remove action and filter hooks through configuration.
 *
 * Example config (usually located at config/defaults.php):
 *
 * ```
 * $d2_hooks = [
 *     Hooks::ADD => [
 *         [
 *             Hooks::TAG         => 'genesis_site_title',
 *             Hooks::CALLBACK    => 'the_custom_logo',
 *             Hooks::PRIORITY    => 0,
 *             Hooks::ARGS        => 1,
 *             Hooks::CONDITIONAL => function() {
 *                 return has_custom_logo();
 *             },
 *         ],
 *     ],
 *     Hooks::REMOVE => [
 *         [
 *             Hooks::TAG      => 'genesis_after_header',
 *             Hooks::CALLBACK => 'genesis_do_nav',
 *             Hooks::PRIORITY => 10,
 *         ],
 *     ],
 * ];
 * ```
 *
 * @package SeoThemes\Core
 */
class Hooks extends Component {

	const ADD = 'add';
	const REMOVE = 'remove';

	const TAG = 'tag';
	const CALLBACK = 'callback';
	const PRIORITY = 'priority';
	const ARGS = 'args';
	const CONDITIONAL = 'conditional';

	/**
	 * Hook defaults.
	 *
	 * @var array
	 */
	protected $defaults = [];

	/**
	 * Apply hooks if config is found.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function init() {
		if ( array_key_exists( self::ADD, $this->config ) || array_key_exists( self::REMOVE, $this->config ) ) {
			$this->defaults = [
				self::TAG         => false,
				self::CALLBACK    => false,
				self::PRIORITY    => 10,
				self::ARGS        => 1,
				self::CONDITIONAL => function () {
					return true;
				},
			];

			/**
			 * Attach everything to a later hook.
			 *
			 * Running all of the code inside `genesis_setup` means that some conditionals
			 * can't be parsed, because it's too early in the load order. The `wp` hook
			 * is used here instead, which allows us to attach things to later hooks.
			 *
			 * @since 0.1.0
			 *
			 * @return void
			 */
			add_action( 'wp', [ $this, 'apply_hooks' ] );
		}
	}

	/**
	 * Adds or removes action and filter hooks.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function apply_hooks() {
		foreach ( $this->config as $add_or_remove => $sub_configs ) {
			foreach ( $sub_configs as $sub_config => $hook ) {
				$function    = $add_or_remove . '_filter';
				$tag         = $this->get_value( self::TAG, $hook );
				$callback    = $this->get_value( self::CALLBACK, $hook );
				$priority    = $this->get_value( self::PRIORITY, $hook );
				$args        = $this->get_value( self::ARGS, $hook );
				$conditional = $this->get_value( self::CONDITIONAL, $hook );

				if ( is_callable( $conditional ) && $conditional() ) {
					$function( $tag, $callback, $priority, $args );
				}
			}
		}

	}

	/**
	 * Returns the default value for an array key.
	 *
	 * @since 0.1.0
	 *
	 * @param string $key   Key to check.
	 * @param array  $array Array to search.
	 *
	 * @return mixed
	 */
	protected function get_value( $key, $array ) {
		return array_key_exists( $key, $array ) ? $array[ $key ] : $this->defaults[ $key ];
	}
}
