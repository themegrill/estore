<?php
// Estore About Widget
class estore_about extends WP_Widget {
	function __construct() {
		$widget_ops = array(
			'classname'   => 'widget-about clearfix',
			'description' => esc_html__( 'Show your about page. Suitable for Click to Action.', 'estore' )
		);
		$control_ops = array(
			'width'  => 200,
			'height' => 250
		);
		parent::__construct( false, $name = esc_html__( 'TG: About Widget', 'estore' ), $widget_ops, $control_ops);
	}

	function form( $instance ) {
		$defaults[ 'title' ]            = '';
		$defaults[ 'page_id' ]          = '';

		$instance = wp_parse_args( (array) $instance, $defaults );

		$title            = esc_attr( $instance[ 'title' ] );
		$page_id          = absint( $instance[ 'page_id' ] );
		?>

		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php esc_html_e( 'Title:', 'estore' ); ?></label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" />
		</p>

		<p><?php esc_html_e('Select a page to display Title, Excerpt and Featured image.', 'estore') ?></p>
		<label for="<?php echo $this->get_field_id( 'page_id' ); ?>"><?php esc_html_e( 'Page', 'estore' ); ?>:</label>

		<?php wp_dropdown_pages( array(
			'show_option_none'  => ' ',
			'name'              => $this->get_field_name( 'page_id' ),
			'selected'          => $instance[ 'page_id' ]
		) );
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance[ 'title' ]          = sanitize_text_field( $new_instance[ 'title' ] );
		$instance[ 'page_id' ]        = absint( $new_instance[ 'page_id' ] );

		return $instance;
	}

	function widget( $args, $instance ) {
		extract( $args );
		extract( $instance );

		global $post;
		$title            = apply_filters( 'widget_title', isset( $instance[ 'title' ] ) ? $instance[ 'title' ] : '');
		$page_id          = isset( $instance[ 'page_id' ] ) ? $instance[ 'page_id' ] : '';

		echo $before_widget; ?>
		<div class="section-wrapper">
		<?php if( $page_id ) : ?>
		<?php if( get_the_post_thumbnail($page_id) != '' ) : ?>
			<figure class="about-img">
				<?php echo get_the_post_thumbnail ( $page_id, 'full', '' ); ?>
			</figure>
		<?php endif; ?>
		<div class="tg-container">
		<div class="about-content-wrapper">
			<div class="about-block">
				<?php
				$the_query = new WP_Query( 'page_id='.$page_id );
				while( $the_query->have_posts() ):$the_query->the_post();
				$title_attribute = the_title_attribute( 'echo=0' );

				$output   = '<h3 class="about-title"><a href="' . esc_url( get_permalink() ) . '">' . $title . '</a></h3>';

				$output  .= '<h4 class="about-sub-title">'. get_the_title() .'</h4>';

				$output .= '<div class="about-content">' . get_the_excerpt() . '</div>';

				$output .= '</div>';
				echo $output;
				?>
			</div>
			<?php endwhile;

			// Reset Post Data
			wp_reset_postdata(); ?>
		</div><!-- .about-content-wrapper -->
	<?php endif; ?>
		</div><!-- .tg-container -->
		<?php echo $after_widget;
	}
}
