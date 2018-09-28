<?php
/**
 * Add Customizer color settings and CSS output through configuration.
 *
 * @package   SeoThemes\Core
 * @author    Lee Anthony <seothemeswp@gmail.com>
 * @author    Craig Simpson <craig@craigsimpson.scot>
 * @copyright 2018, D2 Themes
 * @license   GPL-3.0-or-later
 */

namespace SeoThemes\Core;

/**
 * Add Customizer color settings and CSS output through configuration.
 *
 * Example config (usually located at config/defaults.php):
 *
 * ```
 * use SeoThemes\Core\CustomColors;
 *
 * $core_custom_colors = [
 *     [
 *         CustomColors::ID            => 'background',
 *         CustomColors::DEFAULT_COLOR => '#ffffff',
 *         CustomColors::OUTPUT        => [
 *             [
 *                 CustomColors::ELEMENTS   => [
 *                     'body',
 *                     '.site-container',
 *                 ],
 *                 CustomColors::PROPERTIES => [
 *                     'background-color' => '%s',
 *                 ],
 *             ],
 *         ],
 *     ],
 * ];
 *
 * return [
 *     CustomColors::class => $core_custom_colors,
 * ];
 * ```
 */
class CustomColors extends Component {

	const ID            = 'id';
	const DEFAULT_COLOR = 'default';
	const OUTPUT        = 'output';
	const ELEMENTS      = 'elements';
	const PROPERTIES    = 'properties';
	const FORMAT        = 'format';

	/**
	 * Attach hooks to add Customizer color settings.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function init() {
		add_action( 'customize_register', [ $this, 'add_settings' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'output_css' ], 100 );
	}

	/**
	 * Sets up the theme customizer sections, controls, and settings.
	 *
	 * @since 0.2.0
	 *
	 * @param object $wp_customize Global customizer object.
	 *
	 * @return void
	 */
	public function add_settings( $wp_customize ) {
		foreach ( $this->config as $color => $settings ) {
			$id      = $settings[ self::ID ];
			$setting = "child_theme_{$id}_color";
			$label   = ucwords( str_replace( '_', ' ', $id ) ) . ' Color';

			$wp_customize->add_setting(
				$setting,
				[
					'default'           => $settings[ self::DEFAULT_COLOR ],
					'sanitize_callback' => 'sanitize_hex_color',
				]
			);

			$wp_customize->add_control(
				new \WP_Customize_Color_Control(
					$wp_customize,
					$setting,
					[
						'section'  => 'colors',
						'label'    => $label,
						'settings' => $setting,
					]
				)
			);
		}
	}

	/**
	 * Logic to output customizer styles.
	 *
	 * @since  0.2.0
	 *
	 * @return void
	 */
	public function output_css() {
		$css = '';

		foreach ( $this->config as $color => $settings ) {
			$id           = $settings[ self::ID ];
			$custom_color = get_theme_mod( "child_theme_{$id}_color", $settings[ self::DEFAULT_COLOR ] );

			if ( $settings[ self::DEFAULT_COLOR ] !== $custom_color ) {
				foreach ( $settings[ self::OUTPUT ] as $rule ) {
					$counter = 0;

					foreach ( $rule[ self::ELEMENTS ] as $element ) {
						$comma = ( 0 === $counter ++ ? '' : ',' );
						$css   .= $comma . $element;
					}

					$css .= '{';

					foreach ( $rule[ self::PROPERTIES ] as $property => $pattern ) {
						$format = strpos( $pattern, 'rgba' ) ? $this->hex_to_rgb( $custom_color ) : $custom_color;
						$css    .= $property . ':' . sprintf( $pattern, $format ) . ';';
					}

					$css .= '}';
				}
			}
		}

		if ( ! empty( $css ) ) {
			$handle = defined( 'CHILD_THEME_NAME' ) && CHILD_THEME_NAME ? sanitize_title_with_dashes( CHILD_THEME_NAME ) : 'child-theme';

			wp_add_inline_style( $handle, $this->minify_css( $css ) );
		}
	}

	/**
	 * Converts hex to rgb.
	 *
	 * @since 1.0.0
	 *
	 * @param $color
	 *
	 * @return string
	 */
	protected function hex_to_rgb( $color ) {
		list( $r, $g, $b ) = [
			$color[1] . $color[2],
			$color[3] . $color[4],
			$color[5] . $color[6],
		];

		$r = hexdec( $r );
		$g = hexdec( $g );
		$b = hexdec( $b );

		return implode( ',', [ $r, $g, $b ] );
	}

	/**
	 * Quick and dirty way to mostly minify CSS.
	 *
	 * @since  0.2.0
	 *
	 * @author Gary Jones
	 *
	 * @param string $css CSS to minify.
	 *
	 * @return string Minified CSS
	 */
	public function minify_css( $css ) {
		$css = preg_replace( '/\s+/', ' ', $css );
		$css = preg_replace( '/(\s+)(\/\*(.*?)\*\/)(\s+)/', '$2', $css );
		$css = preg_replace( '~/\*(?![\!|\*])(.*?)\*/~', '', $css );
		$css = preg_replace( '/;(?=\s*})/', '', $css );
		$css = preg_replace( '/(,|:|;|\{|}|\*\/|>) /', '$1', $css );
		$css = preg_replace( '/ (,|;|\{|}|\(|\)|>)/', '$1', $css );
		$css = preg_replace( '/(:| )0\.([0-9]+)(%|em|ex|px|in|cm|mm|pt|pc)/i', '${1}.${2}${3}', $css );
		$css = preg_replace( '/(:| )(\.?)0(%|em|ex|px|in|cm|mm|pt|pc)/i', '${1}0', $css );
		$css = preg_replace( '/0 0 0 0/', '0', $css );
		$css = preg_replace( '/#([a-f0-9])\\1([a-f0-9])\\2([a-f0-9])\\3/i', '#\1\2\3', $css );

		return trim( $css );
	}
}
