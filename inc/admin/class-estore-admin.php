<?php
/**
 * eStore Admin Class.
 *
 * @author  ThemeGrill
 * @package estore
 * @since   1.3.8
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'eStore_Admin' ) ) :

	/**
	 * eStore_Admin Class.
	 */
	class eStore_Admin {


		/**
		 * Constructor.
		 */
		public function __construct() {
			 add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		}

		/**
		 * Localize array for import button AJAX request.
		 */
		public function enqueue_scripts() {
			wp_enqueue_style( 'estore-admin-style', get_template_directory_uri() . '/inc/admin/css/admin.css', array(), ESTORE_THEME_VERSION );

			wp_enqueue_script( 'estore-plugin-install-helper', get_template_directory_uri() . '/inc/admin/js/plugin-handle.js', array( 'jquery' ), ESTORE_THEME_VERSION, true );

			$welcome_data = array(
				'uri'      => esc_url( admin_url( '/themes.php?page=demo-importer&browse=all&estore-hide-notice=welcome' ) ),
				'btn_text' => esc_html__( 'Processing...', 'estore' ),
				'nonce'    => wp_create_nonce( 'estore_demo_import_nonce' ),
			);

			wp_localize_script( 'estore-plugin-install-helper', 'estoreRedirectDemoPage', $welcome_data );
		}
		/**
		 * Show welcome notice.
		 */
		public function welcome_notice() {
			?>
			<div id="message" class="updated estore-message">
				<a class="estore-message-close notice-dismiss" href="<?php echo esc_url( wp_nonce_url( remove_query_arg( array( 'activated' ), add_query_arg( 'estore-hide-notice', 'welcome' ) ), 'estore_hide_notices_nonce', '_estore_notice_nonce' ) ); ?>">
					<?php esc_html_e( 'Dismiss', 'estore' ); ?>
				</a>

				<div class="estore-message-wrapper">
					<div class="estore-logo">
						<img src="<?php echo esc_url( get_template_directory_uri() ); ?>/img/estore-logo.png" alt="<?php esc_attr_e( 'estore', 'estore' ); ?>" /><?php // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped, Squiz.PHP.EmbeddedPhp.SpacingBeforeClose ?>
					</div>

					<p>
						<?php printf( esc_html__( 'Welcome! Thank you for choosing eStore! To fully take advantage of the best our theme can offer please make sure you visit our %1$swelcome page%2$s.', 'estore' ), '<a href="' . esc_url( admin_url( 'themes.php?page=estore-options' ) ) . '">', '</a>' ); ?>

						<span class="plugin-install-notice"><?php esc_html_e( 'Clicking the button below will install and activate the ThemeGrill demo importer plugin.', 'estore' ); ?></span>
					</p>

					<div class="submit">
						<a class="btn-get-started button button-primary button-hero" href="#" data-name="" data-slug="" aria-label="<?php esc_attr_e( 'Get started with eStore', 'estore' ); ?>"><?php esc_html_e( 'Get started with eStore', 'estore' ); ?></a>
					</div>
				</div>
			</div>
			<?php
		}
	}

endif;

return new eStore_Admin();
