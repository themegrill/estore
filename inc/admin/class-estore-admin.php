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
			add_action( 'admin_menu', array( $this, 'admin_menu' ) );
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
				'estore-plugin-install-helper', 'estore_plugin_helper',
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
				$api      = plugins_api( 'plugin_information', array(
					'slug'   => sanitize_key( wp_unslash( 'themegrill-demo-importer' ) ),
					'fields' => array(
						'sections' => false,
					),
				) );
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
		 * Add admin menu.
		 */
		public function admin_menu() {
			$theme = wp_get_theme( get_template() );

			$page = add_theme_page( esc_html__( 'About', 'estore' ) . ' ' . $theme->display( 'Name' ), esc_html__( 'About', 'estore' ) . ' ' . $theme->display( 'Name' ), 'activate_plugins', 'estore-sitelibrary', array(
				$this,
				'sitelibrary_screen',
			) );
			add_action( 'admin_print_styles-' . $page, array( $this, 'enqueue_styles' ) );
		}

		/**
		 * Enqueue styles.
		 */
		public function enqueue_styles() {
			global $estore_version;

			wp_enqueue_style( 'estore-welcome', get_template_directory_uri() . '/css/admin/welcome.css', array(), $estore_version );
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
					wp_die( __( 'Action failed. Please refresh the page and retry.', 'estore' ) );
				}

				if ( ! current_user_can( 'manage_options' ) ) {
					wp_die( __( 'Cheatin&#8217; huh?', 'estore' ) );
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
						<img src="<?php echo get_template_directory_uri(); ?>/img/estore-logo.png" alt="<?php esc_html_e( 'eStore', 'estore' ); ?>" /><?php // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped, Squiz.PHP.EmbeddedPhp.SpacingBeforeClose ?>
					</div>

					<p>
						<?php printf( esc_html__( 'Welcome! Thank you for choosing eStore! To fully take advantage of the best our theme can offer please make sure you visit our %swelcome page%s.', 'estore' ), '<a href="' . esc_url( admin_url( 'themes.php?page=estore-welcome' ) ) . '">', '</a>' ); ?>

						<span class="plugin-install-notice"><?php esc_html_e( 'Clicking the button below will install and activate the ThemeGrill demo importer plugin.', 'estore' ); ?></span>
					</p>

					<div class="submit">
						<a class="btn-get-started button button-primary button-hero" href="#" data-name="" data-slug="" aria-label="<?php esc_html_e( 'Get started with eStore', 'estore' ); ?>"><?php esc_html_e( 'Get started with eStore', 'estore' ); ?></a>
					</div>
				</div>
			</div>
			<?php
		}

		/**
		 * Intro text/links shown to all about pages.
		 *
		 * @access private
		 */
		private function intro() {
			global $estore_version;
			$theme = wp_get_theme( get_template() );
			?>
			<div class="header">
				<div class="info">
					<h1>
						<?php esc_html_e( 'About', 'estore' ); ?>
						<?php echo $theme->display( 'Name' ); ?>
						<span class="version-container"><?php echo esc_html( $estore_version ); ?></span>
					</h1>

					<div class="tg-about-text about-text">
						<?php echo $theme->display( 'Description' ); ?>
					</div>

					<a href="https://themegrill.com/" target="_blank" class="wp-badge tg-welcome-logo"></a>
				</div>
			</div>

			<p class="estore-actions">
				<a href="<?php echo esc_url( 'https://themegrill.com/themes/estore/?utm_source=estore-about&utm_medium=theme-info-link&utm_campaign=theme-info' ); ?>"
				   class="button button-secondary" target="_blank"><?php esc_html_e( 'Theme Info', 'estore' ); ?></a>

				<a href="<?php echo esc_url( apply_filters( 'estore_pro_theme_url', 'https://demo.themegrill.com/estore/' ) ); ?>"
				   class="button button-secondary docs"
				   target="_blank"><?php esc_html_e( 'View Demo', 'estore' ); ?></a>

				<a href="<?php echo esc_url( apply_filters( 'estore_pro_theme_url', 'https://themegrill.com/themes/estore/?utm_source=estore-about&utm_medium=view-pro-link&utm_campaign=view-pro#free-vs-pro' ) ); ?>"
				   class="button button-primary docs" target="_blank"><?php esc_html_e( 'View Pro', 'estore' ); ?></a>

				<a href="<?php echo esc_url( apply_filters( 'estore_pro_theme_url', 'https://wordpress.org/support/theme/estore/reviews/?filter=5' ) ); ?>"
				   class="button button-secondary docs"
				   target="_blank"><?php esc_html_e( 'Rate this theme', 'estore' ); ?></a>
			</p>

			<h2 class="nav-tab-wrapper">
				<a class="nav-tab <?php if ( empty( $_GET['tab'] ) && $_GET['page'] == 'estore-sitelibrary' ) {
					echo 'nav-tab-active';
				} ?>" href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'estore-sitelibrary' ), 'themes.php' ) ) ); ?>">
					<?php esc_html_e( 'Site Library', 'estore' ); ?>
				</a>
				<a class="nav-tab <?php if ( isset( $_GET['tab'] ) && $_GET['tab'] == 'welcome' ) {
					echo 'nav-tab-active';
				} ?>" href="<?php echo esc_url( admin_url( add_query_arg( array(
					'page' => 'estore-sitelibrary',
					'tab'  => 'welcome',
				), 'themes.php' ) ) ); ?>">
					<?php esc_html_e( 'Getting Started', 'estore' ); ?>
				</a>
				<a class="nav-tab <?php if ( isset( $_GET['tab'] ) && $_GET['tab'] == 'supported_plugins' ) {
					echo 'nav-tab-active';
				} ?>" href="<?php echo esc_url( admin_url( add_query_arg( array(
					'page' => 'estore-sitelibrary',
					'tab'  => 'supported_plugins',
				), 'themes.php' ) ) ); ?>">
					<?php esc_html_e( 'Supported Plugins', 'estore' ); ?>
				</a>
				<a class="nav-tab <?php if ( isset( $_GET['tab'] ) && $_GET['tab'] == 'free_vs_pro' ) {
					echo 'nav-tab-active';
				} ?>" href="<?php echo esc_url( admin_url( add_query_arg( array(
					'page' => 'estore-sitelibrary',
					'tab'  => 'free_vs_pro',
				), 'themes.php' ) ) ); ?>">
					<?php esc_html_e( 'Free Vs Pro', 'estore' ); ?>
				</a>
				<a class="nav-tab <?php if ( isset( $_GET['tab'] ) && $_GET['tab'] == 'changelog' ) {
					echo 'nav-tab-active';
				} ?>" href="<?php echo esc_url( admin_url( add_query_arg( array(
					'page' => 'estore-sitelibrary',
					'tab'  => 'changelog',
				), 'themes.php' ) ) ); ?>">
					<?php esc_html_e( 'Changelog', 'estore' ); ?>
				</a>
			</h2>
			<?php
		}

		/**
		 * Site library screen page.
		 */
		public function sitelibrary_screen() {
			$current_tab = empty( $_GET['tab'] ) ? 'library' : sanitize_title( $_GET['tab'] );

			// Look for a {$current_tab}_screen method.
			if ( is_callable( array( $this, $current_tab . '_screen' ) ) ) {
				return $this->{$current_tab . '_screen'}();
			}

			// Fallback to about screen.
			return $this->sitelibrary_display_screen();
		}

		/**
		 * Render site library.
		 */
		public function sitelibrary_display_screen() {
			?>
			<div class="wrap about-wrap">
				<?php
				$this->intro();
				?>

				<div class="plugin-install-notice">
					<?php esc_html_e( 'Clicking the "Import" button in any of the demos below will install and activate the ThemeGrill demo importer plugin.', 'estore' ); ?>
				</div>

				<?php
				// Display site library.
				echo eStore_Site_Library::estore_site_library_page_content();
				?>
			</div>
			<?php
		}

		/**
		 * Welcome screen page.
		 */
		public function welcome_screen() {
			$this->about_screen();
		}

		/**
		 * Output the about screen.
		 */
		public function about_screen() {
			$theme = wp_get_theme( get_template() );
			?>
			<div class="wrap about-wrap">

				<?php $this->intro(); ?>

				<div class="changelog point-releases">
					<div class="under-the-hood two-col">
						<div class="col">
							<h3><?php esc_html_e( 'Import Demo', 'estore' ); ?></h3>
							<p><?php esc_html_e( 'Needs ThemeGrill Demo Importer plugin.', 'estore' ) ?></p>

							<div class="submit">
								<a class="btn-get-started button button-primary button-hero" href="#" data-name="" data-slug="" aria-label="<?php esc_html_e( 'Import', 'estore' ); ?>"><?php esc_html_e( 'Import', 'estore' ); ?></a>
							</div>
						</div>

						<div class="col">
							<h3><?php esc_html_e( 'Theme Customizer', 'estore' ); ?></h3>
							<p><?php esc_html_e( 'All Theme Options are available via Customize screen.', 'estore' ) ?></p>
							<p><a href="<?php echo admin_url( 'customize.php' ); ?>"
							      class="button button-secondary"><?php esc_html_e( 'Customize', 'estore' ); ?></a></p>
						</div>

						<div class="col">
							<h3><?php esc_html_e( 'Documentation', 'estore' ); ?></h3>
							<p><?php esc_html_e( 'Please view our documentation page to setup the theme.', 'estore' ) ?></p>
							<p>
								<a href="<?php echo esc_url( 'https://docs.themegrill.com/estore/?utm_source=estore-about&utm_medium=documentation-link&utm_campaign=documentation' ); ?>"
								   class="button button-secondary" target="_blank"><?php esc_html_e( 'Documentation', 'estore' ); ?></a>
							</p>
						</div>

						<div class="col">
							<h3><?php esc_html_e( 'Got theme support question?', 'estore' ); ?></h3>
							<p><?php esc_html_e( 'Please put it in our dedicated support forum.', 'estore' ) ?></p>
							<p>
								<a href="<?php echo esc_url( 'https://themegrill.com/support-forum/?utm_source=estore-about&utm_medium=support-forum-link&utm_campaign=support-forum' ); ?>"
								   class="button button-secondary" target="_blank"><?php esc_html_e( 'Support Forum', 'estore' ); ?></a>
							</p>
						</div>

						<div class="col">
							<h3><?php esc_html_e( 'Need more features?', 'estore' ); ?></h3>
							<p><?php esc_html_e( 'Upgrade to PRO version for more exciting features.', 'estore' ) ?></p>
							<p>
								<a href="<?php echo esc_url( 'https://themegrill.com/themes/estore/?utm_source=estore-about&utm_medium=view-pro-link&utm_campaign=view-pro#free-vs-pro' ); ?>"
								   class="button button-secondary" target="_blank"><?php esc_html_e( 'View Pro', 'estore' ); ?></a>
							</p>
						</div>

						<div class="col">
							<h3><?php esc_html_e( 'Got sales related question?', 'estore' ); ?></h3>
							<p><?php esc_html_e( 'Please send it via our sales contact page.', 'estore' ) ?></p>
							<p>
								<a href="<?php echo esc_url( 'https://themegrill.com/contact/?utm_source=estore-about&utm_medium=contact-page-link&utm_campaign=contact-page' ); ?>"
								   class="button button-secondary" target="_blank"><?php esc_html_e( 'Contact Page', 'estore' ); ?></a>
							</p>
						</div>

						<div class="col">
							<h3>
								<?php
								esc_html_e( 'Translate', 'estore' );
								echo ' ' . $theme->display( 'Name' );
								?>
							</h3>
							<p><?php esc_html_e( 'Click below to translate this theme into your own language.', 'estore' ) ?></p>
							<p>
								<a href="<?php echo esc_url( 'http://translate.wordpress.org/projects/wp-themes/estore' ); ?>"
								   class="button button-secondary">
									<?php
									esc_html_e( 'Translate', 'estore' );
									echo ' ' . $theme->display( 'Name' );
									?>
								</a>
							</p>
						</div>
					</div>
				</div>

				<div class="return-to-dashboard estore">
					<?php if ( current_user_can( 'update_core' ) && isset( $_GET['updated'] ) ) : ?>
						<a href="<?php echo esc_url( self_admin_url( 'update-core.php' ) ); ?>">
							<?php is_multisite() ? esc_html_e( 'Return to Updates', 'estore' ) : esc_html_e( 'Return to Dashboard &rarr; Updates', 'estore' ); ?>
						</a> |
					<?php endif; ?>
					<a href="<?php echo esc_url( self_admin_url() ); ?>"><?php is_blog_admin() ? esc_html_e( 'Go to Dashboard &rarr; Home', 'estore' ) : esc_html_e( 'Go to Dashboard', 'estore' ); ?></a>
				</div>
			</div>
			<?php
		}

		/**
		 * Output the changelog screen.
		 */
		public function changelog_screen() {
			global $wp_filesystem;

			?>
			<div class="wrap about-wrap">

				<?php $this->intro(); ?>

				<p class="about-description"><?php esc_html_e( 'View changelog below.', 'estore' ); ?></p>

				<?php
				$changelog_file = apply_filters( 'estore_changelog_file', get_template_directory() . '/readme.txt' );

				// Check if the changelog file exists and is readable.
				if ( $changelog_file && is_readable( $changelog_file ) ) {
					WP_Filesystem();
					$changelog      = $wp_filesystem->get_contents( $changelog_file );
					$changelog_list = $this->parse_changelog( $changelog );

					echo wp_kses_post( $changelog_list );
				}
				?>
			</div>
			<?php
		}

		/**
		 * Parse changelog from readme file.
		 *
		 * @param  string $content
		 *
		 * @return string
		 */
		private function parse_changelog( $content ) {
			$matches   = null;
			$regexp    = '~==\s*Changelog\s*==(.*)($)~Uis';
			$changelog = '';

			if ( preg_match( $regexp, $content, $matches ) ) {
				$changes = explode( '\r\n', trim( $matches[1] ) );

				$changelog .= '<pre class="changelog">';

				foreach ( $changes as $index => $line ) {
					$changelog .= wp_kses_post( preg_replace( '~(=\s*Version\s*(\d+(?:\.\d+)+)\s*=|$)~Uis', '<span class="title">${1}</span>', $line ) );
				}

				$changelog .= '</pre>';
			}

			return wp_kses_post( $changelog );
		}

		/**
		 * Output the supported plugins screen.
		 */
		public function supported_plugins_screen() {
			?>
			<div class="wrap about-wrap">

				<?php $this->intro(); ?>

				<p class="about-description"><?php esc_html_e( 'This theme recommends following plugins.', 'estore' ); ?></p>
				<ol>
					<li><a href="<?php echo esc_url( 'https://wordpress.org/plugins/social-icons/' ); ?>"
					       target="_blank"><?php esc_html_e( 'Social Icons', 'estore' ); ?></a>
						<?php esc_html_e( ' by ThemeGrill', 'estore' ); ?>
					</li>
					<li><a href="<?php echo esc_url( 'https://wordpress.org/plugins/easy-social-sharing/' ); ?>"
					       target="_blank"><?php esc_html_e( 'Easy Social Sharing', 'estore' ); ?></a>
						<?php esc_html_e( ' by ThemeGrill', 'estore' ); ?>
					</li>
					<li><a href="<?php echo esc_url( 'https://wordpress.org/plugins/everest-forms/' ); ?>"
					       target="_blank"><?php esc_html_e( 'Everest Forms – Easy Contact Form and Form Builder', 'estore' ); ?></a>
						<?php esc_html_e( ' by ThemeGrill', 'estore' ); ?>
					</li>
					<li><a href="<?php echo esc_url( 'https://wordpress.org/plugins/wp-pagenavi/' ); ?>"
					       target="_blank"><?php esc_html_e( 'WP-PageNavi', 'estore' ); ?></a></li>
					<li><a href="<?php echo esc_url( 'https://wordpress.org/plugins/woocommerce/' ); ?>"
					       target="_blank"><?php esc_html_e( 'WooCommerce', 'estore' ); ?></a></li>
					<li><a href="<?php echo esc_url( 'https://wordpress.org/plugins/yith-woocommerce-wishlist/' ); ?>"
					       target="_blank"><?php esc_html_e( 'YITH WooCommerce Wishlist', 'estore' ); ?></a>
					</li>
					<li><a href="<?php echo esc_url( 'https://wpml.org/' ); ?>"
					       target="_blank"><?php esc_html_e( 'WPML', 'estore' ); ?></a>
					</li>
				</ol>

			</div>
			<?php
		}

		/**
		 * Output the free vs pro screen.
		 */
		public function free_vs_pro_screen() {
			?>
			<div class="wrap about-wrap">

				<?php $this->intro(); ?>

				<p class="about-description"><?php esc_html_e( 'Upgrade to PRO version for more exciting features.', 'estore' ); ?></p>

				<table>
					<thead>
					<tr>
						<th class="table-feature-title"><h3><?php esc_html_e( 'Features', 'estore' ); ?></h3></th>
						<th><h3><?php esc_html_e( 'eStore', 'estore' ); ?></h3></th>
						<th><h3><?php esc_html_e( 'eStore Pro', 'estore' ); ?></h3></th>
					</tr>
					</thead>
					<tbody>
					<tr>
						<td><h3><?php esc_html_e( 'Different Header Layouts', 'estore' ); ?></h3></td>
						<td><?php esc_html_e( 'Default header layout (free)', 'estore' ); ?></td>
						<td><?php esc_html_e( '3 different header layouts', 'estore' ); ?></td>
					</tr>
					<tr>
						<td><h3><?php esc_html_e( 'Google Fonts Option', 'estore' ); ?></h3></td>
						<td><span class="dashicons dashicons-no"></span></td>
						<td><?php esc_html_e( '600+', 'estore' ); ?></td>
					</tr>
					<tr>
						<td><h3><?php esc_html_e( 'Font Size options', 'estore' ); ?></h3></td>
						<td><span class="dashicons dashicons-no"></span></td>
						<td><span class="dashicons dashicons-yes"></span></td>
					</tr>
					<tr>
						<td><h3><?php esc_html_e( 'Color Palette', 'estore' ); ?></h3></td>
						<td><?php esc_html_e( 'Primary Color Option', 'estore' ); ?></td>
						<td><?php esc_html_e( 'Multiple Color Options', 'estore' ); ?></td>
					</tr>
					<tr>
						<td><h3><?php esc_html_e( 'Translation Ready', 'estore' ); ?></h3></td>
						<td><span class="dashicons dashicons-yes"></span></td>
						<td><span class="dashicons dashicons-yes"></span></td>
					</tr>
					<tr>
						<td><h3><?php esc_html_e( 'Woocommerce Compatible', 'estore' ); ?></h3></td>
						<td><span class="dashicons dashicons-yes"></span></td>
						<td><span class="dashicons dashicons-yes"></span></td>
					</tr>
					<tr>
						<td><h3><?php esc_html_e( 'YITH Wishlist Compatible', 'estore' ); ?></h3></td>
						<td><span class="dashicons dashicons-yes"></span></td>
						<td><span class="dashicons dashicons-yes"></span></td>
					</tr>
					<tr>
						<td><h3><?php esc_html_e( 'WPML Compatible', 'estore' ); ?></h3></td>
						<td><span class="dashicons dashicons-yes"></span></td>
						<td><span class="dashicons dashicons-yes"></span></td>
					</tr>
					<tr>
						<td><h3><?php esc_html_e( 'RTL Support', 'estore' ); ?></h3></td>
						<td><span class="dashicons dashicons-yes"></span></td>
						<td><span class="dashicons dashicons-yes"></span></td>
					</tr>
					<tr>
						<td><h3><?php esc_html_e( 'Footer Copyright Editor', 'estore' ); ?></h3></td>
						<td><span class="dashicons dashicons-no"></span></td>
						<td><span class="dashicons dashicons-yes"></span></td>
					</tr>
					<tr>
						<td><h3><?php esc_html_e( 'Footer Widgets Column', 'estore' ); ?></h3></td>
						<td><?php esc_html_e( '1,2,3,4 Columns', 'estore' ); ?></td>
						<td><?php esc_html_e( '1,2,3,4 Columns', 'estore' ); ?></td>
					</tr>
					<tr>
						<td><h3><?php esc_html_e( 'Demo Content', 'estore' ); ?></h3></td>
						<td><span class="dashicons dashicons-no"></span></td>
						<td><span class="dashicons dashicons-yes"></span></td>
					</tr>
					<tr>
						<td><h3><?php esc_html_e( 'Support', 'estore' ); ?></h3></td>
						<td><?php esc_html_e( 'Forum', 'estore' ); ?></td>
						<td><?php esc_html_e( 'Forum + Emails/Support Ticket', 'estore' ); ?></td>
					</tr>
					<tr>
						<td><h3><?php esc_html_e( 'Extra Options on Widgets', 'estore' ); ?></h3></td>
						<td><span class="dashicons dashicons-no"></span></td>
						<td><span class="dashicons dashicons-yes"></span></td>
					</tr>
					<tr>
						<td><h3><?php esc_html_e( 'TG: About Widget', 'estore' ); ?></h3></td>
						<td><span class="dashicons dashicons-yes"></span></td>
						<td><span class="dashicons dashicons-yes"></span></td>
					</tr>
					<tr>
						<td><h3><?php esc_html_e( 'TG: Advertisement', 'estore' ); ?></h3></td>
						<td><span class="dashicons dashicons-yes"></span></td>
						<td><span class="dashicons dashicons-yes"></span></td>
					</tr>
					<tr>
						<td><h3><?php esc_html_e( 'TG: Category Carousel', 'estore' ); ?></h3></td>
						<td><span class="dashicons dashicons-yes"></span></td>
						<td><span class="dashicons dashicons-yes"></span></td>
					</tr>
					<tr>
						<td><h3><?php esc_html_e( 'TG: Category Grid', 'estore' ); ?></h3></td>
						<td><span class="dashicons dashicons-yes"></span></td>
						<td><span class="dashicons dashicons-yes"></span></td>
					</tr>
					<tr>
						<td><h3><?php esc_html_e( 'TG: Category Slider', 'estore' ); ?></h3></td>
						<td><span class="dashicons dashicons-yes"></span></td>
						<td><span class="dashicons dashicons-yes"></span></td>
					</tr>
					<tr>
						<td><h3><?php esc_html_e( 'TG: Featured Posts', 'estore' ); ?></h3></td>
						<td><span class="dashicons dashicons-yes"></span></td>
						<td><span class="dashicons dashicons-yes"></span></td>
					</tr>
					<tr>
						<td><h3><?php esc_html_e( 'TG: Horizontal Promo', 'estore' ); ?></h3></td>
						<td><span class="dashicons dashicons-yes"></span></td>
						<td><span class="dashicons dashicons-yes"></span></td>
					</tr>
					<tr>
						<td><h3><?php esc_html_e( 'TG: Horizontal Promo WC Category', 'estore' ); ?></h3></td>
						<td><span class="dashicons dashicons-yes"></span></td>
						<td><span class="dashicons dashicons-yes"></span></td>
					</tr>
					<tr>
						<td><h3><?php esc_html_e( 'TG: Logo', 'estore' ); ?></h3></td>
						<td><span class="dashicons dashicons-yes"></span></td>
						<td><span class="dashicons dashicons-yes"></span></td>
					</tr>
					<tr>
						<td><h3><?php esc_html_e( 'TG: Product grid', 'estore' ); ?></h3></td>
						<td><span class="dashicons dashicons-yes"></span></td>
						<td><span class="dashicons dashicons-yes"></span></td>
					</tr>
					<tr>
						<td><h3><?php esc_html_e( 'TG: Logos', 'estore' ); ?></h3></td>
						<td><span class="dashicons dashicons-yes"></span></td>
						<td><span class="dashicons dashicons-yes"></span></td>
					</tr>
					<tr>
						<td><h3><?php esc_html_e( 'TG: Products Carousel', 'estore' ); ?></h3></td>
						<td><span class="dashicons dashicons-yes"></span></td>
						<td><span class="dashicons dashicons-yes"></span></td>
					</tr>
					<tr>
						<td><h3><?php esc_html_e( 'TG: Product slider', 'estore' ); ?></h3></td>
						<td><span class="dashicons dashicons-yes"></span></td>
						<td><span class="dashicons dashicons-yes"></span></td>
					</tr>
					<tr>
						<td><h3><?php esc_html_e( 'TG: Vertical Promo', 'estore' ); ?></h3></td>
						<td><span class="dashicons dashicons-yes"></span></td>
						<td><span class="dashicons dashicons-yes"></span></td>
					</tr>
					<tr>
						<td><h3><?php esc_html_e( 'TG: Vertical Promo WC Category', 'estore' ); ?></h3></td>
						<td><span class="dashicons dashicons-yes"></span></td>
						<td><span class="dashicons dashicons-yes"></span></td>
					</tr>
					<tr>
						<td><h3><?php esc_html_e( 'TG: Testimonials', 'estore' ); ?></h3></td>
						<td><span class="dashicons dashicons-no"></span></td>
						<td><span class="dashicons dashicons-yes"></span></td>
					</tr>
					<tr>
						<td><h3><?php esc_html_e( 'TG: Icon Text', 'estore' ); ?></h3></td>
						<td><span class="dashicons dashicons-no"></span></td>
						<td><span class="dashicons dashicons-yes"></span></td>
					</tr>
					<tr>
						<td><h3><?php esc_html_e( 'TG: Products Tab', 'estore' ); ?></h3></td>
						<td><span class="dashicons dashicons-no"></span></td>
						<td><span class="dashicons dashicons-yes"></span></td>
					</tr>
					<tr>
						<td></td>
						<td></td>
						<td class="btn-wrapper">
							<a href="<?php echo esc_url( apply_filters( 'estore_pro_theme_url', 'https://themegrill.com/themes/estore/?utm_source=estore-free-vs-pro-table&utm_medium=view-pro-link&utm_campaign=view-pro#free-vs-pro' ) ); ?>" class="button button-secondary docs" target="_blank"><?php _e( 'View Pro', 'estore' ); ?></a>
						</td>
					</tr>
					</tbody>
				</table>

			</div>
			<?php
		}
	}

endif;

return new eStore_Admin();
