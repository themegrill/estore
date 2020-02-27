<?php
/**
 * Estore_Dashboard Class.
 *
 * @author  ThemeGrill
 * @package estore
 * @since   1.4.5
 */

class Estore_Dashboard {
	private static $instance;

	public static function instance( $config ) {

		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	private function __construct() {
		$this->setup_hooks();
	}

	private function setup_hooks() {
		add_action( 'admin_menu', array( $this, 'create_menu' ) );

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	public function enqueue_scripts() {
		wp_enqueue_style(
			'estore-admin-dashboard',
			get_template_directory_uri() . '/admin/css/dashboard.css'
		);
	}

	public function create_menu() {
		add_theme_page(
			'eStore Options',
			'eStore Options',
			'edit_theme_options',
			'estore-options',
			array(
				$this,
				'option_page',
			)
		);
	}

	public function option_page() {
		$theme        = wp_get_theme();
		?>
		<div class="wrap">
		<div class="estore-header">
			<h1>
				<?php
				/* translators: %s: Theme version. */
				echo sprintf( esc_html__( 'eStore %s', 'estore' ), $theme->Version );
				?>
			</h1>
		</div>
		<div class="welcome-panel">
			<div class="welcome-panel-content">
				<h2><?php esc_html_e( 'Welcome to eStore!', 'estore' ); ?></h2>
				<p class="about-description">
					<?php
					/* translators: %s: Theme Name. */
					echo sprintf( esc_html__( 'Important links to get you started with %s', 'estore' ), $theme->Name );
					?>
				</p>

				<div class="welcome-panel-column-container">
					<div class="welcome-panel-column">
						<h3><?php esc_html_e( 'Get Started', 'estore' ); ?></h3>
						<a class="button button-primary button-hero"
						   href="<?php echo esc_url( 'https://docs.themegrill.com/estore/' ); ?>"
						   target="_blank"><?php esc_html_e( 'Learn Basics', 'estore' ); ?>
						</a>
					</div>

					<div class="welcome-panel-column">
						<h3><?php esc_html_e( 'Next Steps', 'estore' ); ?></h3>
						<ul>
							<li><?php printf( '<a target="_blank" href="%s" class="welcome-icon dashicons-media-text">' . esc_html__( 'Documentation', 'estore' ) . '</a>', esc_url( 'https://docs.themegrill.com/estore/' ) ); ?></li>
							<li><?php printf( '<a target="_blank" href="%s" class="welcome-icon dashicons-layout">' . esc_html__( 'Starter Demos', 'estore' ) . '</a>', esc_url( 'https://demo.themegrill.com/estore-demos/' ) ); ?></li>
							<li><?php printf( '<a target="_blank" href="%s" class="welcome-icon welcome-add-page">' . esc_html__( 'Premium Version', 'estore' ) . '</a>', esc_url( 'https://themegrill.com/themes/estore/' ) ); ?></li>
						</ul>
					</div>

					<div class="welcome-panel-column">
						<h3><?php esc_html_e( 'Further Actions', 'estore' ); ?></h3>
						<ul>
							<li><?php printf( '<a target="_blank" href="%s" class="welcome-icon dashicons-businesswoman">' . esc_html__( 'Got theme support question?', 'estore' ) . '</a>', esc_url( 'https://wordpress.org/support/theme/estore/' ) ); ?></li>
							<li><?php printf( '<a target="_blank" href="%s" class="welcome-icon dashicons-thumbs-up">' . esc_html__( 'Leave a review', 'estore' ) . '</a>', esc_url( 'https://wordpress.org/support/theme/estore/reviews/' ) ); ?></li>
						</ul>
					</div>
				</div>
			</div>
		</div>
		<?php
	}
}

$config = array(
	'menu_name'       => __( 'About eStore', 'estore' ),
	'page_name'       => __( 'About eStore', 'estore' ),
	'welcome_content' => __( 'eStore is beautifully designed clean WordPress blog theme. Easy to setup and has a nice set of features that make your site stand out. It is suitable for personal, fashion, food, travel, business, professional, niche and any kind of blogging sites. Comes with various demos for various purposes, which you can easily import with the help of ThemeGrill Demo Importer plugin.', 'estore' ),
	/* translators: s - theme name */
	'welcome_title'   => sprintf( __( 'Welcome to %s! : Version ', 'estore' ), 'eStore' ),
	'tabs'            => array(
		'site_library'        => __( 'Site Library', 'estore' ),
		'getting_started'     => __( 'Getting Started', 'estore' ),
		'recommended_plugins' => __( 'Recommended Plugins', 'estore' ),
		'support'             => __( 'Support', 'estore' ),
		'changelog'           => __( 'Changelog', 'estore' ),
	),

	'site_library' => array(
		'one' => array(),
	),

);

Estore_Dashboard::instance( apply_filters( 'estore_about_page_array', $config ) );
