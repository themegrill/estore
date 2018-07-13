<?php
// Estore WooCommerce Product Grid Widget
class estore_woocommerce_product_grid extends WP_Widget {
	function __construct() {
		$widget_ops = array(
			'classname'   => 'widget-collection clearfix',
			'description' => esc_html__( 'Show WooCommerce Featured Products Grid.', 'estore' )
		);
		$control_ops = array(
			'width'  => 200,
			'height' => 250
		);
		parent::__construct( false, $name = esc_html__( 'TG: Product Grid', 'estore' ), $widget_ops, $control_ops);
	}

	function form( $instance ) {
		$defaults[ 'title' ]            = '';
		$defaults[ 'subtitle' ]         = '';
		$defaults[ 'category']          = '';
		$defaults[ 'product_number' ]   = 10;
		$defaults[ 'cat_image_url' ]    = '';
		$defaults[ 'cat_image_link' ]   = '';
		$defaults[ 'align' ]            = 'collection-left-align';

		$instance = wp_parse_args( (array) $instance, $defaults );

		$title            = esc_attr( $instance[ 'title' ] );
		$subtitle         = esc_textarea( $instance[ 'subtitle' ] );
		$cat_image_url    = esc_url( $instance[ 'cat_image_url' ] );
		$cat_image_link   = esc_url( $instance[ 'cat_image_link' ] );
		$align            = $instance[ 'align' ];
		$category         = absint( $instance[ 'category' ] );
		$product_number   = absint( $instance[ 'product_number' ] );
		?>

		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php esc_html_e( 'Title:', 'estore' ); ?></label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" />
		</p>

		<?php esc_html_e( 'Description:','estore' ); ?>
		<textarea class="widefat" rows="5" cols="20" id="<?php echo $this->get_field_id( 'subtitle' ); ?>" name="<?php echo $this->get_field_name('subtitle'); ?>"><?php echo $subtitle; ?></textarea>

		<label><?php esc_html_e( 'Add your Category Image here.', 'estore' ); ?></label>

		<p>
			<label for="<?php echo $this->get_field_id( 'cat_image_link' ); ?>"> <?php esc_html_e( 'Image Link', 'estore' ); ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'cat_image_link' ); ?>" name="<?php echo $this->get_field_name( 'cat_image_link' ); ?>" value="<?php echo $instance[ 'cat_image_link' ]; ?>"/>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'cat_image_url' ); ?>"> <?php esc_html_e( 'Category Image', 'estore' ); ?></label>
		<div class="media-uploader" id="<?php echo $this->get_field_id( 'cat_image_url' ); ?>">
			<div class="custom_media_preview">
				<?php if ( $instance[ 'cat_image_url' ] != '' ) : ?>
					<img class="custom_media_preview_default" src="<?php echo esc_url( $instance[ 'cat_image_url' ] ); ?>" style="max-width:100%;" />
				<?php endif; ?>
			</div>
			<input type="text" class="widefat custom_media_input" id="<?php echo $this->get_field_id( 'cat_image_url' ); ?>" name="<?php echo $this->get_field_name( 'cat_image_url' ); ?>" value="<?php echo esc_url( $instance['cat_image_url'] ); ?>" style="margin-top:5px;" />
			<button class="custom_media_upload button button-secondary button-large" id="<?php echo $this->get_field_id( 'cat_image_url' ); ?>" data-choose="<?php esc_attr_e( 'Choose an image', 'estore' ); ?>" data-update="<?php esc_attr_e( 'Use image', 'estore' ); ?>" style="width:100%;margin-top:6px;margin-right:30px;"><?php esc_html_e( 'Select an Image', 'estore' ); ?></button>
		</div>
		</p>

		<label><?php esc_html_e( 'Choose where to align your image.', 'estore' ); ?></label>

