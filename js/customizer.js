/**
 * File customizer.js.
 *
 * Theme Customizer enhancements for a better user experience.
 *
 * Contains handlers to make Theme Customizer preview reload changes asynchronously.
 */

(function ( $ ) {
	// Site title
	wp.customize( 'blogname', function ( value ) {
		value.bind( function ( to ) {
			$( '#site-title a' ).text( to );
		} );
	} );

	// Site description.
	wp.customize( 'blogdescription', function ( value ) {
		value.bind( function ( to ) {
			$( '#site-description' ).text( to );
		} );
	} );

	// Primary color option
	wp.customize( 'estore_primary_color', function ( value ) {
		value.bind( function ( primaryColor ) {
			// Store internal style for primary color
			var primaryColorStyle = '<style id="estore-internal-primary-color"> .navigation .nav-links a:hover,' +
			'.bttn:hover,button,input[type="button"]:hover,input[type="reset"]:hover,input[type="submit"]:hover,' +
			'.widget_tag_cloud a:hover,.right-top-header .top-header-menu-wrapper ul li a:hover,.right-header-block a:hover,' +
			'#lang_sel_click a.lang_sel_sel:hover,.wcmenucart-contents,.category-menu:hover,.category-menu .category-toggle.active,' +
			'.widget_shopping_cart .button:hover,.woocommerce .widget_shopping_cart_content .buttons a.button:hover,' +
			'.search-user-block:hover,.slider-caption-wrapper .slider-btn,.slider-caption-wrapper .slider-btn:hover i,' +
			'.widget-collection .page-title:after,.widget-featured-collection .page-title:after,.featured-slider li .featured-img .featured-hover-wrapper .featured-hover-block a:hover,' +
			'.widget-featured-collection .bx-controls .bx-prev:hover,.widget-featured-collection .bx-controls .bx-next:hover,' +
			'.featured-slider li .single_add_to_wishlist,.widget_featured_posts_block .entry-thumbnail .posted-on:hover,' +
			'.product-collection .page-title:after,.men-collection-color .page-title:after,.hot-product-title,' +
			'.hot-content-wrapper .single_add_to_wishlist,.widget-collection .cart-wishlist-btn a.added_to_cart:hover:after,' +
			'.entry-thumbnail .posted-on:hover,.woocommerce-page ul.products li.product .yith-wcwl-add-to-wishlist .add_to_wishlist.button.alt,' +
			'.woocommerce-page ul.products li.product .yith-wcwl-add-to-wishlist .yith-wcwl-wishlistexistsbrowse a,' +
			'.woocommerce-page ul.products li.product .yith-wcwl-add-to-wishlist .yith-wcwl-wishlistaddedbrowse a,' +
			'.single-product.woocommerce-page .product .cart .single_add_to_cart_button,' +
			'.single-product.woocommerce-page .product .yith-wcwl-add-to-wishlist .add_to_wishlist.button.alt,' +
			'.single-product.woocommerce-page .product .yith-wcwl-add-to-wishlist .yith-wcwl-wishlistexistsbrowse a,' +
			'.single-product.woocommerce-page .product .yith-wcwl-add-to-wishlist .yith-wcwl-wishlistaddedbrowse a,' +
			'.single-product.woocommerce-page .product .yith-wcwl-add-to-wishlist .single_add_to_cart_button,' +
			'.woocommerce.widget_price_filter .price_slider_wrapper .ui-widget-content .ui-slider-range,' +
			'.woocommerce.widget_price_filter .price_slider_wrapper .ui-widget-content .ui-slider-handle,' +
			'.woocommerce-cart .woocommerce table.shop_table.cart tr.cart_item td.product-remove a,' +
			'.woocommerce-cart .woocommerce table.shop_table.cart tr td.actions input[type="submit"],' +
			'.woocommerce .cart-collaterals .cart_totals .shop_table td button,.woocommerce ul.products li.product .add_to_cart_button,' +
			'.return-to-shop a.button,.woocommerce #content .wishlist_table tbody tr td.product-remove a.remove_from_wishlist,' +
			'.woocommerce #content .wishlist_table tbody tr td.product-add-to-cart a,.woocommerce #respond input#submit,' +
			'.woocommerce a.button,.woocommerce button.button,.woocommerce input.button,.woocommerce #respond input#submit.alt,' +
			'.woocommerce a.button.alt,.woocommerce button.button.alt,.woocommerce input.button.alt,.sub-toggle,.scrollup  { background: ' + primaryColor + '; }' +
			'a,.widget_archive a:hover::before,.widget_categories a:hover:before,.widget_pages a:hover:before,.widget_meta a:hover:before,' +
			'.widget_recent_comments a:hover:before,.widget_recent_entries a:hover:before,.widget_rss a:hover:before,.widget_nav_menu a:hover:before,' +
			'.widget_product_categories li a:hover:before,.widget_archive li a:hover,.widget_categories li a:hover,.widget_pages li a:hover,' +
			'.widget_meta li a:hover,.widget_recent_comments li a:hover,.widget_recent_entries li a:hover,.widget_rss li a:hover,' +
			'.widget_nav_menu li a:hover,.widget_tag_cloud a:hover,.widget_product_categories a:hover,.wcmenucart-contents .cart-value,' +
			'#site-navigation ul li:hover > a,#site-navigation ul li.current-menu-item > a,#site-navigation ul li:hover > a:after,.slider-caption-wrapper .slider-title a:hover,' +
			'.widget_vertical_promo .slider-title a:hover,.hot-content-wrapper .star-rating,.product-list-wrap .product-list-block .product-list-content .price ins,.widget-collection .cart-wishlist-btn a i,' +
			'.widget-collection .cart-wishlist-btn a.added_to_cart:after,.widget-about .tg-container .about-content-wrapper .about-block .about-sub-title,.featured-slider li .featured-title a,' +
			'.featured-slider li .woocommerce-product-rating .star-rating,.featured-slider li .price ins,.page-header .entry-title,.entry-title a:hover,' +
			'.entry-btn .btn:hover,.entry-meta a:hover,.woocommerce-page ul.products li.product .star-rating,.woocommerce-page ul.products li.product .price ins,' +
			'.woocommerce-page ul.products li.product .yith-wcwl-add-to-wishlist .feedback,.single-product.woocommerce-page .product .summary .price,' +
			'.single-product.woocommerce-page .product .woocommerce-product-rating .star-rating,.widget.woocommerce .star-rating,' +
			'.cart-empty,.woocommerce .woocommerce-info:before,.woocommerce .woocommerce-error:before,.woocommerce .woocommerce-message:before,' +
			'.toggle-wrap:hover i,#cancel-comment-reply-link,#cancel-comment-reply-link:before,.logged-in-as a { color: ' + primaryColor + '; }' +
			'.widget-title span,#lang_sel_click ul ul,.wcmenucart-contents .cart-value,#category-navigation,' +
			'#category-navigation ul.sub-menu,#masthead .widget_shopping_cart,.widget_shopping_cart .button:hover,.woocommerce .widget_shopping_cart_content .buttons a.button:hover,' +
			'#site-navigation .sub-menu,.search-wrapper .header-search-box,.hot-product-content-wrapper .hot-img,.widget-collection .cart-wishlist-btn a i,' +
			'.widget-collection .cart-wishlist-btn a.added_to_cart:after,.featured-slider li .featured-img .featured-hover-wrapper .featured-hover-block a:hover,' +
			'.widget-featured-collection .bx-controls .bx-prev:hover,.widget-featured-collection .bx-controls .bx-next:hover,.single-product.woocommerce-page .product .images .thumbnails a,' +
			'.woocommerce .woocommerce-info,.woocommerce .woocommerce-error,.woocommerce .woocommerce-message,.menu-primary-container,' +
			'.comment-list .comment-body{ border-color: ' + primaryColor + '; }' +
			'.search-wrapper .header-search-box:before,#masthead .widget_shopping_cart::before{ border-bottom-color:' + primaryColor + '; }' +
			'.big-slider .bx-controls .bx-prev:hover,.category-slider .bx-controls .bx-prev:hover{ border-left-color:' + primaryColor + '; }' +
			'.big-slider .bx-controls .bx-next:hover,' +
			'.category-slider .bx-controls .bx-next:hover{ border-right-color:' + primaryColor + '; }' +
			'#primary-menu{ border-top-color:' + primaryColor + '; } </style>';

			// Remove previously create internal style and add new one.
			$( 'head #estore-internal-primary-color' ).remove();
			$( 'head' ).append( primaryColorStyle );
		}
		);
	} );
})( jQuery );