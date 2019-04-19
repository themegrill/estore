<?php
// Vertical Promo Widget
class estore_woocommerce_vertical_promo_widget extends WP_Widget {
	function __construct() {
		$widget_ops = array(
			'classname'   => 'widget_vertical_promo collection-wrapper clearfix',
			'description' => esc_html__( 'Display WooCommerce Product Categories in vertical layout with featured image.', 'estore' ) );
		$control_ops = array(
			'width'  => 200,
			'height' => 250
		);
		parent::__construct( false, $name = esc_html__( 'TG: Vertical Promo WC Category', 'estore' ), $widget_ops, $control_ops);
	}

	function form( $instance ) {
		for ( $i=0; $i<2; $i++ ) {
			$var = 'cat_id'.$i;
			$defaults[$var] = '';
		}

		$instance = wp_parse_args( (array) $instance, $defaults );
		for ( $i=0; $i<2; $i++ ) {
			$var = 'cat_id'.$i;
			$var = absint( $instance[ $var ] );
		}

		for( $i=0; $i<2; $i++) { ?>
			<p>
				<label for="<?php echo $this->get_field_id( key($defaults) ); ?>"><?php esc_html_e( 'Category', 'estore' ); ?>:</label>
				<?php wp_dropdown_categories(
					array(
						'show_option_none' => ' ',
						'name'             => $this->get_field_name( key($defaults) ),
						'selected'         => $instance[key($defaults)],
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
		for( $i=0; $i<2; $i++ ) {
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
		for( $i=0; $i<2; $i++ ) {
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
		<?php
		if( !empty($cat_array) ) {
			$all_categories = get_categories( $get_featured_cats );
			$j = 1;
			foreach ($all_categories as $cat) {
				$cat_id   = $cat->term_id;
				$cat_link = get_term_link( $cat_id );
				?>
				<div class="collection-block">
					<figure class="slider-collection-img">
						<?php
						$thumbnail_id = get_term_meta( $cat->term_id, 'thumbnail_id', true );
						//$image = wp_get_attachment_url( $thumbnail_id );
						$image           = wp_get_attachment_image_src( $thumbnail_id, 'estore-featured-image' );
						$title_attribute = esc_attr( get_the_title( $thumbnail_id ) );
						$img_altr        = get_post_meta( $thumbnail_id, '_wp_attachment_image_alt', true );
						$img_alt         = ! empty( $img_altr ) ? $img_altr : $title_attribute;
						if ( $image[0] ) {
							echo '<img src="' . esc_url( $image[0] ) . '" alt="' .$img_alt. '" />';
						}
						// @todo: Default Place holder image needed
						?>
					</figure>
					<h3 class="slider-title"><a href="<?php echo esc_url( $cat_link ); ?>"><?php echo esc_html( $cat->name ); ?></a></h3>
				</div>
				<?php $j++; ?>
			<?php } //foreach
			// Reset Post Data
			wp_reset_postdata();
		} // cat array check
		?>
		<?php
		echo $after_widget;
	}
}
