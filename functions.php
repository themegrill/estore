<?php
/**
 * ThemeGrill Starter functions and definitions.
 *
 * @link       https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package    ThemeGrill
 * @subpackage eStore
 * @since      eStore 0.1
 */

/**
 * Set the content width based on the theme's design and stylesheet.
 */
if ( ! isset( $content_width ) ) {
	$content_width = 870;
}
/**
 * $content_width global variable adjustment as per layout option.
 */
function estore_content_width() {
	global $post;
	global $content_width;
	if ( $post ) {
		$layout_meta = get_post_meta( $post->ID, 'estore_layout_call', true );
	}
	if ( empty( $layout_meta ) || is_archive() || is_search() ) {
		$layout_meta = 'default_layout';
	}
	$estore_global_layout = get_theme_mod( 'estore_global_layout', 'right_sidebar' );
	if ( $layout_meta == 'default_layout' ) {
		if ( $estore_global_layout == 'no_sidebar_full_width' ) {
			$content_width = 1200; /* pixels */
		} else {
			$content_width = 870; /* pixels */
		}
	} elseif ( $layout_meta == 'no_sidebar_full_width' ) {
		$content_width = 1200; /* pixels */
	} else {
		$content_width = 870; /* pixels */
	}
}

add_action( 'template_redirect', 'estore_content_width' );

if ( ! function_exists( 'estore_setup' ) ) :
	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 * Note that this function is hooked into the after_setup_theme hook, which
	 * runs before the init hook. The init hook is too late for some features, such
	 * as indicating support for post thumbnails.
	 */
	function estore_setup() {
		/*
		 * Make theme available for translation.
		 * Translations can be filed in the /languages/ directory.
		 * If you're building a theme based on ThemeGrill Starter, use a find and replace
		 * to change 'estore' to the name of your theme in all the template files.
		 */
		load_theme_textdomain( 'estore', get_template_directory() . '/languages' );

		// Add default posts and comments RSS feed links to head.
		add_theme_support( 'automatic-feed-links' );

		/*
		 * Let WordPress manage the document title.
		 * By adding theme support, we declare that this theme does not use a
		 * hard-coded <title> tag in the document head, and expect WordPress to
		 * provide it for us.
		 */
		add_theme_support( 'title-tag' );

		// Adding excerpt option box for pages as well
		add_post_type_support( 'page', 'excerpt' );

		// Adds Support for Custom Logo Introduced in WordPress 4.5
		add_theme_support(
			'custom-logo',
			array(
				'height'      => 80,
				'width'       => 160,
				'flex-width'  => true,
				'flex-height' => true,
			)
		);

		/*
		 * Enable support for Post Thumbnails on posts and pages.
		 *
		 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		 */
		add_theme_support( 'post-thumbnails' );

		// This theme uses wp_nav_menu() in two locations.
		register_nav_menus(
			array(
				'primary'   => esc_html__( 'Primary Menu', 'estore' ),
				'header'    => esc_html__( 'Header Top Bar Menu', 'estore' ),
				'secondary' => esc_html__( 'Secondary Menu', 'estore' ),
			)
		);

		/*
		 * Switch default core markup for search form, comment form, and comments
		 * to output valid HTML5.
		 */
		add_theme_support(
			'html5',
			array(
				'search-form',
				'comment-form',
				'comment-list',
				'gallery',
				'caption',
			)
		);

		// Set up the WordPress core custom background feature.
		add_theme_support(
			'custom-background',
			apply_filters(
				'estore_custom_background_args',
				array(
					'default-color' => 'ffffff',
					'default-image' => '',
				)
			)
		);

		// Cropping the images to different sizes to be used in the theme
		add_image_size( 'estore-featured-image', 380, 250, true );
		add_image_size( 'estore-product-grid', '75', '75', true );
		add_image_size( 'estore-square', '444', '444', true );
		add_image_size( 'estore-slider', '800', '521', true );
		add_image_size( 'estore-medium-image', 250, 180, true );

		// Declare WooCommerce Support
		add_theme_support( 'woocommerce' );

		add_theme_support( 'wc-product-gallery-lightbox' );
		add_theme_support( 'wc-product-gallery-zoom' );
		add_theme_support( 'wc-product-gallery-slider' );

		// Gutenberg wide layout support.
		add_theme_support( 'align-wide' );

		// Gutenberg block styles support.
		add_theme_support( 'wp-block-styles' );

		// Gutenberg responsive embeds support.
		add_theme_support( 'responsive-embeds' );

		// Selective refresh widgets support
		add_theme_support( 'customize-selective-refresh-widgets' );
	}
endif; // estore_setup
add_action( 'after_setup_theme', 'estore_setup' );

/**
 * Enqueue scripts and styles.
 */
