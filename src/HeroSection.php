<?php
/**
 * Adds hero section logic through configuration.
 *
 * @package   SeoThemes\Core
 * @author    Lee Anthony <seothemeswp@gmail.com>
 * @author    Craig Simpson <craig@craigsimpson.scot>
 * @copyright 2018, D2 Themes
 * @license   GPL-3.0-or-later
 */

namespace SeoThemes\Core;

/**
 * Add hero section through configuration.
 *
 * Example config (usually located at config/defaults.php):
 *
 * ```
 * use SeoThemes\Core\HeroSection;
 *
 * $core_hero_section = [
 *     HeroSection::ENABLE => [
 *         HeroSection::PAGE            => true,
 *         HeroSection::POST            => false,
 *         HeroSection::PRODUCT         => true,
 *         HeroSection::PORTFOLIO_ITEM  => true,
 *         HeroSection::FRONT_PAGE      => true,
 *         HeroSection::ATTACHMENT      => true,
 *         HeroSection::ERROR_404       => true,
 *         HeroSection::LANDING_PAGE    => false,
 *         HeroSection::BLOG_TEMPLATE   => true,
 *         HeroSection::SEARCH          => true,
 *         HeroSection::AUTHOR          => true,
 *         HeroSection::DATE            => true,
 *         HeroSection::LATEST_POSTS    => true,
 *         HeroSection::BLOG            => true,
 *         HeroSection::SHOP            => true,
 *         HeroSection::PORTFOLIO       => true,
 *         HeroSection::PORTFOLIO_TYPE  => true,
 *         HeroSection::PRODUCT_ARCHIVE => true,
 *         HeroSection::CATEGORY        => true,
 *         HeroSection::TAG             => true,
 *     ],
 * ];
 *
 * return [
 *     HeroSection::class => $core_hero_section,
 * ];
 * ```
 */
class HeroSection extends Component {

    const ENABLE          = 'enable';
	const PAGE            = 'page';
	const POST            = 'post';
	const PRODUCT         = 'product';
	const PORTFOLIO_ITEM  = 'portfolio-item';
	const FRONT_PAGE      = 'front-page';
	const ATTACHMENT      = 'attachment';
	const LANDING_PAGE    = 'landing-page';
	const BLOG_TEMPLATE   = 'blog-template';
	const SEARCH          = 'search';
	const ERROR_404       = 'error-404';
	const LATEST_POSTS    = 'latest-posts';
	const SHOP            = 'shop';
	const PORTFOLIO       = 'portfolio';
	const PORTFOLIO_TYPE  = 'portfolio-type';
	const PRODUCT_ARCHIVE = 'product-archive';
	const AUTHOR          = 'author';
	const DATE            = 'date';
	const BLOG            = 'blog';
	const CATEGORY        = 'category';
	const TAG             = 'tag';

	/**
	 * List of conditional page type checks.
	 *
	 * @var array
	 */
	protected $conditionals;

	/**
	 * Initialize class.
	 *
	 * @since 3.3.0
	 *
	 * @return void
	 */
	public function init() {

		// Add theme support by default.
		add_theme_support( 'hero-section' );

		// Add meta box.
		add_action( 'add_meta_boxes', [ $this, 'add_meta_box' ] );
		add_action( 'save_post', [ $this, 'save_meta_box' ] );

		// Conditionally display hero section.
        if ( array_key_exists( self::ENABLE, $this->config ) ) {
	        foreach ( $this->config[ self::ENABLE ] as $type => $enabled ) {
		        add_action( 'genesis_meta', function () use ( $enabled, $type ) {
			        if ( $this->is_enabled( $enabled, $type ) ) {
				        add_filter( 'body_class', function ( $classes ) {
					        $classes[] = 'has-hero-section';

					        return $classes;
				        } );
				        $this->setup();
			        }
		        }, 100 );
	        }
        }
	}

