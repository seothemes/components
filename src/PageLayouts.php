<?php
/**
 * Register and unregister Genesis layouts through configuration.
 *
 * @package   SeoThemes\Core
 * @author    Lee Anthony <seothemeswp@gmail.com>
 * @copyright 2018, Lee Anthony
 * @license   GPL-3.0-or-later
 */

namespace SeoThemes\Core;

/**
 * Register and unregister Genesis layouts through configuration.
 *
 * Example config (usually located at config/defaults.php):
 *
 * ```
 * use SeoThemes\Core\GenesisLayout;
 *
 * $d2_layouts = [
 *     GenesisLayout::REGISTER   => [
 *         'slim-content', [
 *             'label' => 'Slim Content Area',
 *             'image' => get_stylesheet_directory_uri() . '/images/slim-content-icon.png',
 *         ],
 *     ],
 *     GenesisLayout::UNREGISTER => [
 *         GenesisLayout::SIDEBAR_CONTENT,
 *         GenesisLayout::CONTENT_SIDEBAR_SIDEBAR,
 *         GenesisLayout::SIDEBAR_CONTENT_SIDEBAR,
 *         GenesisLayout::SIDEBAR_SIDEBAR_CONTENT,
 *     ],
 * ];
 *
 * return [
 *     GenesisLayout::class => $d2_layouts,
 * ];
 * ```
 *
 * @package SeoThemes\Core
 */
class PageLayouts extends Component {

	const REGISTER                = 'register';
	const UNREGISTER              = 'unregister';
	const FULL_WIDTH_CONTENT      = 'full-width-content';
	const CONTENT_SIDEBAR         = 'content-sidebar';
	const SIDEBAR_CONTENT         = 'sidebar-content';
	const CONTENT_SIDEBAR_SIDEBAR = 'content-sidebar-sidebar';
	const SIDEBAR_CONTENT_SIDEBAR = 'sidebar-content-sidebar';
	const SIDEBAR_SIDEBAR_CONTENT = 'sidebar-sidebar-content';

	/**
	 * Register and unregister Genesis layouts through configuration.
	 *
	 * @return void
	 */
	public function init() {
		if ( array_key_exists( self::REGISTER, $this->config ) ) {
			array_map( 'genesis_register_layout', array_keys( $this->config[ self::REGISTER ] ), $this->config[ self::REGISTER ] );
		}

		if ( array_key_exists( self::UNREGISTER, $this->config ) ) {
			array_map( 'genesis_unregister_layout', $this->config[ self::UNREGISTER ] );
		}
	}
}
