<?php
// Vertical Promo Widget
class estore_vertical_promo_widget extends WP_Widget {
	function __construct() {
		$widget_ops = array(
			'classname'   => 'widget_vertical_promo collection-wrapper clearfix',
			'description' => esc_html__( 'Display some pages in vertical layout with featured image.', 'estore' ) );
		$control_ops = array(
			'width'  => 200,
			'height' => 250
		);
		parent::__construct( false, $name = esc_html__( 'TG: Vertical Promo', 'estore' ), $widget_ops, $control_ops);
	}

	function form( $instance ) {
		for ( $i=0; $i<2; $i++ ) {
			$var = 'page_id'.$i;
			$defaults[$var] = '';
		}

		$instance = wp_parse_args( (array) $instance, $defaults );
		for ( $i=0; $i<2; $i++ ) {
			$var = 'page_id'.$i;
			$var = absint( $instance[ $var ] );
		}

		for( $i=0; $i<2; $i++) { ?>
			<p>
				<label for="<?php echo $this->get_field_id( key($defaults) ); ?>"><?php esc_html_e( 'Pages', 'estore' ); ?>:</label>
				<?php wp_dropdown_pages( array(
					'show_option_none'  => ' ',
					'name'              => $this->get_field_name( key($defaults) ),
					'selected'          => $instance[ key($defaults) ]
				) );
				?>
			</p>
			<?php
			next( $defaults );// forwards the key of $defaults array
		}
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		for( $i=0; $i<2; $i++ ) {
			$var = 'page_id'.$i;
			$instance[ $var] = absint( $new_instance[ $var ] );
		}

		return $instance;
	}

	function widget( $args, $instance ) {
		extract( $args );
		extract( $instance );

		global $post;
		$page_array = array();
		for( $i=0; $i<2; $i++ ) {
			$var = 'page_id'.$i;
			$page_id = isset( $instance[ $var ] ) ? $instance[ $var ] : '';

			if( !empty( $page_id ) ) {
				array_push( $page_array, $page_id );// Push the category id in the array
			}
		}
		$get_featured_pages = new WP_Query( array(
			'posts_per_page'        => 2,
			'post_type'             =>  array( 'page' ),
			'post__in'              => $page_array,
		) );
		echo $before_widget; ?>
		<?php
		if( !empty($page_array) ) {
			while( $get_featured_pages->have_posts() ):$get_featured_pages->the_post();

				if( !has_post_thumbnail() )
					return;
				?>
				<div class="collection-block">
					<figure class="slider-collection-img">
						<?php echo get_the_post_thumbnail( $post->ID, 'estore-featured-image' ); ?>
					</figure>
					<h3 class="slider-title"><a href="<?php echo esc_url( get_the_permalink() ); ?>"><?php echo esc_html( get_the_title() ); ?></a></h3>
				</div>
			<?php endwhile;
			wp_reset_postdata();
		}
		?>
		<?php
		echo $after_widget;
	}
}
