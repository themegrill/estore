<?php
// Featured Posts Grid Widget
class estore_posts_grid extends WP_Widget {
	function __construct() {
		$widget_ops = array(
			'classname'   => 'widget-collection clearfix',
			'description' => esc_html__( 'Show Featured Category Posts in Grid Layout.', 'estore' )
		);
		$control_ops = array(
			'width'  => 200,
			'height' => 250
		);
		parent::__construct( false, $name = esc_html__( 'TG: Category Grid', 'estore' ), $widget_ops, $control_ops);
	}

	function form( $instance ) {
		$defaults[ 'title' ]            = '';
		$defaults[ 'subtitle' ]         = '';
		$defaults[ 'category']          = '';
		$defaults[ 'post_number' ]      = 10;
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
		$post_number      = absint( $instance[ 'post_number' ] );
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
					'selected'         => $instance['category']
				)
			);
			?>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'post_number' ); ?>"><?php esc_html_e( 'Number of Posts:', 'estore' ); ?></label>
			<input id="<?php echo $this->get_field_id( 'post_number' ); ?>" name="<?php echo $this->get_field_name( 'post_number' ); ?>" type="number" value="<?php echo $post_number; ?>" />
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

		$instance[ 'post_number' ]  = absint( $new_instance[ 'post_number' ] );

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
		$post_number      = isset( $instance[ 'post_number' ] ) ? $instance[ 'post_number' ] : '';

		$args = array(
			'posts_per_page'        => $post_number,
			'post_type'             => 'post',
			'category__in'          => $category
		);

		// For Multilingual compatibility
		if ( function_exists( 'icl_register_string' ) ) {
			icl_register_string( 'eStore', 'TG: Category Grid Subtitle' . $this->id, $subtitle );
			icl_register_string( 'eStore', 'TG: Category Grid Image' . $this->id, $cat_image_url );
			icl_register_string( 'eStore', 'TG: Category Grid Image Link' . $this->id, $cat_image_link );
		}
		if ( function_exists( 'icl_t' ) ) {
			$subtitle       = icl_t( 'eStore', 'TG: Category Grid Subtitle'. $this->id, $subtitle );
			$cat_image_url  = icl_t( 'eStore', 'TG: Category Grid Image'. $this->id, $cat_image_url );
			$cat_image_link = icl_t( 'eStore', 'TG: Category Grid Image Link'. $this->id, $cat_image_link );
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
					if($count == 1){ ?>
						<div class="tg-column-4 collection-block">
							<div class="hot-product-block">
								<div class="hot-product-content-wrapper clearfix">
									<?php
									$image_id = get_post_thumbnail_id();
									$image_url = wp_get_attachment_image_src($image_id,'estore-medium-image', false); ?>
									<figure class="hot-img">
										<a href="<?php the_permalink(); ?>">
											<?php if($image_url[0]){ ?>
												<img src="<?php echo esc_url( $image_url[0] ); ?>" alt="<?php the_title_attribute(); ?>">
											<?php } else { ?>
												<img src="<?php echo get_template_directory_uri() . '/images/placeholder-blog-380x250.jpg'; ?>" alt="<?php the_title_attribute(); ?>">
											<?php } ?>
										</a>
									</figure>
									<div class="hot-content-wrapper">
										<h3 class="hot-title"> <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
										<!-- Rating products -->
										<div class="hot-content"><?php the_excerpt(); ?></div>
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
									<?php if($image_url[0]){ ?>
										<img src="<?php echo esc_url( $image_url[0] ); ?>" alt="<?php the_title_attribute(); ?>">
									<?php } else { ?>
										<img src="<?php echo get_template_directory_uri() . '/images/placeholder-blog.jpg'; ?>" alt="<?php the_title_attribute(); ?>" width="75" height="75">
									<?php } ?>
								</a>
							</figure>
							<div class="product-list-content">
								<h3 class="product-list-title"> <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a> </h3>
								<div class="entry-meta">
									<span class="byline author vcard"><i class="fa fa-user"></i><a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>" title="<?php echo esc_attr(get_the_author()); ?>"><?php echo esc_html( get_the_author() ); ?></a></span>
									<?php

									if ( ! post_password_required() && comments_open() ) { ?>
										<span class="comments-link"><i class="fa fa-comments-o"></i><?php comments_popup_link( esc_html__( '0 Comment', 'estore' ), esc_html__( '1 Comment', 'estore' ), esc_html__( ' % Comments', 'estore' ) ); ?></span>
									<?php } ?>
								</div>
							</div> <!-- cart-wishlist-btn end -->
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
			</div>
		</div><!-- collection-block-wrapper tg-column-wrapper clearfix -->
		<?php
		echo $after_widget;
	}
}