	/**
	 * Runs conditional check for page type.
	 *
	 * @since 1.0.0
	 *
	 * @param string $type Page type.
	 *
	 * @return bool
	 */
	protected function conditional( $type ) {
		$this->conditionals = [
			self::PAGE            => function () {
				return is_singular( 'page' ) && ! is_page_template( 'page_blog.php' ) && ! is_page_template( '/resources/views/page-landing.php' );
			},
			self::POST            => function () {
				return is_singular( 'post' );
			},
			self::PRODUCT         => function () {
				return is_singular( 'product' );
			},
			self::PORTFOLIO_ITEM  => function () {
				return is_singular( 'portfolio' );
			},
			self::FRONT_PAGE      => function () {
				return is_front_page();
			},
			self::ATTACHMENT      => function () {
				return is_attachment();
			},
			self::LANDING_PAGE    => function () {
				return is_page_template( '/resources/views/page-landing.php' );
			},
			self::BLOG_TEMPLATE   => function () {
				return is_page_template( 'page_blog.php' );
			},
			self::ERROR_404       => function () {
				return is_404();
			},
			self::SEARCH          => function () {
				return is_search();
			},
			self::AUTHOR          => function () {
				return is_author();
			},
			self::DATE            => function () {
				return is_date();
			},
			self::LATEST_POSTS    => function () {
				return is_home() && 'posts' === get_option( 'show_on_front' );
			},
			self::BLOG            => function () {
				return is_home();
			},
			self::SHOP            => function () {
				return class_exists( 'WooCommerce' ) && is_shop();
			},
			self::PORTFOLIO       => function () {
				return is_post_type_archive( 'portfolio' );
			},
			self::PORTFOLIO_TYPE  => function () {
				return is_tax( 'portfolio-type' );
			},
			self::PRODUCT_ARCHIVE => function () {
				return is_tax( [ 'product_cat', 'product_tag' ] );
			},
			self::CATEGORY        => function () {
				return is_category();
			},
			self::TAG             => function () {
				return is_tag();
			},
		];

		return $this->conditionals[ $type ];
	}

