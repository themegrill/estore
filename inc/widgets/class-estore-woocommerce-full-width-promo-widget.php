<?php
// Full Width Promo Widget
class estore_woocommerce_full_width_promo_widget extends WP_Widget {
	function __construct() {
		$widget_ops = array(
			'classname'   => 'widget_full_width_promo widget-collection-thumb clearfix',
			'description' => esc_html__( 'Display WooCommerce Product Categories with featured image in horizontal layout.', 'estore' ) );
		$control_ops = array(
			'width'  => 200,
			'height' => 250
		);
		parent::__construct( false, $name = esc_html__( 'TG: Horizontal Promo WC Category', 'estore' ), $widget_ops, $control_ops);
	}

	function form( $instance ) {
		for ( $i=0; $i<3; $i++ ) {
			$var = 'cat_id'.$i;
			$defaults[$var] = '';
		}

		$instance = wp_parse_args( (array) $instance, $defaults );
		for ( $i=0; $i<3; $i++ ) {
			$var = 'cat_id'.$i;
			$var = absint( $instance[ $var ] );
		}

		for( $i=0; $i<3; $i++) { ?>
			<p>
				<label for="<?php echo $this->get_field_id( 'cat_id'.$i ); ?>"><?php esc_html_e( 'Category', 'estore' ); ?>:</label>
				<?php wp_dropdown_categories(
					array(
						'show_option_none' => ' ',
						'name'             => $this->get_field_name( 'cat_id'.$i ),
						'selected'         => $instance['cat_id'.$i],
						'taxonomy'         => 'product_cat'
					)
				);
				?>
			</p>
			<?php
			next( $defaults );// forwards the key of $defaults array
		}
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		for( $i=0; $i<3; $i++ ) {
			$var = 'cat_id'.$i;
			$instance[ $var] = absint( $new_instance[ $var ] );
		}

		return $instance;
	}

	function widget( $args, $instance ) {
		extract( $args );
		extract( $instance );

		global $post;
		$cat_array = array();
		for( $i=0; $i<3; $i++ ) {
			$var = 'cat_id'.$i;
			$cat_id = isset( $instance[ $var ] ) ? $instance[ $var ] : '';

			if( !empty( $cat_id ) ) {
				array_push( $cat_array, $cat_id );// Push the category id in the array
			}
		}

		$get_featured_cats = array(
			'taxonomy'     => 'product_cat',
			'orderby'      => 'date',
			'hide_empty'   => '0',
			'include'      => $cat_array
		);
		echo $before_widget; ?>
		<div class="tg-container">
			<div class="tg-column-wrapper clearfix">
				<?php
				if( !empty($cat_array) ) {
					$all_categories = get_categories( $get_featured_cats );
					$j = 1;
					foreach ($all_categories as $cat) {
						$cat_id   = $cat->term_id;
						$cat_link = get_term_link( $cat_id );
						?>
						<div class="tg-column-3 collection-thumb-block">
							<a href="<?php echo esc_url( $cat_link ); ?>">
								<figure class="collection-thumb-img">
									<?php
									$thumbnail_id = get_term_meta( $cat->term_id, 'thumbnail_id', true );
									//$image = wp_get_attachment_url( $thumbnail_id );
									$image = wp_get_attachment_image_src( $thumbnail_id, 'estore-featured-image');
									$img_altr = get_post_meta( $thumbnail_id, '_wp_attachment_image_alt', true );
									$img_alt = ! empty( $img_altr ) ? $img_altr : '';

									if ( $image[0] ) {
										echo '<img src="' . esc_url( $image[0] ) . '" alt="' .esc_attr( $img_alt ). '" />';
									}
									// @todo: Default Place holder image needed
									?>
								</figure>
								<span class="collection-thumb-hover">
							<span class="collection-thumb-title-wrapper">
								<span class="collection-thumb-title"><?php echo esc_html( $cat->name ); ?></span>
								<span class="collection-thumb-sub-title"><?php echo esc_html( $cat->description ); ?></span>
							</span>
						</span> <!-- collection-thumb-hover end -->
							</a>
						</div>
						<?php $j++; ?>
					<?php } // Foreach
					// Reset Post Data
					wp_reset_postdata();
				} // check $cat_array is empty
				?>
			</div>
		</div>
		<?php
		echo $after_widget;
	}
}
