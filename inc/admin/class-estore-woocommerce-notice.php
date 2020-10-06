<?php
/**
 * eStore WooCommerce Notice Class.
 *
 * @author  ThemeGrill
 * @package estore
 * @since   1.5.2
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

class eStore_WooCommerce_Notice {

	public function __construct() {
		add_action( 'wp_loaded', array( $this, 'woocommerce_notice' ), 20 );
		add_action( 'wp_loaded', array( $this, 'hide_notices' ), 15 );
	}

	public function woocommerce_notice() {
		if ( ! get_option( 'estore_admin_notice_welcome' ) ) {
			add_action( 'admin_notices', array( $this, 'woocommerce_notice_markup' ) ); // Show notice.
		}
	}

	/**
	 * Show WooCommerce notice.
		 */
	public function woocommerce_notice_markup() {
		$dismiss_url = wp_nonce_url(
			remove_query_arg( array( 'activated' ), add_query_arg( 'estore-hide-notices', 'welcome' ) ),
			'estore_hide_notices_nonce',
			'_estore_notice_nonce'
		);
		if ( ! estore_is_woocommerce_activated() && current_user_can( 'install_plugins' ) ) :
			?>
		<div id="message" class="notice notice-info estore-notice">
			<a class="estore-notice-dismiss notice-dismiss" href="<?php echo esc_url( $dismiss_url ); ?>"></a>

			<h2><?php esc_html_e( 'Thank you for choosing eStore.', 'estore' ); ?></h2>
			<p><?php esc_html_e( 'To enable eCommerce features, you need to install the WooCommerce plugin.', 'estore' ); ?></p>
		</div>
			<?php
		endif;
	}

	/**
	 * Hide a notice if the GET variable is set.
	 */
	public function hide_notices() {
		if ( isset( $_GET['estore-hide-notices'] ) && isset( $_GET['_estore_notice_nonce'] ) ) {
			if ( ! wp_verify_nonce( wp_unslash( $_GET['_estore_notice_nonce'] ), 'estore_hide_notices_nonce' ) ) { // phpcs:ignore WordPress.VIP.ValidatedSanitizedInput.InputNotSanitized
				wp_die( __( 'Action failed. Please refresh the page and retry.', 'estore' ) ); // WPCS: xss ok.
			}

			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die( __( 'Cheatin&#8217; huh?', 'estore' ) ); // WPCS: xss ok.
			}

			$hide_notice = sanitize_text_field( wp_unslash( $_GET['estore-hide-notices'] ) );

			// Hide.
			if ( 'welcome' === $_GET['estore-hide-notices'] ) {
				update_option( 'estore_admin_notice_' . $hide_notice, 1 );
			} else { // Show.
				delete_option( 'estore_admin_notice_' . $hide_notice );
			}
		}
	}
}

new eStore_WooCommerce_Notice();
