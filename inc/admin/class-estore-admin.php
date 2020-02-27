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
			add_action( 'wp_loaded', array( __CLASS__, 'hide_notices' ) );
			add_action( 'wp_loaded', array( $this, 'admin_notice' ) );
			add_action( 'wp_ajax_import_button', array( $this, 'estore_ajax_import_button_handler' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'estore_ajax_enqueue_scripts' ) );
		}

		/**
		 * Localize array for import button AJAX request.
		 */
		public function estore_ajax_enqueue_scripts() {
			wp_enqueue_script( 'updates' );
			wp_enqueue_script( 'estore-plugin-install-helper', get_template_directory_uri() . '/inc/admin/js/plugin-handle.js', array( 'jquery' ), 1, true );
			wp_localize_script(
				'estore-plugin-install-helper',
				'estore_plugin_helper',
				array(
					'activating' => esc_html__( 'Activating ', 'estore' ),
				)
			);
			$translation_array = array(
				'uri'      => esc_url( admin_url( '/themes.php?page=demo-importer&browse=all&estore-hide-notice=welcome' ) ),
				'btn_text' => esc_html__( 'Processing...', 'estore' ),
				'nonce'    => wp_create_nonce( 'estore_demo_import_nonce' ),
			);
			wp_localize_script( 'estore-plugin-install-helper', 'estore_redirect_demo_page', $translation_array );
		}

		/**
		 * Handle the AJAX process while import or get started button clicked.
		 */
		public function estore_ajax_import_button_handler() {
			check_ajax_referer( 'estore_demo_import_nonce', 'security' );
			$state = '';
			if ( is_plugin_active( 'themegrill-demo-importer/themegrill-demo-importer.php' ) ) {
				$state = 'activated';
			} elseif ( file_exists( WP_PLUGIN_DIR . '/themegrill-demo-importer/themegrill-demo-importer.php' ) ) {
				$state = 'installed';
			}
			if ( 'activated' === $state ) {
				$response['redirect'] = admin_url( '/themes.php?page=demo-importer&browse=all&estore-hide-notice=welcome' );
			} elseif ( 'installed' === $state ) {
				$response['redirect'] = admin_url( '/themes.php?page=demo-importer&browse=all&estore-hide-notice=welcome' );
				if ( current_user_can( 'activate_plugin' ) ) {
					$result = activate_plugin( 'themegrill-demo-importer/themegrill-demo-importer.php' );
					if ( is_wp_error( $result ) ) {
						$response['errorCode']    = $result->get_error_code();
						$response['errorMessage'] = $result->get_error_message();
					}
				}
			} else {
				wp_enqueue_script( 'plugin-install' );

				$response['redirect'] = admin_url( '/themes.php?page=demo-importer&browse=all&estore-hide-notice=welcome' );
				/**
				 * Install Plugin.
				 */
				include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
				include_once ABSPATH . 'wp-admin/includes/plugin-install.php';
				$api = plugins_api(
					'plugin_information',
					array(
						'slug'   => sanitize_key( wp_unslash( 'themegrill-demo-importer' ) ),
						'fields' => array(
							'sections' => false,
						),
					)
				);
				$skin     = new WP_Ajax_Upgrader_Skin();
				$upgrader = new Plugin_Upgrader( $skin );
				$result   = $upgrader->install( $api->download_link );
				if ( $result ) {
					$response['installed'] = 'succeed';
				} else {
					$response['installed'] = 'failed';
				}
				// Activate plugin.
				if ( current_user_can( 'activate_plugin' ) ) {
					$result = activate_plugin( 'themegrill-demo-importer/themegrill-demo-importer.php' );
					if ( is_wp_error( $result ) ) {
						$response['errorCode']    = $result->get_error_code();
						$response['errorMessage'] = $result->get_error_message();
					}
				}
			}
			wp_send_json( $response );
			exit();
		}


		/**
		 * Add admin notice.
		 */
		public function admin_notice() {
			global $estore_version, $pagenow;

			wp_enqueue_style( 'estore-message', get_template_directory_uri() . '/css/admin/message.css', array(), $estore_version );

			// Let's bail on theme activation.
			$notice_nag = get_option( 'estore_admin_notice_welcome' );
			if ( ! $notice_nag ) {
				add_action( 'admin_notices', array( $this, 'welcome_notice' ) );
			}
		}

		/**
		 * Hide a notice if the GET variable is set.
		 */
		public static function hide_notices() {
			if ( isset( $_GET['estore-hide-notice'] ) && isset( $_GET['_estore_notice_nonce'] ) ) {
				if ( ! wp_verify_nonce( $_GET['_estore_notice_nonce'], 'estore_hide_notices_nonce' ) ) {
					wp_die( esc_html__( 'Action failed. Please refresh the page and retry.', 'estore' ) );
				}

				if ( ! current_user_can( 'manage_options' ) ) {
					wp_die( esc_html__( 'Cheatin&#8217; huh?', 'estore' ) );
				}

				$hide_notice = sanitize_text_field( $_GET['estore-hide-notice'] );
				update_option( 'estore_admin_notice_' . $hide_notice, 1 );

				// Hide.
				if ( 'welcome' === $_GET['estore-hide-notice'] ) {
					update_option( 'estore_admin_notice_' . $hide_notice, 1 );
				} else { // Show.
					delete_option( 'estore_admin_notice_' . $hide_notice );
				}
			}
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
						<img src="<?php echo esc_url( get_template_directory_uri() . '/img/estore-logo.png' ); ?>"
								alt="<?php esc_html_e( 'eStore', 'estore' ); ?>"/><?php // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped, Squiz.PHP.EmbeddedPhp.SpacingBeforeClose ?>
					</div>

					<p>
						<?php printf( esc_html__( 'Welcome! Thank you for choosing eStore! To fully take advantage of the best our theme can offer please make sure you visit our %swelcome page%s.', 'estore' ), '<a href="' . esc_url( admin_url( 'themes.php?page=estore-options' ) ) . '">', '</a>' ); ?>

						<span class="plugin-install-notice"><?php esc_html_e( 'Clicking the button below will install and activate the ThemeGrill demo importer plugin.', 'estore' ); ?></span>
					</p>

					<div class="submit">
						<a class="btn-get-started button button-primary button-hero" href="#" data-name="" data-slug="" aria-label="<?php esc_html_e( 'Get started with eStore', 'estore' ); ?>"><?php esc_html_e( 'Get started with eStore', 'estore' ); ?></a>
					</div>
				</div>
			</div>
			<?php
		}
	}

endif;

return new eStore_Admin();
