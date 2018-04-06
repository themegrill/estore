<?php
/**
 * eStore functionality
 *
 * @package ThemeGrill
 * @subpackage estore
 * @since eStore 1.2.5
 */

/**
 * Header search form
 */
function estore_header_search_box() {
	$search_form = get_theme_mod( 'estore_header_search_option', 'wp_search' );
	?>
	<div class="search-wrapper search-user-block">
		<div class="search-icon">
			<i class="fa fa-search"> </i>
		</div>
		<div class="header-search-box">
			<?php
			// if WC is activated and WC Search is selected in customizer
			if ( function_exists( 'get_product_search_form' ) && $search_form === 'wc_search' ) :
				get_product_search_form();
			else :
				get_search_form();
			endif;
			?>
		</div>
	</div>
	<?php
}
