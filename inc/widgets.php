<?php
/**
 * Contains all the functions related to sidebar and widget.
 *
 * @package ThemeGrill
 * @subpackage estore
 * @since estore 1.0
 */

add_action( 'widgets_init', 'estore_widgets_init' );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function estore_widgets_init() {
	// Registering Right Sidebar
   register_sidebar( array(
      'name'          => esc_html__( 'Right Sidebar', 'estore' ),
      'id'            => 'estore_sidebar_right',
      'description'   => '',
      'before_widget' => '<section id="%1$s" class="widget %2$s">',
      'after_widget'  => '</section>',
      'before_title'  => '<h3 class="widget-title"><span>',
      'after_title'   => '</span></h3>'
   ) );
	// Registering Left Sidebar
	register_sidebar( array(
		'name'          => esc_html__( 'Left Sidebar', 'estore' ),
		'id'            => 'estore_sidebar_left',
		'description'   => '',
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h3 class="widget-title"><span>',
		'after_title'   => '</span></h3>'
	) );
   // Register Header Right Sidebar
   register_sidebar( array(
      'name'          => esc_html__( 'Header Right Sidebar', 'estore' ),
      'id'            => 'estore_sidebar_header',
      'description'   => '',
      'before_widget' => '<section id="%1$s" class="widget %2$s">',
      'after_widget'  => '</section>',
      'before_title'  => '<h3 class="widget-title"><span>',
      'after_title'   => '</span></h3>'
   ) );

	// Register Slider Sidebar
	register_sidebar( array(
		'name'          => esc_html__( 'Front Page: Slider Area', 'estore' ),
		'id'            => 'estore_sidebar_slider',
		'description'   => '',
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>'
	) );

	// Register Area beside slider Sidebar
	register_sidebar( array(
		'name'          => esc_html__( 'Front Page: Area beside Slider', 'estore' ),
		'id'            => 'estore_sidebar_slider_beside',
		'description'   => '',
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>'
	) );

   // Register Front Page Sidebar
   register_sidebar( array(
      'name'          => esc_html__( 'Front Page Sidebar', 'estore' ),
      'id'            => 'estore_sidebar_front',
      'description'   => '',
      'before_widget' => '<section id="%1$s" class="widget %2$s">',
      'after_widget'  => '</section>',
      'before_title'  => '<h3 class="widget-title">',
      'after_title'   => '</h3>'
   ) );

	// Footer Widgets
	$footer_sidebar_count = get_theme_mod('estore_footer_widgets', '4');

	register_sidebar( array(
		'name'          => esc_html__( 'Footer Sidebar 1', 'estore' ),
		'id'            => 'estore_footer_sidebar1',
		'description'   => esc_html__( 'Show widgets at Footer section', 'estore' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h3 class="widget-title"><span>',
		'after_title'   => '</span></h3>',
	) );

	if ( $footer_sidebar_count >= 2 ) {
		register_sidebar( array(
			'name'          => esc_html__( 'Footer Sidebar 2', 'estore' ),
			'id'            => 'estore_footer_sidebar2',
			'description'   => esc_html__( 'Show widgets at Footer section', 'estore' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h3 class="widget-title"><span>',
			'after_title'   => '</span></h3>',
		) );
	}

	if ( $footer_sidebar_count >= 3 ) {
		register_sidebar( array(
			'name'          => esc_html__( 'Footer Sidebar 3', 'estore' ),
			'id'            => 'estore_footer_sidebar3',
			'description'   => esc_html__( 'Show widgets at Footer section', 'estore' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h3 class="widget-title"><span>',
			'after_title'   => '</span></h3>',
		) );
	}

	if ($footer_sidebar_count >= 4 ) {
		register_sidebar( array(
			'name'          => esc_html__( 'Footer Sidebar 4', 'estore' ),
			'id'            => 'estore_footer_sidebar4',
			'description'   => esc_html__( 'Show widgets at Footer section', 'estore' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h3 class="widget-title"><span>',
			'after_title'   => '</span></h3>',
		) );
	}

	// Widgets Registration
	register_widget( 'estore_728x90_ad' );
	register_widget( 'estore_about' );
	register_widget( 'estore_featured_posts_widget' );
	register_widget( 'estore_logo_widget' );
	register_widget( 'estore_featured_posts_slider_widget' );
	register_widget( 'estore_featured_posts_carousel_widget' );
	register_widget( 'estore_posts_grid' );
	register_widget( 'estore_vertical_promo_widget' );
	register_widget( 'estore_full_width_promo_widget' );
}

/**
 * Include eStore widgets class.
 */
// Class: TG: About Widget.
require_once get_template_directory() . '/inc/widgets/class-estore-about.php';

// Class: TG: Advertisement.
require_once get_template_directory() . '/inc/widgets/class-estore-728x90-ad.php';

// Class: TG: Featured Posts.
require_once get_template_directory() . '/inc/widgets/class-estore-featured-posts-widget.php';

// Class: TG: Logo.
require_once get_template_directory() . '/inc/widgets/classs-estore-logo-widget.php';

// Class: TG: Category Slider.
require_once get_template_directory() . '/inc/widgets/class-estore-featured-posts-slider-widget.php';

// Class: TG: Category Carousel.
require_once get_template_directory() . '/inc/widgets/class-estore-featured-posts-carousel-widget.php';

// Class: TG: Category Grid.
require_once get_template_directory() . '/inc/widgets/class-estore-posts-grid.php';

// Class: TG: Vertical Promo.
require_once get_template_directory() . '/inc/widgets/class-estore-vertical-promo-widget.php';

// Class: TG: Horizontal Promo.
require_once get_template_directory() . '/inc/widgets/class-estore-full-width-promo-widget.php';

// Class: TG: Horizontal Promo WC Category.
require_once get_template_directory() . '/inc/widgets/class-estore-woocommerce-full-width-promo-widget.php';

// Class: TG: Vertical Promo WC Category.
require_once get_template_directory() . '/inc/widgets/class-estore-woocommerce-vertical-promo-widget.php';

// Class: TG: Products Carousel.
require_once get_template_directory() . '/inc/widgets/class-estore-woocommerce-product-carousel.php';

// Class: TG: Product Slider.
require_once get_template_directory() . '/inc/widgets/class-estore-woocommerce-product-slider.php';

// Class: TG: Product Grid.
require_once get_template_directory() . '/inc/widgets/class-estore-woocommerce-product-grid.php';
