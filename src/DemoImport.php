<?php
/**
 * Set One Click Demo Import plugin settings.
 *
 * @package   SeoThemes\Core
 * @author    Lee Anthony <seothemeswp@gmail.com>
 * @author    Craig Simpson <craig@craigsimpson.scot>
 * @copyright 2018, D2 Themes
 * @license   GPL-3.0-or-later
 */

namespace SeoThemes\Core;

/**
 * Set One Click Demo Import plugin settings through configuration.
 *
 * Example config (usually located at config/defaults.php):
 *
 * ```
 * use SeoThemes\Core\DemoImport;
 *
 * $core_demo_import = [
 *     DemoImport::IMPORT_SETTINGS      => [
 *         DemoImport::LOCAL_IMPORT_FILE            => get_stylesheet_directory() .
 *         '/resources/demo/sample.xml', DemoImport::LOCAL_IMPORT_WIDGET_FILE     =>
 *         get_stylesheet_directory() . '/resources/demo/widgets.wie',
 *         DemoImport::LOCAL_IMPORT_CUSTOMIZER_FILE => get_stylesheet_directory() .
 *         '/resources/demo/customizer.dat', DemoImport::IMPORT_FILE_NAME             =>
 *         'Demo Import', DemoImport::CATEGORIES                   => false,
 *         DemoImport::LOCAL_IMPORT_REDUX           => false,
 *         DemoImport::IMPORT_PREVIEW_IMAGE_URL     => false, DemoImport::IMPORT_NOTICE
 *                      => false,
 *     ],
 *     DemoImport::PAGE_SETTINGS => [
 *         DemoImport::SHOW_ON_FRONT            => 'page',
 *         DemoImport::PAGE_ON_FRONT            => 'Home',
 *         DemoImport::PAGE_FOR_POSTS           => 'Blog',
 *         DemoImport::WOOCOMMERCE_SHOP_PAGE_ID => 'Shop',
 *     ],
 *     DemoImport::MENU_SETTINGS => [
 *         [
 *             DemoImport::MENU_NAME     => 'Header Menu',
 *             DemoImport::MENU_LOCATION => 'primary',
 *         ],
 *     ],
 * ];
 *
 * return [
 *     DemoImport::class => $core_demo_import,
 * ];
 * ```
 *
 * @package SeoThemes\Core
 */
class DemoImport extends Component {

	const IMPORT_SETTINGS = 'import_settings';
	const PAGE_SETTINGS = 'page_settings';
	const MENU_SETTINGS = 'menu_settings';
	const SHOW_ON_FRONT = 'show_on_front';
	const PAGE_ON_FRONT = 'page_on_front';
	const PAGE_FOR_POSTS = 'page_for_posts';
	const WOOCOMMERCE_SHOP_PAGE_ID = 'woocommerce_shop_page_id';
	const MENU_NAME = 'menu_name';
	const MENU_LOCATION = 'menu_location';
	const LOCAL_IMPORT_FILE = 'local_import_file';
	const LOCAL_IMPORT_WIDGET_FILE = 'local_import_widget_file';
	const LOCAL_IMPORT_CUSTOMIZER_FILE = 'local_import_customizer_file';
	const IMPORT_FILE_NAME = 'import_file_name';
	const CATEGORIES = 'categories';
	const LOCAL_IMPORT_REDUX = 'local_import_redux';
	const IMPORT_PREVIEW_IMAGE_URL = 'import_preview_image_url';
	const IMPORT_NOTICE = 'import_notice';

	/**
	 * Initialize class.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function init() {
		if ( array_key_exists( self::IMPORT_SETTINGS, $this->config ) ) {
			add_filter( 'pt-ocdi/disable_pt_branding', '__return_true' );
			add_filter( 'pt-ocdi/import_files', [ $this, 'import_settings' ] );
		}

		if ( array_key_exists( self::PAGE_SETTINGS, $this->config ) ) {
			add_filter( 'pt-ocdi/after_all_import_execution', [ $this, 'set_pages' ] );
			add_filter( 'pt-ocdi/after_all_import_execution', 'flush_rewrite_rules' );
		}

		if ( array_key_exists( self::MENU_SETTINGS, $this->config ) ) {
			add_filter( 'pt-ocdi/after_all_import_execution', [ $this, 'set_menus' ] );
		}
	}

	/**
	 * Returns one click demo import settings.
	 *
	 * @since 0.1.0
	 *
	 * @return array
	 */
	public function import_settings() {
		return [ $this->config[ self::IMPORT_SETTINGS ] ];
	}

	/**
	 * Update page settings upon demo import.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function set_pages() {
		$config = $this->config[ self::PAGE_SETTINGS ];

		foreach ( $config as $key => $value ) {

			if ( self::SHOW_ON_FRONT === $key ) {
				update_option( $key, $value );
			} else {
				$title = get_page_by_title( $value );

				if ( $title ) {
					update_option( $key, $title->ID );
				}
			}
		}
	}

	/**
	 * Update menu settings upon demo import.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function set_menus() {
		$config = $this->config[ self::MENU_SETTINGS ];

		foreach ( $config as $menu => $settings ) {
			$menu = get_term_by( 'name', $settings[ self::MENU_NAME ], 'nav_menu' );

			if ( $menu ) {
				set_theme_mod( 'nav_menu_locations', [
					$settings[ self::MENU_LOCATION ] => $menu->term_id,
				] );
			}
		}
	}
}
