<?php
// Estore WooCommerce Product Carousel Widget
class estore_woocommerce_product_carousel extends WP_Widget {
	function __construct() {
		$widget_ops = array(
			'classname'   => 'widget-featured-collection featured-collection-color clearfix',
			'description' => esc_html__( 'Show WooCommerce Featured Products Carousel.', 'estore' )
		);
		$control_ops = array(
			'width'  => 200,
			'height' => 250
		);
		parent::__construct( false, $name = esc_html__( 'TG: Products Carousel', 'estore' ), $widget_ops, $control_ops);
	}

	function form( $instance ) {
		$defaults[ 'title' ]            = '';
		$defaults[ 'subtitle' ]         = '';
		$defaults[ 'source' ]           = '';
		$defaults[ 'category' ]         = '';
		$defaults[ 'product_number' ]   = 10;
		$defaults[ 'hide_thumbnail_mask' ]   = 0;

		$instance = wp_parse_args( (array) $instance, $defaults );

		$title            = esc_attr( $instance[ 'title' ] );
		$subtitle         = esc_textarea( $instance[ 'subtitle' ] );
		$source           = $instance[ 'source' ];
		$category         = absint( $instance[ 'category' ] );
		$product_number   = absint( $instance[ 'product_number' ] );
		$hide_thumbnail_mask = $instance[ 'hide_thumbnail_mask' ] ? 'checked="checked"' : '';
		?>

		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php esc_html_e( 'Title:', 'estore' ); ?></label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" />
		</p>

		<?php esc_html_e( 'Description:','estore' ); ?>
		<textarea class="widefat" rows="5" cols="20" id="<?php echo $this->get_field_id( 'subtitle' ); ?>" name="<?php echo $this->get_field_name('subtitle'); ?>"><?php echo $subtitle; ?></textarea>

		<p>
			<label for="<?php echo $this->get_field_id( 'source' ); ?>"><?php esc_html_e( 'Product Source:', 'estore' ); ?></label>
			<select id="<?php echo $this->get_field_id( 'source' ); ?>" name="<?php echo $this->get_field_name( 'source' ); ?>">
				<option value="latest" <?php selected( $instance['source'], 'latest'); ?>><?php esc_html_e( 'Latest Products', 'estore' ); ?></option>
				<option value="featured" <?php selected( $instance['source'], 'featured'); ?>><?php esc_html_e( 'Featured Products', 'estore' ); ?></option>
				<option value="sale" <?php selected( $instance['source'], 'sale'); ?>><?php esc_html_e( 'On Sale Products', 'estore' ); ?></option>
				<option value="category" <?php selected( $instance['source'], 'category'); ?>><?php esc_html_e( 'Certain Category', 'estore' ); ?></option>
			</select>
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
		<p>
			<input class="checkbox" <?php echo $hide_thumbnail_mask; ?> id="<?php echo $this->get_field_id( 'hide_thumbnail_mask' ); ?>" name="<?php echo $this->get_field_name( 'hide_thumbnail_mask' ); ?>" type="checkbox" />
			<label for="<?php echo $this->get_field_id('hide_thumbnail_mask'); ?>"><?php esc_html_e( 'Check to hide image hover effect.', 'estore' ); ?></label>
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
		$instance[ 'source' ]         = $new_instance[ 'source' ];
		$instance[ 'category' ]       = absint( $new_instance[ 'category' ] );
		$instance[ 'product_number' ] = absint( $new_instance[ 'product_number' ] );
		$instance[ 'hide_thumbnail_mask' ]   = isset( $new_instance[ 'hide_thumbnail_mask' ] ) ? 1 : 0;

		return $instance;
	}