		<p>
			<input type="radio" <?php checked( $align, 'collection-right-align' ) ?> id="<?php echo $this->get_field_id( 'align' ); ?>" name="<?php echo $this->get_field_name( 'align' ); ?>" value="collection-right-align" /><?php esc_html_e( 'Right Align', 'estore' );?><br />
			<input type="radio" <?php checked( $align,'collection-left-align' ) ?> id="<?php echo $this->get_field_id( 'align' ); ?>" name="<?php echo $this->get_field_name( 'align' ); ?>" value="collection-left-align" /><?php esc_html_e( 'Left Align', 'estore' );?><br />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'category' ); ?>"><?php esc_html_e( 'Category:', 'estore' ); ?></label>
			<?php
			wp_dropdown_categories(
				array(
					'show_option_none' => '',
					'name'             => $this->get_field_name( 'category' ),
					'selected'         => $instance['category'],
					'taxonomy'         => 'product_cat'
				)
			);
			?>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'product_number' ); ?>"><?php esc_html_e( 'Number of Products:', 'estore' ); ?></label>
			<input id="<?php echo $this->get_field_id( 'product_number' ); ?>" name="<?php echo $this->get_field_name( 'product_number' ); ?>" type="number" value="<?php echo $product_number; ?>" />
		</p>
		<?php
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance[ 'title' ]          = sanitize_text_field( $new_instance[ 'title' ] );
		if ( current_user_can('unfiltered_html') )
			$instance[ 'subtitle' ] =  $new_instance[ 'subtitle' ];
		else
			$instance[ 'subtitle' ] = stripslashes( wp_filter_post_kses( addslashes( $new_instance[ 'subtitle' ] ) ) );
		$instance[ 'category' ]       = absint( $new_instance[ 'category' ] );

		$instance[ 'product_number' ] = absint( $new_instance[ 'product_number' ] );

		$instance[ 'cat_image_link' ] = esc_url_raw( $new_instance[ 'cat_image_link' ] );
		$instance[ 'cat_image_url' ]  = esc_url_raw( $new_instance[ 'cat_image_url' ] );
		$instance[ 'align' ]          = $new_instance[ 'align' ];

		return $instance;
	}

	function widget( $args, $instance ) {
		extract( $args );
		extract( $instance );

		global $post;
		$title            = apply_filters( 'widget_title', isset( $instance[ 'title' ] ) ? $instance[ 'title' ] : '');
		$subtitle         = isset( $instance[ 'subtitle' ] ) ? $instance[ 'subtitle' ] : '';

		$cat_image_link   = isset( $instance[ 'cat_image_link' ] ) ? $instance[ 'cat_image_link' ] : '';
		$cat_image_url    = isset( $instance[ 'cat_image_url' ] ) ? $instance[ 'cat_image_url' ] : '';
		$align            = isset( $instance[ 'align' ] ) ? $instance[ 'align' ] : 'collection-left-align' ;

		$category         = isset( $instance[ 'category' ] ) ? $instance[ 'category' ] : '';
		$product_number   = isset( $instance[ 'product_number' ] ) ? $instance[ 'product_number' ] : '';

		// For Multilingual compatibility
		if ( function_exists( 'icl_register_string' ) ) {
			icl_register_string( 'eStore', 'TG: Product Grid Subtitle' . $this->id, $subtitle );
			icl_register_string( 'eStore', 'TG: Product Grid Image' . $this->id, $cat_image_url );
			icl_register_string( 'eStore', 'TG: Product Grid Image Link' . $this->id, $cat_image_link );
		}
		if ( function_exists( 'icl_t' ) ) {
			$subtitle       = icl_t( 'eStore', 'TG: Product Grid Subtitle'. $this->id, $subtitle );
			$cat_image_url  = icl_t( 'eStore', 'TG: Product Grid Image'. $this->id, $cat_image_url );
			$cat_image_link = icl_t( 'eStore', 'TG: Product Grid Image Link'. $this->id, $cat_image_link );
		}

		$args = array(
			'post_type' => 'product',
			'orderby'   => 'date',
			'tax_query' => array(
				array(
					'taxonomy'  => 'product_cat',
					'field'     => 'id',
					'terms'     => $category
				)
			),
			'posts_per_page' => $product_number
		);

		/**
		 *  Hide out of stock items from the catalog
		 *
		 * @see https://docs.woocommerce.com/document/configuring-woocommerce-settings/#inventory-options
		 */
		$stock_invisibility = get_option( 'woocommerce_hide_out_of_stock_items' );

		if ( $stock_invisibility === 'yes' ) {
			$args[ 'meta_key' ] = '_stock_status';
			$args[ 'meta_value' ] = 'instock';
		}

		echo $before_widget; ?>
		<div class="tg-container estore-cat-color_<?php echo $category; ?> <?php echo $align; ?>">
			<div class="section-title-wrapper clearfix">
				<div class="section-title-block">
					<?php if ( !empty( $title ) ) { ?>
						<h3 class="page-title"><a href="<?php echo esc_url( get_term_link( $category ) ); ?>"><?php echo esc_html( $title ); ?></a></h3>
					<?php }
					if ( !empty( $subtitle ) ) { ?>
						<h4 class="page-sub-title"><?php echo esc_textarea( $subtitle );?></h4>
					<?php } ?>
				</div>
				<div class="sorting-form-wrapper">
					<a href="<?php echo esc_url( get_term_link( $category ) ); ?>"><?php esc_html_e( 'View all', 'estore' ); ?></a>
				</div>
			</div>
			<div class="collection-block-wrapper tg-column-wrapper clearfix">
				<div class="tg-column-4 collection-block">
					<?php
					$output = '';
					if ( !empty( $cat_image_url ) ) {
						if ( !empty( $cat_image_link ) ) {
							$output .= '<a href="'.esc_url($cat_image_link).'" target="_blank" rel="nofollow">
											<img src="'.esc_url($cat_image_url).'" alt="'.esc_html($title).'" />
										</a>';
						} else {
							$output .= '<img src="'.esc_url($cat_image_url).'" alt="'.esc_html($title).'" />';
						}
						echo $output;
					} ?>
				</div>
				<?php
				$count = 1;
				$featured_query = new WP_Query( $args );
				while ($featured_query->have_posts()) :
					$featured_query->the_post();
					$product = wc_get_product( $featured_query->post->ID );
					if($count == 1){ ?>
						<div class="tg-column-4 collection-block">
							<div class="hot-product-block">
								<h3 class="hot-product-title"><?php esc_html_e( 'Hot products', 'estore' ); ?></h3>
								<div class="hot-product-content-wrapper clearfix">
									<?php
									$image_id = get_post_thumbnail_id();
									$image_url = wp_get_attachment_image_src($image_id,'estore-medium-image', false); ?>
									<figure class="hot-img">
										<a href="<?php the_permalink(); ?>">
											<?php if($image_url) { ?>
												<img src="<?php echo esc_url( $image_url[0] ); ?>" alt="<?php the_title_attribute(); ?>">
											<?php } else { ?>
												<img src="<?php echo get_template_directory_uri() . '/images/placeholder-shop-380x250.jpg'; ?>" alt="<?php the_title_attribute(); ?>" width="250" height="180" >
											<?php } ?>
										</a>
										<div class="cart-price-wrapper clearfix">
											<?php woocommerce_template_loop_add_to_cart( $product ); ?>
											<?php if ( $price_html = $product->get_price_html() ) : ?>
												<span class="hot-price price"><?php echo $price_html; ?></span>
											<?php endif; ?>
										</div>
										<?php if ( $product->is_on_sale() ) : ?>
											<?php echo apply_filters( 'woocommerce_sale_flash', '<div class="sales-tag">' . esc_html__( 'Sale!', 'estore' ) . '</div>', $post, $product ); ?>
										<?php endif; ?>
									</figure>
									<div class="hot-content-wrapper">
										<h3 class="hot-title"> <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
										<div class="hot-content"><?php the_excerpt(); ?></div>
										<!-- Rating products -->
										<div class="woocommerce-product-rating woocommerce">
											<?php if ( $rating_html = wc_get_rating_html( $product->get_average_rating() ) ) { ?>
												<?php echo $rating_html; ?>
											<?php } else {
												echo '<div class="star-rating"></div>' ;
											}?>
										</div>
										<?php
										if( function_exists( 'YITH_WCWL' ) ){
											$url = add_query_arg( 'add_to_wishlist', $product->get_id() );
											?>
											<a href="<?php echo esc_url($url); ?>" class="single_add_to_wishlist" ><?php esc_html_e('Add to Wishlist','estore'); ?><i class="fa fa-heart"></i></a>
										<?php } ?>
									</div> <!-- hot-content-wrapper end -->
								</div> <!-- hot-product-content-wrapper end -->
							</div> <!-- hot product block end -->
						</div>
					<?php }

					if($count == 2 || $count == 7){ ?>
						<div class="tg-column-4 collection-block">
						<div class="product-list-wrap">
					<?php }
					if($count > 1 && $count < 7 || $count > 6) { ?>
						<div class="product-list-block clearfix">
							<?php
							$image_id = get_post_thumbnail_id();
							$image_url = wp_get_attachment_image_src($image_id,'estore-product-grid', false); ?>
							<figure class="product-list-img">
								<a href="<?php the_permalink(); ?>">
									<?php if($image_url[0]) { ?>
										<img src="<?php echo esc_url( $image_url[0] ); ?>" alt="<?php the_title_attribute(); ?>">
									<?php } else { ?>
										<img src="<?php echo estore_woocommerce_placeholder_img_src(); ?>" alt="<?php the_title_attribute(); ?>" width="75" height="75">
									<?php } ?>
								</a>
							</figure>
							<div class="product-list-content">
								<h3 class="product-list-title"> <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a> </h3>
								<?php if ( $price_html = $product->get_price_html() ) : ?>
									<span class="price"><span class="price-text"><?php esc_html_e('Price: ', 'estore'); ?></span><?php echo $price_html; ?></span>
								<?php endif; ?>
								<div class="cart-wishlist-btn">
									<?php
									if( function_exists( 'YITH_WCWL' ) ){
										$url = add_query_arg( 'add_to_wishlist', $product->get_id() );
										?>
										<a href="<?php echo esc_url($url); ?>" class="single_add_to_wishlist" ><?php esc_html_e('Add to Wishlist','estore'); ?><i class="fa fa-heart"></i></a>
									<?php }
									woocommerce_template_loop_add_to_cart( $product );
									?>

								</div> <!-- cart-wishlist-btn end -->
							</div>
						</div>
					<?php } // Closing div for columns
					if($count == 6){ ?>
						</div>
						</div>
					<?php }
					$count++;
				endwhile;
				wp_reset_postdata();
				?>
			</div><!-- collection-block-wrapper tg-column-wrapper clearfix -->
		</div>
		<?php
		echo $after_widget;
	}
}

