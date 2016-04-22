<?php
/**
 * ThemeGrill Starter functions and definitions.
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package ThemeGrill
 * @subpackage eStore
 * @since eStore 0.1
 */

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
	add_theme_support( 'custom-logo',
		array(
    		'flex-width' => true,
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
   register_nav_menus( array(
	  'primary'   => esc_html__( 'Primary Menu', 'estore' ),
	  'header'    => esc_html__( 'Header Top Bar Menu', 'estore' ),
	  'secondary' => esc_html__( 'Secondary Menu', 'estore' ),
   ) );

   /*
	* Switch default core markup for search form, comment form, and comments
	* to output valid HTML5.
	*/
   add_theme_support( 'html5', array(
	  'search-form',
	  'comment-form',
	  'comment-list',
	  'gallery',
	  'caption',
   ) );

   // Set up the WordPress core custom background feature.
   add_theme_support( 'custom-background', apply_filters( 'estore_custom_background_args', array(
	  'default-color' => 'ffffff',
	  'default-image' => '',
   ) ) );

   // Cropping the images to different sizes to be used in the theme
	add_image_size( 'estore-featured-image', 380, 250, true );
	add_image_size( 'estore-product-grid', '75', '75', true );
	add_image_size( 'estore-square', '444', '444', true );
	add_image_size( 'estore-slider', '800', '521', true );
	add_image_size( 'estore-medium-image', 250, 180, true );

	// Declare WooCommerce Support
	add_theme_support( 'woocommerce' );
}
endif; // estore_setup
add_action( 'after_setup_theme', 'estore_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function estore_content_width() {
   $GLOBALS['content_width'] = apply_filters( 'estore_content_width', 870 );
}
add_action( 'after_setup_theme', 'estore_content_width', 0 );

/**
 * Enqueue scripts and styles.
 */
function estore_scripts() {

	$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

	// Load fontawesome
	wp_enqueue_style( 'font-awesome', get_template_directory_uri().'/font-awesome/css/font-awesome.min.css', array(), '4.3.0' );

	// Load bxslider
	wp_enqueue_script( 'bxslider', get_template_directory_uri().'/js/jquery.bxslider' . $suffix . '.js', array('jquery'), false, true );

	wp_enqueue_script( 'superfish', get_template_directory_uri().'/js/superfish' . $suffix . '.js', array('jquery'), false, true );

	wp_enqueue_style ( 'estore-googlefonts', '//fonts.googleapis.com/css?family=Open+Sans:400,600,700,300' );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}

	wp_enqueue_style( 'estore-style', get_stylesheet_uri() );

	wp_enqueue_style( 'estore-reponsive', get_template_directory_uri().'/css/responsive.css', array(), '1.0.0' );

	wp_enqueue_script( 'estore-custom', get_template_directory_uri() . '/js/custom' . $suffix . '.js', array('jquery'), false, true );
}
add_action( 'wp_enqueue_scripts', 'estore_scripts' );

/**
 * Enqeue scripts in admin section for widgets.
 */
add_action('admin_enqueue_scripts', 'estore_admin_scripts');

function estore_admin_scripts( $hook ) {
	global $post_type;

	if( $hook == 'widgets.php' || $hook == 'customize.php' ) {
		// Image Uploader
		wp_enqueue_media();
		wp_enqueue_script( 'estore-script', get_template_directory_uri() . '/js/image-uploader.js', false, '1.0', true );
	}

	if( $post_type == 'page' ) {
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
 * eStore Functions.
 */
require get_template_directory() . '/inc/estore.php';

/**
 * eStore Functions related to WooCommerce.
 */
if(class_exists('woocommerce')) {
	require get_template_directory() . '/inc/woocommerce.php';
}

/**
 * Constant Definition
 */
define( 'Estore_ADMIN_IMAGES_URL', get_template_directory_uri() . '/inc/admin/images');

/**
 * Design Related Metaboxes
 */
require get_template_directory() . '/inc/admin/meta-boxes.php';
