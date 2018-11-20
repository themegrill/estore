<?php
// 728 X 90 Advertisement Widget
class estore_728x90_ad extends WP_Widget {
	function __construct() {
		$widget_ops = array(
			'classname'   => 'widget_image_with_link',
			'description' => esc_html__( 'Add your Advertisement here', 'estore')
		);
		$control_ops = array(
			'width'  => 200,
			'height' => 250
		);
		parent::__construct( false,$name= esc_html__( 'TG: Advertisement', 'estore' ),$widget_ops);
	}

	function form( $instance ) {
		$instance                = wp_parse_args( (array) $instance, array( 'title' => '', '728x90_image_url' => '', '728x90_image_link' => '') );
		$title                   = esc_attr( $instance[ 'title' ] );

		$image_link              = '728x90_image_link';
		$image_url               = '728x90_image_url';

		$instance[ $image_link ] = esc_url( $instance[ $image_link ] );
		$instance[ $image_url ]  = esc_url( $instance[ $image_url ] );

		?>

		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php esc_html_e( 'Title:', 'estore' ); ?></label>
			<input id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
		</p>
		<label><?php esc_html_e( 'Add your Advertisement Images Here. Any size supported.', 'estore' ); ?></label>
		<p>
			<label for="<?php echo $this->get_field_id( $image_link ); ?>"> <?php esc_html_e( 'Advertisement Image Link ', 'estore' ); ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( $image_link ); ?>" name="<?php echo $this->get_field_name( $image_link ); ?>" value="<?php echo $instance[$image_link]; ?>"/>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( $image_url ); ?>"> <?php esc_html_e( 'Advertisement Image ', 'estore' ); ?></label>
		<div class="media-uploader" id="<?php echo $this->get_field_id( $image_url ); ?>">
			<div class="custom_media_preview">
				<?php if ( $instance[ $image_url ] != '' ) : ?>
					<img class="custom_media_preview_default" src="<?php echo esc_url( $instance[ $image_url ] ); ?>" style="max-width:100%;" />
				<?php endif; ?>
			</div>
			<input type="text" class="widefat custom_media_input" id="<?php echo $this->get_field_id( $image_url ); ?>" name="<?php echo $this->get_field_name( $image_url ); ?>" value="<?php echo esc_url( $instance[$image_url] ); ?>" style="margin-top:5px;" />
			<button class="custom_media_upload button button-secondary button-large" id="<?php echo $this->get_field_id( $image_url ); ?>" data-choose="<?php esc_attr_e( 'Choose an image', 'estore' ); ?>" data-update="<?php esc_attr_e( 'Use image', 'estore' ); ?>" style="width:100%;margin-top:6px;margin-right:30px;"><?php esc_html_e( 'Select an Image', 'estore' ); ?></button>
		</div>
		</p>

	<?php }
	function update( $new_instance, $old_instance ) {
		$instance                = $old_instance;
		$instance[ 'title' ]     = sanitize_text_field( $new_instance[ 'title' ] );

		$image_link              = '728x90_image_link';
		$image_url               = '728x90_image_url';

		$instance[ $image_link ] = esc_url_raw( $new_instance[ $image_link ] );
		$instance[ $image_url ]  = esc_url_raw( $new_instance[ $image_url ] );

		return $instance;
	}

	function widget( $args, $instance ) {
		extract( $args );
		extract( $instance );

		$title      = apply_filters( 'widget_title', isset( $instance[ 'title' ] ) ? $instance[ 'title' ] : '' );

		$image_link = '728x90_image_link';
		$image_url  = '728x90_image_url';

		$image_link   = isset( $instance[ $image_link ] ) ? $instance[ $image_link ] : '';
		$image_url    = isset( $instance[ $image_url ] ) ? $instance[ $image_url ] : '';


		// For Multilingual compatibility
		if ( function_exists( 'icl_register_string' ) ) {
			icl_register_string( 'eStore', 'TG: Advertisement Image' . $this->id, $image_url );
			icl_register_string( 'eStore', 'TG: Advertisement Image Link' . $this->id, $image_link );
		}
		if ( function_exists( 'icl_t' ) ) {
			$image_url  = icl_t( 'eStore', 'TG: Advertisement Image'. $this->id, $image_url );
			$image_link = icl_t( 'eStore', 'TG: Advertisement Image Link'. $this->id, $image_link );
		}

		echo $before_widget; ?>

		<div class="image_with_link">
			<?php if ( !empty( $title ) ) { ?>
				<div class="image_with_link-title">
					<?php echo $before_title. esc_html( $title ) . $after_title; ?>
				</div>
			<?php }
			$output = '';
			if ( !empty( $image_url ) ) {
				$attachment_post_id = attachment_url_to_postid( $image_url );
				$img_altr           = get_post_meta( $attachment_post_id, '_wp_attachment_image_alt', true );
				$img_alt            = ! empty( $img_altr ) ? $img_altr : $title;
				$output .= '<div class="image_with_link-content">';
				if ( !empty( $image_link ) ) {
					$output .= '<a href="'.esc_url($image_link).'" class="single_image_with_link" target="_blank" rel="nofollow">
								<img src="'.esc_url($image_url).'" alt="'.esc_attr( $img_alt ).'" />
							</a>';
				} else {
					$output .= '<img src="'.esc_url($image_url).'" />';
				}
				$output .= '</div>';
				echo $output;
			} ?>
		</div>
		<?php
		echo $after_widget;
	}
}
