<?php

// Clients/Brands Logo Widget
class estore_logo_widget extends WP_Widget {
	function __construct() {
		$widget_ops  = array(
			'classname'   => 'widget_logo clearfix',
			'description' => esc_html__( 'Add your clients/brand logo images here', 'estore' ),
		);
		$control_ops = array(
			'width'  => 200,
			'height' => 250,
		);
		parent::__construct( false, $name = esc_html__( 'TG: Logo', 'estore' ), $widget_ops );
	}

	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance,
			array(
				'title'       => '',
				'logo_image1' => '',
				'logo_link1'  => '',
				'logo_image2' => '',
				'logo_link2'  => '',
				'logo_image3' => '',
				'logo_link3'  => '',
				'logo_image4' => '',
				'logo_link4'  => '',
				'logo_image5' => '',
				'logo_link5'  => '',
			)
		);
		$title    = esc_attr( $instance['title'] );

		for ( $i = 1; $i < 6; $i ++ ) {
			$image_link = 'logo_link' . $i;
			$image_url  = 'logo_image' . $i;

			$instance[ $image_link ] = esc_url( $instance[ $image_link ] );
			$instance[ $image_url ]  = esc_url( $instance[ $image_url ] );
		}

		?>

		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php esc_html_e( 'Title:', 'estore' ); ?></label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" />
		</p>
		<label><?php esc_html_e( 'Add your clients/brands logo Here', 'estore' ); ?></label>
		<?php
		for ( $i = 1; $i < 6; $i ++ ) {
			$image_link = 'logo_link' . $i;
			$image_url  = 'logo_image' . $i;
			?>
			<p>
				<label for="<?php echo $this->get_field_id( $image_link ); ?>"> <?php esc_html_e( 'Logo Link ', 'estore' );
					echo $i; ?></label>
				<input type="text" class="widefat" id="<?php echo $this->get_field_id( $image_link ); ?>" name="<?php echo $this->get_field_name( $image_link ); ?>" value="<?php echo $instance[ $image_link ]; ?>" />
			</p>
			<p>
				<label for="<?php echo $this->get_field_id( $image_url ); ?>"> <?php esc_html_e( 'Logo Image ', 'estore' );
					echo $i; ?></label>
			<div class="media-uploader" id="<?php echo $this->get_field_id( $image_url ); ?>">
				<div class="custom_media_preview">
					<?php if ( $instance[ $image_url ] != '' ) : ?>
						<img class="custom_media_preview_default" src="<?php echo esc_url( $instance[ $image_url ] ); ?>" style="max-width:100%;" />
					<?php endif; ?>
				</div>
				<input type="text" class="widefat custom_media_input" id="<?php echo $this->get_field_id( $image_url ); ?>" name="<?php echo $this->get_field_name( $image_url ); ?>" value="<?php echo esc_url( $instance[ $image_url ] ); ?>" style="margin-top:5px;" />
				<button class="custom_media_upload button button-secondary button-large" id="<?php echo $this->get_field_id( $image_url ); ?>" data-choose="<?php esc_attr_e( 'Choose an image', 'estore' ); ?>" data-update="<?php esc_attr_e( 'Use image', 'estore' ); ?>" style="width:100%;margin-top:6px;margin-right:30px;"><?php esc_html_e( 'Select an Image', 'estore' ); ?></button>
			</div>
			</p>

		<?php } // Loop ending
	}

	function update( $new_instance, $old_instance ) {
		$instance          = $old_instance;
		$instance['title'] = sanitize_text_field( $new_instance['title'] );

		for ( $i = 1; $i < 7; $i ++ ) {
			$image_link = 'logo_link' . $i;
			$image_url  = 'logo_image' . $i;

			$instance[ $image_link ] = esc_url_raw( $new_instance[ $image_link ] );
			$instance[ $image_url ]  = esc_url_raw( $new_instance[ $image_url ] );
		}

		return $instance;
	}

	function widget( $args, $instance ) {
		extract( $args );
		extract( $instance );

		$title = apply_filters( 'widget_title', isset( $instance['title'] ) ? $instance['title'] : '' );

		$image_array = array();
		$link_array  = array();

		$j = 0;
		for ( $i = 1; $i < 6; $i ++ ) {
			$image_link = 'logo_link' . $i;
			$image_url  = 'logo_image' . $i;

			$image_link = isset( $instance[ $image_link ] ) ? $instance[ $image_link ] : '';
			$image_url  = isset( $instance[ $image_url ] ) ? $instance[ $image_url ] : '';

			array_push( $link_array, $image_link );
			array_push( $image_array, $image_url );

			// For Multilingual compatibility
			if ( function_exists( 'icl_register_string' ) ) {
				icl_register_string( 'eStore', 'TG: Logo Image' . $this->id . $j, $image_array[ $j ] );
				icl_register_string( 'eStore', 'TG: Logo Link' . $this->id . $j, $link_array[ $j ] );
			}

			$j ++;
		}

		echo $before_widget; ?>

		<div class="tg-container">
			<div class="tg-column-wrapper">
				<?php if ( ! empty( $title ) ) { ?>
					<?php echo $before_title . esc_html( $title ) . $after_title; ?>
				<?php }

				$output = '';
				if ( ! empty( $image_array ) ) {
					$output .= '<div class="tg-column-wrapper">';
					for ( $i = 1; $i < 6; $i ++ ) {
						$j                  = $i - 1;
						$attachment_post_id = attachment_url_to_postid( $image_array[ $j ] );
						$image_attributes   = wp_get_attachment_image_src( $attachment_post_id, 'full' );
						if ( ! empty( $image_array[ $j ] ) ) {
							$output .= '<div class="tg-column-5">';
							// For Multilingual compatibility
							if ( function_exists( 'icl_t' ) ) {
								$image_array[ $j ] = icl_t( 'eStore', 'TG: Logo Image' . $this->id . $j, $image_array[ $j ] );
							}
							if ( ! empty( $link_array[ $j ] ) ) {
								if ( function_exists( 'icl_t' ) ) {
									$link_array[ $j ] = icl_t( 'eStore', 'TG: Logo Link' . $this->id . $j, $link_array[ $j ] );
								}

								$output .= '<a href="' . esc_url( $link_array[ $j ] ) . '" class="logo-link" target="_blank"><img src="' . esc_url( $image_array[ $j ] ) . '" width="' . esc_attr( $image_attributes[1] ) . '" height="' . esc_attr( $image_attributes[2] ) . '"></a>';
							} else {
								$output .= '<img src="' . esc_url( $image_array[ $j ] ) . '" width="' . esc_attr( $image_attributes[1] ) . '" height="' . esc_attr( $image_attributes[2] ) . '">';
							}
							$output .= '</div>';
						}
					}
					$output .= '</div>';
					echo $output;
				}
				?>
			</div>
		</div>
		<?php
		echo $after_widget;
	}
}
