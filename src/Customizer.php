<?php
/**
 * Add Customizer panels, sections and fields.
 *
 * @package   SeoThemes\Core
 * @author    Lee Anthony <seothemeswp@gmail.com>
 * @author    Craig Simpson <craig@craigsimpson.scot>
 * @copyright 2018, D2 Themes
 * @license   GPL-3.0-or-later
 */

namespace SeoThemes\Core;

/**
 * Add Customizer panels, sections and fields through configuration.
 *
 * Note: Fields are a combination of Customizer settings and controls.
 *
 * Example config (usually located at config/defaults.php):
 *
 * ```
 * use SeoThemes\Core\PostTypeSupport
 *
 *
 * $core_customizer = [
 *     Customizer::PANELS   => [
 *         [
 *             Customizer::ID          => 'single_posts_panel',
 *             Customizer::TITLE       => 'Single Posts Panel',
 *             Customizer::DESCRIPTION => 'Single posts panel description.',
 *             Customizer::PRIORITY    => 1,
 *         ],
 *     ],
 *     Customizer::SECTIONS => [
 *         [
 *             Customizer::ID          => 'single_posts',
 *             Customizer::TITLE       => 'Single Posts Section',
 *             Customizer::DESCRIPTION => 'Single posts section description.',
 *             Customizer::PANEL       => 'single_posts_panel',
 *         ],
 *     ],
 *     Customizer::FIELDS   => [
 *         [
 *             Customizer::CONTROL_TYPE  => 'checkbox',
 *             Customizer::SETTINGS      => 'single_post_featured_image',
 *             Customizer::LABEL         => 'Display featured image',
 *             Customizer::SECTION       => 'single_posts',
 *             Customizer::DEFAULT_VALUE => true,
 *         ],
 *     ],
 * ];
 *
 * return [
 *     Customizer::class => $core_customizer,
 * ];
 * ```
 *
 * @package SeoThemes\Core
 */
class Customizer extends Component {

	const FIELDS               = 'fields';
	const PANELS               = 'panels';
	const PANEL                = 'panel';
	const ID                   = 'id';
	const SECTIONS             = 'sections';
	const TITLE                = 'title';
	const CONTROL              = 'control';
	const SETTING_TYPE         = 'type';
	const CAPABILITY           = 'capability';
	const THEME_SUPPORTS       = 'theme_supports';
	const DEFAULT_VALUE        = 'default';
	const TRANSPORT            = 'transport';
	const VALIDATE_CALLBACK    = 'validate_callback';
	const SANITIZE_CALLBACK    = 'sanitize_callback';
	const SANITIZE_JS_CALLBACK = 'sanitize_js_callback';
	const DIRTY                = 'dirty';
	const SETTINGS             = 'settings';
	const SETTING              = 'setting';
	const PRIORITY             = 'priority';
	const SECTION              = 'section';
	const LABEL                = 'label';
	const DESCRIPTION          = 'description';
	const CHOICES              = 'choices';
	const INPUT_ATTRS          = 'input_attrs';
	const ALLOW_ADDITION       = 'allow_addition';
	const CONTROL_TYPE         = 'type';
	const ACTIVE_CALLBACK      = 'active_callback';

	/**
	 * @var
	 */
	protected $properties;

	/**
	 * Initialize component.
	 *
	 * @since 0.2.0
	 *
	 * @return void
	 */
	public function init() {
		$this->properties = [
			self::SETTING => [
				self::THEME_SUPPORTS,
				self::TRANSPORT,
				self::VALIDATE_CALLBACK,
				self::SANITIZE_CALLBACK,
				self::SANITIZE_JS_CALLBACK,
				self::DIRTY,
			],
			self::CONTROL => [
				self::SETTINGS,
				self::SETTING,
				self::PRIORITY,
				self::SECTION,
				self::LABEL,
				self::DESCRIPTION,
				self::CHOICES,
				self::INPUT_ATTRS,
				self::ALLOW_ADDITION,
				self::CONTROL_TYPE,
				self::ACTIVE_CALLBACK,
			],
		];

		if ( array_key_exists( self::FIELDS, $this->config ) ) {
			$this->fields( $this->config[ self::FIELDS ] );
		}

		if ( array_key_exists( self::SECTIONS, $this->config ) ) {
			$this->sections( $this->config[ self::SECTIONS ] );
		}

		if ( array_key_exists( self::PANELS, $this->config ) ) {
			$this->panels( $this->config[ self::PANELS ] );
		}
	}

	/**
	 * Adds fields (settings & controls).
	 *
	 * @since 0.2.0
	 *
	 * @param array $config
	 *
	 * @return void
	 */
	protected function fields( $config ) {
		add_action( 'customize_register', function () use ( $config ) {
			global $wp_customize;

			foreach ( $config as $sub_config => $args ) {
				$wp_customize->add_setting( $args[ self::SETTINGS ], $this->filter( $args, self::SETTING ) );
				$wp_customize->add_control( $args[ self::SETTINGS ], $this->filter( $args, self::CONTROL ) );
			}
		} );
	}

	/**
	 * Adds sections.
	 *
	 * @since 0.2.0
	 *
	 * @param $config
	 *
	 * @return void
	 */
	protected function sections( $config ) {
		add_action( 'customize_register', function () use ( $config ) {
			global $wp_customize;

			foreach ( $config as $sub_config => $args ) {
				$wp_customize->add_section( $args[ self::ID ], $args );
			}
		} );
	}

	/**
	 * Adds panels.
	 *
	 * @since 0.2.0
	 *
	 * @param $config
	 *
	 * @return void
	 */
	protected function panels( $config ) {
		add_action( 'customize_register', function () use ( $config ) {
			global $wp_customize;

			foreach ( $config as $sub_config => $args ) {
				$wp_customize->add_panel( $args[ self::ID ], $args );
			}
		} );
	}

	/**
	 * Separates unwanted array keys from configs.
	 *
	 * @since 0.2.0
	 *
	 * @param $args
	 * @param $type
	 *
	 * @return array
	 */
	protected function filter( $args, $type ) {
		if ( self::SETTING === $type ) {
			foreach ( $this->properties[ self::CONTROL ] as $property ) {
				unset( $args[ $property ] );
			}
		} elseif ( self::CONTROL === $type ) {
			foreach ( $this->properties[ self::SETTING ] as $property ) {
				unset( $args[ $property ] );
			}
		}

		return $args;
	}
}
