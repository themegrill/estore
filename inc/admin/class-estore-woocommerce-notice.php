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
	}

	public function woocommerce_notice() {
		if (  ! class_exists( 'WooCommerce' ) ) {
			add_action( 'admin_notices', array( $this, 'woocommerce_notice_markup' ) ); // Show notice.
		}
	}

	/**
	 * Show WooCommerce notice.
	 */
	public function woocommerce_notice_markup() {

		$plugin = 'woocommerce/woocommerce.php';

		?>
		<div id="message" class="notice notice-info is-dismissible">

			<p><?php esc_html_e( 'If you are planning to create an eCommerce site, install the WooCommerce plugin, otherwise you can dismiss this message notice.', 'estore' ); ?></p>

			<?php
			if ( _estore_is_woocommerce_installed() ) {
				if ( ! current_user_can( 'activate_plugins' ) ) {
					return;
				}

				$action_url   = wp_nonce_url( 'plugins.php?action=activate&amp;plugin=' . $plugin . '&amp;plugin_status=all&amp;paged=1&amp;s', 'activate-plugin_' . $plugin );
				$button_label = esc_html__( 'Activate WooCommerce', 'woocommerce' );
			} else {
				if ( ! current_user_can( 'install_plugins' ) ) {
					return;
				}
				$action_url   = wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=woocommerce' ), 'install-plugin_woocommerce' );
				$button_label = esc_html__( 'Install WooCommerce', 'woocommerce' );
			}
			echo '<p>' . sprintf(
				/* Translators: 1: Notice CTA URL 2: Notice CTA text */
				'<a href="%s" class="button-primary">%s</a>',
				$action_url,
				$button_label
			) . '</p>';
			?>
		</div>
		<?php
	}
}

new eStore_WooCommerce_Notice();
