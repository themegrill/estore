<?php
/**
 * eStore_Site_Library class to fetch the
 * theme demo configs from GitHub repo.
 *
 * @package eStore
 */

/**
 * Class responsible for showcasing the theme demo
 * in ThemeGrill Demo page.
 *
 * Class eStore_Site_Library
 */
class eStore_Site_Library {
	/**
	 * Constructor function.
	 *
	 * eStore_Site_Library constructor.
	 */
	public function __construct() {
		$this->estore_site_library_demo_packages();
	}

	/**
	 * Demo config packages
	 */
	public function estore_site_library_demo_packages() {
		$this->estore_site_library_get_demos();
	}

	/**
	 * Get the demo packages.
	 *
	 * @return array|mixed|null|object|string
	 */
	public static function estore_site_library_get_demos() {
		$template     = 'estore';
		$packages     = get_transient( 'estore_site_library_theme_' . $template );
		$raw_packages = wp_safe_remote_get( "https://raw.githubusercontent.com/themegrill/themegrill-demo-pack/master/configs/{$template}.json" );
		if ( ! is_wp_error( $raw_packages ) ) {
			$packages = json_decode( wp_remote_retrieve_body( $raw_packages ) );
			if ( $packages ) {
				set_transient( 'estore_site_library_theme_' . $template, $packages, WEEK_IN_SECONDS );
			}
		}

		return $packages;
	}

	/**
	 * Returns the content value filtered to display
	 * the demos of available themes via ThemeGrill Demo Pack
	 * config file.
	 *
	 * @return string The filtered post content.
	 */
	public static function estore_site_library_page_content() {
		$template      = 'estore';
		$output        = '';
		$demo_packages = self::estore_site_library_get_demos();
		if ( isset( $demo_packages ) ) :
			$output .= '<div id="wpbody" class="tg-demo-showcase">';
			$output .= '<div id="wpbody-content">';
			$output .= '<div class="wrap">';
			$output .= '<div class="wp-filter hide-if-no-js">';
			// Renders the theme lists.
			$output .= '<h2 class="screen-reader-text hide-if-no-js">' . esc_html__( 'Themes list', 'estore' ) . '</h2><!-- .screen-reader-text.hide-if-no-js -->';
			$output .= '<div class="theme-browser content-filterable">';
			$output .= '<div class="themes row wp-clearfix">';
			foreach ( $demo_packages->demos as $demo_package_demo => $demo_package_data ) {
				$output .= '<div class="theme col-md-4 col-sm-6 col-lg-4 ' . $demo_package_demo . '">';
				// Inner wrapper.
				$output .= '<div class="theme-inner">';
				// Displays the theme demo screenshot.
				$output .= '<div class="screenshot">';
				$output .= '<img src="' . esc_url( 'https://raw.githubusercontent.com/themegrill/themegrill-demo-pack/master/resources/' . $template . '/' . $demo_package_demo ) . '/screenshot.jpg" alt="' . esc_attr( $demo_package_demo ) . '">';
				$output .= '</div><!-- .screenshot -->';
				// Displays the pro tag.
				if ( isset( $demo_package_data->isPro ) ) {
					$output .= '<span class="premium-demo-banner">';
					$output .= esc_html__( 'Pro', 'estore' );
					$output .= '</span><!-- .premium-demo-banner -->';
				}
				// Wrap details on div.
				$output .= '<div class="theme-details">';
				// Theme id container details.
				$output .= '<div class="theme-id-container">';
				// Display the theme name.
				$output .= '<h2 class="theme-name" id="' . $demo_package_demo . '-name">';
				$output .= $demo_package_data->title;
				$output .= '</h2><!-- .theme-name -->';
				// Display the theme action buttons.
				$output .= '<div class="theme-actions">';
				// Import button.
				if ( ! isset( $demo_package_data->isPro ) ) {
					$output .= '<a class="btn-get-started button button-primary button-small preview install-demo-preview" href="#" data-name="themegrill-demo-importer" data-slug="themegrill-demo-importer" aria-label="' . esc_html__( 'Get started with eStore', 'estore' ) . '">' . esc_html__( 'Import', 'estore' ) . '</a>';
				}
				// Displays the preview now button.
				$output .= '<a class="button button-small preview install-demo-preview" href="' . esc_url( $demo_package_data->preview ) . '" target="_blank">';
				$output .= esc_html__( 'Preview', 'estore' );
				$output .= '</a>';
				// Displays the buy now button.
				if ( isset( $demo_package_data->isPro ) ) {
					$output .= '<a class="button button-small button-primary purchase-now" href="' . $demo_packages->homepage . '" target="_blank">';
					$output .= esc_html__( 'Buy Now', 'estore' );
					$output .= '</a><!-- .button.button-primary.purchase-now -->';
				}
				$output .= '</div><!-- .theme-actions -->';
				$output .= '</div><!-- .theme-id-container -->';
				$output .= '</div><!-- .theme-details -->';
				$output .= '</div><!-- .theme-inner -->';
				$output .= '</div><!-- .theme -->';
			}
			$output .= '</div><!-- .themes.wp-clearfix -->';
			$output .= '</div><!-- .theme-browser.content-filterable -->';
			$output .= '</div><!-- .wp-filter.hide-if-no-js -->';
			$output .= '</div><!-- .wrap -->';
			$output .= '</div><!-- #wpbody-content -->';
			$output .= '</div><!-- #wpbody -->';
		endif;

		return $output;
	}
}

new eStore_Site_Library();