	function widget( $args, $instance ) {
		extract( $args );
		extract( $instance );

		global $post;
		$title            = apply_filters( 'widget_title', isset( $instance[ 'title' ] ) ? $instance[ 'title' ] : '');
		$subtitle         = isset( $instance[ 'subtitle' ] ) ? $instance[ 'subtitle' ] : '';
		$source           = isset( $instance[ 'source' ] ) ? $instance[ 'source' ] : '';
		$category         = isset( $instance[ 'category' ] ) ? $instance[ 'category' ] : '';
		$product_number   = isset( $instance[ 'product_number' ] ) ? $instance[ 'product_number' ] : '';
		$hide_thumbnail_mask = isset( $instance[ 'hide_thumbnail_mask' ] ) ? $instance[ 'hide_thumbnail_mask' ] : 0;

		// For Multilingual compatibility
		if ( function_exists( 'icl_register_string' ) ) {
			icl_register_string( 'eStore', 'TG: Product Carousel Subtitle' . $this->id, $subtitle );
		}
		if ( function_exists( 'icl_t' ) ) {
			$subtitle  = icl_t( 'eStore', 'TG: Product Carousel Subtitle'. $this->id, $subtitle );
		}

		if ( $source == 'featured' ) {
			$args = array(
				'post_type'        => 'product',
				'posts_per_page'   => $product_number,
				'tax_query' => array(
					array(
						'taxonomy' => 'product_visibility',
						'field'    => 'name',
						'terms'    => 'featured',
					),
				)
			);
		} elseif ( $source == 'sale' ) {
			$args = array(
				'post_type'      => 'product',
				'meta_query'     => array(
					'relation' => 'OR',
					array( // Simple products type
					       'key'           => '_sale_price',
					       'value'         => 0,
					       'compare'       => '>',
					       'type'          => 'numeric'
					),
					array( // Variable products type
					       'key'           => '_min_variation_sale_price',
					       'value'         => 0,
					       'compare'       => '>',
					       'type'          => 'numeric'
					)
				),
				'posts_per_page'   => $product_number
			);
		} elseif ( $source == 'category' ){
			$args = array(
				'post_type' => 'product',
				'tax_query' => array(
					array(
						'taxonomy'  => 'product_cat',
						'field'     => 'id',
						'terms'     => $category
					)
				),
				'posts_per_page' => $product_number
			);
		} else {
			$args = array(
				'post_type' => 'product',
				'posts_per_page' => $product_number
			);
		}

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
		<div class="tg-container">
			<div class="section-title-wrapper clearfix">
				<div class="section-title-block">
					<?php if ( !empty( $title ) ) { ?>
						<h3 class="page-title"><?php echo esc_html( $title ); ?></h3>
					<?php }
					if ( !empty( $subtitle ) ) { ?>
						<h4 class="page-sub-title"><?php echo esc_textarea( $subtitle );?></h4>
					<?php } ?>
				</div>
			</div>
			<div class="featured-wrapper clearfix">
				<ul class="featured-slider">
					<?php
					$featured_query = new WP_Query( $args );
					while ($featured_query->have_posts()) :
						$featured_query->the_post();
						$product = wc_get_product( $featured_query->post->ID ); ?>
						<li>
							<?php
							$image_id = get_post_thumbnail_id();
							$image_url = wp_get_attachment_image_src($image_id,'estore-square', false); ?>
							<figure class="featured-img">
								<?php if($image_url[0]) { ?>
									<a href="<?php echo esc_url( get_permalink( $product->get_id() ) ); ?>" alt="<?php the_title(); ?>"><img src="<?php echo esc_url( $image_url[0] ); ?>" alt="<?php the_title_attribute(); ?>"></a>
								<?php } else { ?>
									<a href="<?php echo esc_url( get_permalink( $product->get_id() ) ); ?>" alt="<?php the_title(); ?>"><img src="<?php echo estore_woocommerce_placeholder_img_src(); ?>" alt="<?php the_title_attribute(); ?>"></a>
								<?php } ?>
								<?php if ( $product->is_on_sale() ) : ?>
									<?php echo apply_filters( 'woocommerce_sale_flash', '<div class="sales-tag">' . esc_html__( 'Sale!', 'estore' ) . '</div>', $post, $product ); ?>
								<?php endif; ?>

								<?php if ( $hide_thumbnail_mask != 1 ) : ?>
									<div class="featured-hover-wrapper">
										<div class="featured-hover-block">
											<?php if($image_url[0]) { ?>
												<a href="<?php echo esc_url( $image_url[0] ); ?>" class="zoom" data-rel="prettyPhoto"><i class="fa fa-search-plus"> </i></a>
											<?php } else {?>
												<a href="<?php echo estore_woocommerce_placeholder_img_src(); ?>"  class="zoom" data-rel="prettyPhoto"><i class="fa fa-search-plus"> </i></a>
											<?php }
											woocommerce_template_loop_add_to_cart( $product ); ?>
										</div>
									</div><!-- featured hover end -->
								<?php endif; ?>
							</figure>
							<div class="featured-content-wrapper">
								<h3 class="featured-title"> <a title="<?php the_title_attribute(); ?>" href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
								<div class="woocommerce-product-rating woocommerce"> <?php
									if ( $rating_html = wc_get_rating_html( $product->get_average_rating() ) ) { ?>
										<?php echo $rating_html; ?>
									<?php } else {
										echo '<div class="star-rating"></div>' ;
									}?>
								</div>
								<?php if ( $price_html = $product->get_price_html() ) : ?>
									<span class="price"><span class="price-text"><?php esc_html_e('Price:', 'estore'); ?></span><?php echo $price_html; ?></span>
								<?php endif; ?>

								<?php
								if( function_exists( 'YITH_WCWL' ) ){
									$url = add_query_arg( 'add_to_wishlist', $product->get_id() );
									?>
									<a href="<?php echo esc_url($url); ?>" class="single_add_to_wishlist" ><?php esc_html_e('Add to Wishlist','estore'); ?><i class="fa fa-heart"></i></a>
								<?php } ?>
							</div><!-- featured content wrapper -->
						</li>
					<?php
					endwhile;
					?>
				</ul>
			</div>
		</div>

		<?php wp_reset_postdata(); ?>
		<?php
		echo $after_widget;
	}
}