	/**
	 * Checks if hero section is enabled on specific page.
	 *
	 * @since 1.0.0
	 *
	 * @param bool $enabled Defined in config.
	 * @param bool $type    Conditional check.
	 *
	 * @return bool
	 */
	protected function is_enabled( $enabled, $type ) {
		$conditional = $this->conditional( $type );

		if ( ! $enabled ) {
			return false;
		}

		if ( ! is_callable( $conditional ) || ! $conditional() ) {
			return false;
		}

		if ( ! current_theme_supports( 'hero-section' ) ) {
			return false;
		}

		if ( 'disable' === get_post_meta( get_the_ID(), '_hero_section', true ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Sets up hero section.
	 *
	 * @since  1.5.0
	 *
	 * @return void
	 */
	protected function setup() {
		if ( is_admin() || is_front_page() ) {
			return;
		}

		if ( is_singular() && ! is_page_template( 'page_blog.php' ) ) {
			remove_action( 'genesis_entry_header', 'genesis_do_post_title' );
		}

		remove_action( 'genesis_entry_header', 'genesis_entry_header_markup_open', 5 );
		remove_action( 'genesis_entry_header', 'genesis_entry_header_markup_close', 15 );
		remove_action( 'genesis_before_loop', 'genesis_do_posts_page_heading' );
		remove_action( 'genesis_archive_title_descriptions', 'genesis_do_archive_headings_open', 5, 3 );
		remove_action( 'genesis_archive_title_descriptions', 'genesis_do_archive_headings_close', 15, 3 );
		remove_action( 'genesis_before_loop', 'genesis_do_date_archive_title' );
		remove_action( 'genesis_before_loop', 'genesis_do_blog_template_heading' );
		remove_action( 'genesis_before_loop', 'genesis_do_taxonomy_title_description', 15 );
		remove_action( 'genesis_before_loop', 'genesis_do_author_title_description', 15 );
		remove_action( 'genesis_before_loop', 'genesis_do_cpt_archive_title_description' );
		remove_action( 'genesis_before_loop', 'genesis_do_search_title' );
		remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title', 5 );
		remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );

		add_filter( 'woocommerce_show_page_title', '__return_null' );
		add_filter( 'genesis_search_title_output', '__return_false' );

		add_action( 'child_theme_hero_section', 'genesis_do_posts_page_heading' );
		add_action( 'child_theme_hero_section', 'genesis_do_date_archive_title' );
		add_action( 'child_theme_hero_section', 'genesis_do_taxonomy_title_description' );
		add_action( 'child_theme_hero_section', 'genesis_do_author_title_description' );
		add_action( 'child_theme_hero_section', 'genesis_do_cpt_archive_title_description' );
		add_action( 'child_theme_hero_section', [ $this, 'title' ], 10 );
		add_action( 'child_theme_hero_section', [ $this, 'excerpt' ], 20 );
		add_action( 'be_title_toggle_remove', [ $this, 'title_toggle' ] );
		add_action( 'genesis_before_content', [ $this, 'remove_404_title' ] );
		add_action( 'genesis_before_content_sidebar_wrap', [ $this, 'attributes' ] );
		add_action( 'genesis_before_content_sidebar_wrap', [ $this, 'display' ] );
	}

	/**
	 * Remove default title of 404 pages.
	 *
	 * @since  1.0.0
	 *
	 * @return void
	 */
	public function remove_404_title() {
		if ( is_404() ) {
			add_filter( 'genesis_markup_entry-title_open', '__return_false' );
			add_filter( 'genesis_markup_entry-title_content', '__return_false' );
			add_filter( 'genesis_markup_entry-title_close', '__return_false' );
		}
	}

	/**
	 * Integrate with Genesis Title Toggle plugin.
	 *
	 * @since  1.0.0
	 *
	 * @author Bill Erickson
	 * @link   http://billerickson.net/code/genesis-title-toggle-theme-integration
	 *
	 * @return void
	 */
	public function title_toggle() {
		remove_action( 'child_theme_hero_section', [ $this, 'title' ], 10 );
		remove_action( 'child_theme_hero_section', [ $this, 'excerpt' ], 20 );
	}

	/**
	 * Display title in hero section.
	 *
	 * @since  1.0.0
	 *
	 * @return void
	 */
	public function title() {
		if ( class_exists( 'WooCommerce' ) && is_shop() ) {
			genesis_markup(
				[
					'open'    => '<h1 %s>',
					'close'   => '</h1>',
					'content' => get_the_title( wc_get_page_id( 'shop' ) ),
					'context' => 'entry-title',
				]
			);
		} elseif ( is_home() && 'posts' === get_option( 'show_on_front' ) ) {
			genesis_markup(
				[
					'open'    => '<h1 %s>',
					'close'   => '</h1>',
					'content' => apply_filters( 'child_theme_latest_posts_title', esc_html( 'Latest Posts' ) ),
					'context' => 'entry-title',
				]
			);
		} elseif ( is_404() ) {
			genesis_markup(
				[
					'open'    => '<h1 %s>',
					'close'   => '</h1>',
					'content' => apply_filters( 'genesis_404_entry_title', esc_html( 'Not found, error 404' ) ),
					// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- Parent theme prefix.
					'context' => 'entry-title',
				]
			);
		} elseif ( is_search() ) {
			genesis_markup(
				[
					'open'    => '<h1 %s>',
					'close'   => '</h1>',
					'content' => apply_filters( 'genesis_search_title_text', esc_html( 'Search results for: ' ) . get_search_query() ),
					// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- Parent theme prefix.
					'context' => 'entry-title',
				]
			);
		} elseif ( is_page_template( 'page_blog.php' ) ) {
			do_action( 'genesis_archive_title_descriptions', get_the_title(), '', 'posts-page-description' ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- Parent theme prefix.
		} elseif ( is_singular() ) {
			genesis_do_post_title();
		}
	}

	/**
	 * Display page excerpt.
	 *
	 * @since  1.0.0
	 *
	 * @return void
	 */
	public function excerpt() {
		if ( class_exists( 'WooCommerce' ) && is_shop() ) {
			woocommerce_result_count();
		} elseif ( is_home() && 'posts' === get_option( 'show_on_front' ) ) {
			printf( '<p class="entry-subtitle" itemprop="description">%s</p>', apply_filters( 'child_theme_latest_posts_excerpt', esc_html( 'Showing the latest posts' ) ) );
		} elseif ( is_search() ) {
			$id = get_page_by_path( 'search' );

			if ( has_excerpt( $id ) ) {
				printf( '<p class="entry-subtitle" itemprop="description">%s</p>', do_shortcode( get_the_excerpt( $id ) ) );
			}
		} elseif ( is_404() ) {
			$id = get_page_by_path( 'error' );

			if ( has_excerpt( $id ) ) {
				printf( '<p class="entry-subtitle" itemprop="description">%s</p>', do_shortcode( get_the_excerpt( $id ) ) );
			}
		} elseif ( ( is_singular() ) && ! is_singular( 'product' ) && has_excerpt() ) {
			printf( '<p class="entry-subtitle" itemprop="description">%s</p>', do_shortcode( get_the_excerpt() ) );
		}
	}

	/**
	 * Display the hero section.
	 *
	 * @since  1.0.0
	 *
	 * @return void
	 */
	public function display() {
		genesis_markup( [
			'open'    => '<section %s><div class="wrap">',
			'context' => 'hero-section',
		] );

		do_action( 'child_theme_hero_section' );

		genesis_markup( [
			'close'   => '</div></section>',
			'context' => 'hero-section',
		] );
	}

	/**
	 * Custom header image callback.
	 *
	 * Loads custom header or featured image depending on what is set on a per
	 * page basis. If a featured image is set for a page, it will override
	 * the default header image. It also gets the image for custom post
	 * types by looking for a page with the same slug as the CPT e.g
	 * the Portfolio CPT archive will pull the featured image from
	 * a page with the slug of 'portfolio', if the page exists.
	 *
	 * @since  1.0.0
	 *
	 * @return string
	 */
	public static function custom_header() {

		$id  = '';
		$url = '';

		if ( class_exists( 'WooCommerce' ) && is_shop() ) {
			$id = wc_get_page_id( 'shop' );

		} elseif ( is_post_type_archive() ) {
			$id = get_page_by_path( get_query_var( 'post_type' ) );
			$id = $id && has_post_thumbnail( $id ) ? $id : false;

		} elseif ( is_category() ) {
			$id = get_page_by_title( 'category-' . get_query_var( 'category_name' ), OBJECT, 'attachment' );

		} elseif ( is_tag() ) {
			$id = get_page_by_title( 'tag-' . get_query_var( 'tag' ), OBJECT, 'attachment' );

		} elseif ( is_tax() ) {
			$id = get_page_by_title( 'term-' . get_query_var( 'term' ), OBJECT, 'attachment' );

		} elseif ( is_front_page() ) {
			$id = get_option( 'page_on_front' );

		} elseif ( 'posts' === get_option( 'show_on_front' ) && is_home() ) {
			$id = get_option( 'page_for_posts' );

		} elseif ( is_search() ) {
			$id = get_page_by_path( 'search' );

		} elseif ( is_404() ) {
			$id = get_page_by_path( 'error' );

		} elseif ( is_singular() ) {
			$id = get_the_id();

		}

		if ( is_object( $id ) ) {
			$url = wp_get_attachment_image_url( $id->ID, 'hero' );

		} elseif ( $id ) {
			$url = get_the_post_thumbnail_url( $id, 'hero' );

		} else {
			$url = false;

		}

		$settings = get_post_meta( $id, '_hero_section', true );

		if ( 'default_image' === $settings ) {
			$url = get_header_image();
		} elseif ( 'disable' === $settings || 'no_image' === $settings ) {
			$url = false;
		}

		if ( $url ) {
			$selector = get_theme_support( 'custom-header', 'header-selector' );

			return printf( '<style type="text/css">' . esc_attr( $selector ) . '{background-image:url(%s)}</style>' . "\n", esc_url( $url ) );
		} else {
			return '';
		}
	}

	/**
	 * Description of expected behavior.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function attributes() {
		add_filter( 'genesis_attr_entry', function ( $atts ) {
			if ( is_singular() ) {
				$atts['itemref'] = 'hero-section';
			}

			return $atts;
		} );

		add_filter( 'genesis_attr_hero-section', function ( $atts ) {
			$atts['id']   = 'hero-section';
			$atts['role'] = 'banner';

			return $atts;
		} );
	}

	/**
	 * Adds meta box.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function add_meta_box() {
		add_meta_box(
			'hero-section',
			'Hero Section',
			[ $this, 'render_meta_box' ],
			[ 'post', 'page', 'product', 'portfolio' ],
			'side',
			'low'
		);
	}

	/**
	 * Save the meta when the post is saved.
	 *
	 * @since 1.0.0
	 *
	 * @param int $post_id The ID of the post being saved.
	 *
	 * @return mixed
	 */
	public function save_meta_box( $post_id ) {
		if ( ! isset( $_POST['hero_section_nonce'] ) ) {
			return $post_id;
		}

		if ( ! wp_verify_nonce( $_POST['hero_section_nonce'], 'hero_section_nonce_action' ) ) {
			return $post_id;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}

		if ( 'page' == $_POST['post_type'] ) {
			if ( ! current_user_can( 'edit_page', $post_id ) ) {
				return $post_id;
			}

		} else {
			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				return $post_id;
			}
		}

		if ( array_key_exists( 'hero_section', $_POST ) ) {
			update_post_meta( $post_id, '_hero_section', $_POST['hero_section'] );
		}
	}

	/**
	 * Render Meta Box content.
	 *
	 * @since 1.0.0
	 *
	 * @param object $post The post object.
	 *
	 * @return void
	 */
	public function render_meta_box( $post ) {
		$value   = get_post_meta( $post->ID, '_hero_section', true );
		$choices = [
			'featured_image',
			'default_image',
			'no_image',
			'disable',
		];

		foreach ( $choices as $choice ) {
			?>
            <label for="hero_section_<?php echo $choice; ?>">
                <input type="radio" name="hero_section"
                       id="hero_section_<?php echo $choice; ?>"
                       value="<?php echo $choice; ?>" <?php checked( $value, $choice ); ?> >
				<?php echo ucwords( str_replace( '_', ' ', $choice ) ); ?>
            </label>
            <br>
			<?php
		}

		wp_nonce_field( 'hero_section_nonce_action', 'hero_section_nonce' );
	}
}
