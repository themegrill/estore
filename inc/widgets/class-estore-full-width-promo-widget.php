<?php
// Horizontal Promo - From Pages
class estore_full_width_promo_widget extends WP_Widget {
	function __construct() {
		$widget_ops = array(
			'classname'   => 'widget_full_width_promo widget-collection-thumb clearfix',
			'description' => esc_html__( 'Display some pages with featured image in horizontal layout.', 'estore' ) );
		$control_ops = array(
			'width'  => 200,
			'height' => 250
		);
		parent::__construct( false, $name = esc_html__( 'TG: Horizontal Promo', 'estore' ), $widget_ops, $control_ops);
	}

	function form( $instance ) {
		for ( $i=0; $i<3; $i++ ) {
			$var = 'page_id'.$i;
			$defaults[$var] = '';
		}

		$instance = wp_parse_args( (array) $instance, $defaults );
		for ( $i=0; $i<3; $i++ ) {
			$var = 'page_id'.$i;
			$var = absint( $instance[ $var ] );
		}

		for( $i=0; $i<3; $i++) { ?>
			<p>
				<label for="<?php echo $this->get_field_id( 'page_id'.$i ); ?>"><?php esc_html_e( 'Pages', 'estore' ); ?>:</label>
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
		for( $i=0; $i<3; $i++ ) {
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
		for( $i=0; $i<3; $i++ ) {
			$var = 'page_id'.$i;
			$page_id = isset( $instance[ $var ] ) ? $instance[ $var ] : '';

			if( !empty( $page_id ) ) {
				array_push( $page_array, $page_id );// Push the category id in the array
			}
		}

		$get_featured_pages = new WP_Query( array(
			'posts_per_page'        => 3,
			'post_type'             =>  array( 'page' ),
			'post__in'              => $page_array,
		) );
		echo $before_widget; ?>
		<div class="tg-container">
			<div class="tg-column-wrapper clearfix">
				<?php
				if( !empty($page_array) ) {
					while( $get_featured_pages->have_posts() ):$get_featured_pages->the_post();

						if( !has_post_thumbnail() )
							return;
						?>
						<div class="tg-column-3 collection-thumb-block">
							<a href="<?php echo esc_url( get_the_permalink() ); ?>">
								<figure class="collection-thumb-img">
									<?php echo get_the_post_thumbnail( $post->ID, 'estore-featured-image' ); ?>
								</figure>
								<span class="collection-thumb-hover">
							<span class="collection-thumb-title-wrapper">
								<span class="collection-thumb-title"><?php echo esc_html( get_the_title() ); ?></span>
							</span>
						</span> <!-- collection-thumb-hover end -->
							</a>
						</div>
					<?php
					endwhile;
					wp_reset_postdata();
				}
				?>
			</div>
		</div>
		<?php
		echo $after_widget;
	}
}
