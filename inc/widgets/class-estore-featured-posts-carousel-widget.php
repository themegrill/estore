<?php
// Featured Category Carousel Widget
class estore_featured_posts_carousel_widget extends WP_Widget {

	function __construct() {
		$widget_ops = array(
			'classname'   => 'slider_wrapper widget-featured-collection clearfix',
			'description' => esc_html__( 'Display latest posts or posts of specific category, which will be used as the carousel.', 'estore' ),
		);

		$control_ops = array(
			'width'  => 200,
			'height' => 250,
		);

		parent::__construct( false, $name = esc_html__( 'TG: Category Carousel', 'estore' ), $widget_ops );
	}

	function form( $instance ) {
		$tg_defaults['title']    = '';
		$tg_defaults['subtitle'] = '';
		$tg_defaults['number']   = 5;
		$tg_defaults['type']     = 'latest';
		$tg_defaults['category'] = '';

		$instance = wp_parse_args( (array) $instance, $tg_defaults );

		$title    = esc_attr( $instance['title'] );
		$subtitle = esc_textarea( $instance['subtitle'] );
		$number   = $instance['number'];
		$type     = $instance['type'];
		$category = $instance['category'];
		?>

		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php esc_html_e( 'Title:', 'estore' ); ?></label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" />
		</p>

		<?php esc_html_e( 'Description:', 'estore' ); ?>
		<textarea class="widefat" rows="5" cols="20" id="<?php echo $this->get_field_id( 'subtitle' ); ?>" name="<?php echo $this->get_field_name( 'subtitle' ); ?>"><?php echo $subtitle; ?></textarea>

		<p>
			<label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php esc_html_e( 'Number of posts to display:', 'estore' ); ?></label>
			<input id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="text" value="<?php echo $number; ?>" size="3" />
		</p>

		<p>
			<input type="radio" <?php checked( $type, 'latest' ); ?> id="<?php echo $this->get_field_id( 'type' ); ?>" name="<?php echo $this->get_field_name( 'type' ); ?>" value="latest"/><?php esc_html_e( 'Show latest Posts', 'estore' ); ?><br />
			<input type="radio" <?php checked( $type, 'category' ); ?> id="<?php echo $this->get_field_id( 'type' ); ?>" name="<?php echo $this->get_field_name( 'type' ); ?>" value="category"/><?php esc_html_e( 'Show posts from a category', 'estore' ); ?><br />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'category' ); ?>"><?php esc_html_e( 'Select category', 'estore' ); ?>:</label>
			<?php
			wp_dropdown_categories(
				array(
					'show_option_none' => ' ',
					'name'             => $this->get_field_name( 'category' ),
					'selected'         => $category,
				)
			);
			?>
		</p>

		<?php
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		$instance['title'] = sanitize_text_field( $new_instance['title'] );
		if ( current_user_can( 'unfiltered_html' ) ) {
			$instance['subtitle'] = $new_instance['subtitle'];
		} else {
			$instance['subtitle'] = stripslashes( wp_filter_post_kses( addslashes( $new_instance['subtitle'] ) ) );
		}

		$instance['number']   = absint( $new_instance['number'] );
		$instance['type']     = $new_instance['type'];
		$instance['category'] = $new_instance['category'];