function estore_scripts() {

	$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

	// Load fontawesome
	// Font Awesome 6.7.2.
	$font_awesome_styles = array(
		array(
			'handle'  => 'font-awesome-4',
			'file'    => '/v4-shims',
			'version' => '4.7.0',
		),
		array(
			'handle'  => 'font-awesome-all',
			'file'    => '/all',
			'version' => '6.7.2',
		),
		array(
			'handle'  => 'font-awesome-solid',
			'file'    => '/solid',
			'version' => '6.7.2',
		),
		array(
			'handle'  => 'font-awesome-regular',
			'file'    => '/regular',
			'version' => '6.7.2',
		),
		array(
			'handle'  => 'font-awesome-brands',
			'file'    => '/brands',
			'version' => '6.7.2',
		),
	);

	foreach ( $font_awesome_styles as $style ) {
		wp_register_style(
			$style['handle'],
			get_template_directory_uri() . '/font-awesome/css' . $style['file'] . $suffix . '.css',
			false,
			$style['version']
		);
		wp_enqueue_style( $style['handle'] );
	}
//	wp_enqueue_style( 'font-awesome', get_template_directory_uri() . '/font-awesome/css/font-awesome.min.css', array(), '4.7.0' );

	// Load bxslider
	wp_enqueue_script( 'bxslider', get_template_directory_uri() . '/js/jquery.bxslider' . $suffix . '.js', array( 'jquery' ), false, true );

	wp_enqueue_script( 'superfish', get_template_directory_uri() . '/js/superfish' . $suffix . '.js', array( 'jquery' ), false, true );

	wp_enqueue_style( 'estore-googlefonts', '//fonts.googleapis.com/css?family=Open+Sans:400,600,700,300&display=swap' );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}

	wp_enqueue_style( 'estore-style', get_stylesheet_uri() );

	wp_enqueue_style( 'estore-reponsive', get_template_directory_uri() . '/css/responsive.css', array(), '1.0.0' );

	// Skip link focus fix JS enqueue.
	wp_enqueue_script( 'estore-skip-link-focus-fix', get_template_directory_uri() . '/js/skip-link-focus-fix.js', array(), false, true );

	wp_enqueue_script( 'estore-custom', get_template_directory_uri() . '/js/custom' . $suffix . '.js', array( 'jquery' ), false, true );
}

add_action( 'wp_enqueue_scripts', 'estore_scripts' );

/**
 * Enqueue Google fonts and editor styles.
 */
function estore_block_editor_styles() {
	wp_enqueue_style( 'estore-editor-googlefonts', '//fonts.googleapis.com/css?family=Open+Sans:400,600,700,300' );
	wp_enqueue_style( 'estore-block-editor-styles', get_template_directory_uri() . '/style-editor-block.css' );
}

add_action( 'enqueue_block_editor_assets', 'estore_block_editor_styles', 1, 1 );

/**
 * Enqeue scripts in admin section for widgets.
 */
add_action( 'admin_enqueue_scripts', 'estore_admin_scripts' );

function estore_admin_scripts( $hook ) {
	global $post_type;

	if ( $hook == 'widgets.php' || $hook == 'customize.php' ) {
		// Image Uploader
		wp_enqueue_media();
		wp_enqueue_script( 'estore-script', get_template_directory_uri() . '/js/image-uploader.js', false, '1.0', true );
	}

	if ( $post_type == 'page' ) {
		wp_enqueue_script( 'estore-meta-toggle', get_template_directory_uri() . '/js/metabox-toggle.js', false, '1.0', true );
	}
}

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Widgets.
 */
require get_template_directory() . '/inc/widgets.php';

/**
 * Header Image Functions.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Load Functions.
 */
require get_template_directory() . '/inc/functions.php';

/**
 * Admin.
 */
if ( is_admin() ) {
	require get_template_directory() . '/inc/admin/class-estore-theme-review-notice.php';
}

/**
 * Helper Functions.
 */
require get_template_directory() . '/inc/helpers.php';

/**
 * eStore Functions.
 */
require get_template_directory() . '/inc/estore.php';

/**
 * eStore Functions related to WooCommerce.
 */
if ( class_exists( 'woocommerce' ) ) {
	require get_template_directory() . '/inc/woocommerce.php';
}

/**
 * Constant Definition
 */
define( 'Estore_ADMIN_IMAGES_URL', get_template_directory_uri() . '/inc/admin/images' );

/**
 * Design Related Metaboxes
 */
add_action(
	'init',
	function () {
		require get_template_directory() . '/inc/admin/meta-boxes.php';
	}
);

/**
 * Assign the eStore version to a variable.
 */
$estore_theme = wp_get_theme( 'estore' );

define( 'ESTORE_THEME_VERSION', $estore_theme->get( 'Version' ) );

/**
 * Calling in the admin area for the Welcome Page as well as for the new theme notice too.
 */
if ( is_admin() ) {
	require get_template_directory() . '/inc/admin/class-estore-admin.php';
	require get_template_directory() . '/inc/admin/class-estore-dashboard.php';
	require get_template_directory() . '/inc/admin/class-estore-notice.php';
	require get_template_directory() . '/inc/admin/class-estore-welcome-notice.php';
	require get_template_directory() . '/inc/admin/class-estore-upgrade-notice.php';
}

/**
 * Load Jetpack compatibility file.
 */
if ( defined( 'JETPACK__VERSION' ) ) {
	require_once get_template_directory() . '/inc/jetpack.php';
}

if ( ! function_exists( 'estore_entry_title' ) ) :

	/**
	 *
	 */
	function estore_entry_title() {

		if ( is_singular() ) :
			the_title( '<h1 class="entry-title">', '</h1>' );
		elseif ( is_archive() ) :
			the_archive_title( '<h1 class="page-title">', '</h1>' );
		elseif ( is_404() ) :
			?>
			<h1 class="page-title"><?php esc_html_e( 'Oops! That page can&rsquo;t be found.', 'estore' ); ?></h1>
			<?php
		else :
			the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
		endif;
	}

endif;
