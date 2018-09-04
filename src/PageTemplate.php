<?php
/**
 * Register and unregister page templates through configuration.
 *
 * @package   SeoThemes\Core
 * @author    Lee Anthony <seothemeswp@gmail.com>
 * @copyright 2018, Lee Anthony
 * @license   GPL-3.0-or-later
 */

namespace SeoThemes\Core;

/**
 * Register and unregister page templates through configuration.
 *
 * Example config (usually located at config/defaults.php):
 *
 * ```
 * use SeoThemes\Core\PageTemplate;
 *
 * $d2_page_templates = [
 *     PageTemplate::REGISTER   => [
 *         '/resources/views/example.php' => 'Example Template',
 *     ],
 *     PageTemplate::UNREGISTER => [
 *         PageTemplate::ARCHIVE,
 *         PageTemplate::BLOG,
 *     ],
 * ];
 *
 * return [
 *     PageTemplate::class => $d2_page_templates,
 * ];
 * ```
 *
 * @package SeoThemes\Core
 */
class PageTemplate extends Component {

	const REGISTER = 'register';
	const UNREGISTER = 'unregister';
	const ARCHIVE = 'page_archive.php';
	const BLOG = 'page_blog.php';

	/**
	 * Add filter to register and unregister page templates.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function init() {
		if ( array_key_exists( self::REGISTER, $this->config ) ) {
			add_filter( 'theme_page_templates', [ $this, 'add_templates' ] );
		}
		if ( array_key_exists( self::UNREGISTER, $this->config ) ) {
			add_filter( 'theme_page_templates', [ $this, 'remove_templates' ] );
		}
	}

	/**
	 * Description of expected behavior.
	 *
	 * @since 0.1.0
	 *
	 * @param array $templates Templates to register.
	 *
	 * @return array
	 */
	public function add_templates( $templates ) {
		return array_merge( $templates, $this->config[ self::REGISTER ] );
	}

	/**
	 * Unregister page templates through configuration.
	 *
	 * @since 0.1.0
	 *
	 * @param array $templates Registered page templates.
	 *
	 * @return array
	 */
	public function remove_templates( $templates ) {
		return array_diff_key( $templates, array_flip( $this->config[ self::UNREGISTER ] ) );
	}
}