		return $instance;
	}

	function widget( $args, $instance ) {
		extract( $args );
		extract( $instance );

		global $post;
		$title    = apply_filters( 'widget_title', isset( $instance['title'] ) ? $instance['title'] : '' );
		$subtitle = isset( $instance['subtitle'] ) ? $instance['subtitle'] : '';
		$number   = empty( $instance['number'] ) ? 5 : $instance['number'];
		$type     = isset( $instance['type'] ) ? $instance['type'] : 'latest';
		$category = isset( $instance['category'] ) ? $instance['category'] : '';

		if ( $type == 'latest' ) {
			$get_featured_posts = new WP_Query(
				array(
					'posts_per_page'      => $number,
					'post_type'           => 'post',
					'ignore_sticky_posts' => true,
				)
			);
		} else {
			$get_featured_posts = new WP_Query(
				array(
					'posts_per_page' => $number,
					'post_type'      => 'post',
					'category__in'   => $category,
				)
			);
		}

		// For Multilingual compatibility
		if ( function_exists( 'icl_register_string' ) ) {
			icl_register_string( 'eStore', 'TG: Category Carousel Subtitle' . $this->id, $subtitle );
		}
		if ( function_exists( 'icl_t' ) ) {
			$subtitle = icl_t( 'eStore', 'TG: Category Carousel Subtitle' . $this->id, $subtitle );
		}
		echo $before_widget;
		?>
		<div class="tg-container">
			<div class="section-title-wrapper clearfix">
				<div class="section-title-block">
					<?php if ( ! empty( $title ) ) { ?>
						<h3 class="page-title"><?php echo esc_html( $title ); ?></h3>
						<?php
					}
					if ( ! empty( $subtitle ) ) {
						?>
						<h4 class="page-sub-title"><?php echo esc_textarea( $subtitle ); ?></h4>
					<?php } ?>
				</div>
			</div>
			<div class="featured-wrapper clearfix">
				<ul class="featured-slider">
					<?php

					while ( $get_featured_posts->have_posts() ) :
						$get_featured_posts->the_post();
						?>
						<li>
							<?php
							$image_id  = get_post_thumbnail_id();
							$image_url = wp_get_attachment_image_src( $image_id, 'estore-square', false );
							?>
							<figure class="featured-img">
								<?php if ( isset( $image_url[0] ) ) { ?>
									<img src="<?php echo esc_url( $image_url[0] ); ?>" alt="<?php the_title_attribute(); ?>">
								<?php } else { ?>
									<img src="<?php echo esc_url( get_template_directory_uri() . '/images/placeholder-blog.jpg' ); ?>" alt="<?php the_title_attribute(); ?>">
								<?php } ?>
								<div class="featured-hover-wrapper">
									<div class="featured-hover-block">
										<?php if ( isset( $image_url[0] ) ) { ?>
											<a href="<?php echo esc_url( $image_url[0] ); ?>" class="zoom" data-rel="prettyPhoto"><i class="fa fa-search-plus"></i></a>
										<?php } else { ?>
											<a href="<?php echo esc_url( get_template_directory_uri() . '/images/placeholder-blog.jpg' ); ?>" class="zoom" data-rel="prettyPhoto"><i class="fa fa-search-plus"></i></a>
										<?php } ?>
										<a title="<?php the_title_attribute(); ?>" href="<?php the_permalink(); ?>" class="link"> <i class="fa fa-link"> </i> </a>
									</div>
								</div><!-- featured hover end -->
							</figure>
							<div class="featured-content-wrapper">
								<h3 class="featured-title"> <a title="<?php the_title_attribute(); ?>" href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
								<?php if ( function_exists( 'YITH_WCWL' ) ) {
									$product_id     = get_the_ID();
									$wishlist_url   = esc_url( YITH_WCWL()->get_wishlist_url() );
									$wishlist_url   = add_query_arg(
										array(
											'add_to_wishlist' => $product_id,
										),
										$wishlist_url
									);
									$wishlist_class = YITH_WCWL()->is_product_in_wishlist( $product_id ) ? 'yith-wcwl-wishlistexistsbrowse' : 'add_to_wishlist single_add_to_wishlist';?>
									<a href="<?php echo esc_url( $wishlist_url ); ?>" class="<?php echo esc_html( $wishlist_class ); ?>" data-product-id="<?php echo esc_attr( $product_id ); ?>" data-product-type="simple" data-original-product-id="<?php echo esc_attr( $product_id ); ?>" data-title="<?php esc_attr_e( 'Add to Wishlist', 'estore' ); ?>" rel="nofollow">
										<i class="fa fa-heart"></i>
										<span><?php esc_html_e( 'Add to Wishlist', 'estore' ); ?></span>
									</a>
								<?php } ?>
							</div><!-- featured content wrapper -->
						</li>
						<?php
					endwhile;
					?>
				</ul>
			</div>
		</div>
		<?php
		wp_reset_postdata();
		echo $after_widget;
	}
}
