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

/**
 * estore_product_searchform
 * Overrides default WC search form
 *
 * @since 1.2.5
 */
function estore_product_searchform ( $form ) {

	$form = '<form role="search" method="get" class="estore-wc-product-search" action=" '. esc_url( home_url( '/' ) ) . '">
		<label class="screen-reader-text" for="estore-wc-search-field">' . esc_html__( 'Search for:', 'estore' ) . '</label>
		<input type="search" id="estore-wc-search-field" class="search-field" placeholder="'. esc_attr__( 'Search products ...', 'estore' ) . '" value="' . get_search_query() . '" name="s" />
		<button type="submit" class="searchsubmit" value="' . esc_attr_x( 'Search', 'submit button', 'estore' ) . '">
			<i class="fa fa-search"></i>
		</button>
		<input type="hidden" name="post_type" value="product" />
	</form>';

	return $form;

}
add_filter( 'get_product_search_form' , 'estore_product_searchform', 10, 1 );
