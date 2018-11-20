<?php
// WooCommerce Product Slider Widget
class estore_woocommerce_product_slider extends WP_Widget {

	function __construct() {
		$widget_ops = array(
			'classname'   => 'slider_wrapper product-slider clearfix',
			'description' => esc_html__( 'Display WooCommerce Product from category.', 'estore' ) );

		$control_ops = array(
			'width'  => 200,
			'height' => 250
		);

		parent::__construct( false,$name = esc_html__( 'TG: Product Slider', 'estore' ), $widget_ops);
	}

	function form( $instance ) {
		$tg_defaults['number']   = 4;
		$tg_defaults['type']     = 'latest';
		$tg_defaults['category'] = '';

		$instance = wp_parse_args( (array) $instance, $tg_defaults );

		$number   = $instance['number'];
		$type     = $instance['type'];
		$category = $instance['category'];
		?>

		<p>
			<label for="<?php echo $this->get_field_id('number'); ?>"><?php esc_html_e( 'Number of products to display:', 'estore' ); ?></label>
			<input id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="text" value="<?php echo $number; ?>" size="3" />
		</p>

		<p>
			<input type="radio" <?php checked($type, 'latest') ?> id="<?php echo $this->get_field_id( 'type' ); ?>" name="<?php echo $this->get_field_name( 'type' ); ?>" value="latest"/><?php esc_html_e( 'Show Latest Products', 'estore' );?><br />
			<input type="radio" <?php checked($type,'category') ?> id="<?php echo $this->get_field_id( 'type' ); ?>" name="<?php echo $this->get_field_name( 'type' ); ?>" value="category"/><?php esc_html_e( 'Show Products from a category', 'estore' );?><br />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'category' ); ?>"><?php esc_html_e( 'Select category', 'estore' ); ?>:</label>
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

		<?php
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		$instance[ 'number' ]   = absint( $new_instance[ 'number' ] );
		$instance[ 'type' ]     = $new_instance[ 'type' ];
		$instance[ 'category' ] = $new_instance[ 'category' ];

		return $instance;
	}

	function widget( $args, $instance ) {
		extract( $args );
		extract( $instance );

		global $post;
		$number   = empty( $instance[ 'number' ] ) ? 4 : $instance[ 'number' ];
		$type     = isset( $instance[ 'type' ] ) ? $instance[ 'type' ] : 'latest' ;
		$category = isset( $instance[ 'category' ] ) ? $instance[ 'category' ] : '';

		if( $type == 'latest' ) {
			$args = array(
				'posts_per_page'        => $number,
				'post_type'             => 'product',
				'ignore_sticky_posts'   => true
			);
		}
		else {
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
				'posts_per_page' => $number
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

		$get_featured_posts = new WP_Query( $args );


		echo $before_widget;
		?>

		<ul class="home-slider">
			<?php
			while( $get_featured_posts->have_posts() ):
				$get_featured_posts->the_post();
				if( has_post_thumbnail() ) {
					?>
					<li>
						<?php
						$image_id        = get_post_thumbnail_id();
						$image_url       = wp_get_attachment_image_src( $image_id, 'estore-slider', false );
						$title_attribute = esc_attr( get_the_title( $post->ID ) );
						$img_altr        = get_post_meta( $image_id, '_wp_attachment_image_alt', true );
						$img_alt         = ! empty( $img_altr ) ? $img_altr : $title_attribute; ?>
						<img src="<?php echo esc_url( $image_url[0] ); ?>" alt="<?php echo esc_attr( $img_alt ); ?>">
						<div class="slider-caption-wrapper">
							<h3 class="slider-title"> <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a> </h3>
							<div class="slider-content"><?php the_excerpt(); ?></div>
							<a href="<?php the_permalink(); ?>" class="slider-btn"><?php esc_html_e( 'Add to Cart', 'estore' ); ?><i class="fa fa-shopping-cart"></i></a>
						</div>
					</li>
					<?php
				}
			endwhile;
			// Reset Post Data
			wp_reset_postdata();
			?>
		</ul>
		<?php echo $after_widget;
	}
}

