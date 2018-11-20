<?php
// Estore Featured Post Widget
class estore_featured_posts_widget extends WP_Widget {

	function __construct() {
		$widget_ops = array(
			'classname'   => 'widget_featured_posts_block blog-section',
			'description' => esc_html__( 'Display latest posts or posts of specific category', 'estore')
		);
		$control_ops = array(
			'width'  => 200,
			'height' => 250
		);
		parent::__construct( false,$name= esc_html__( 'TG: Featured Posts', 'estore' ),$widget_ops);
	}

	function form( $instance ) {
		$defaults[ 'title' ]      = '';
		$defaults[ 'number' ]     = 3;
		$defaults[ 'type' ]       = 'latest';
		$defaults[ 'category' ]   = '';

		$instance = wp_parse_args( (array) $instance, $defaults );


		$title       = esc_attr( $instance[ 'title' ] );
		$number      = absint( $instance[ 'number' ] );
		$type        = $instance[ 'type' ];
		$category    = $instance[ 'category' ];
		?>

		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php esc_html_e( 'Title:', 'estore' ); ?></label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php esc_html_e( 'Number of posts to display:', 'estore' ); ?></label>
			<input id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="text" value="<?php echo $number; ?>" size="3" />
		</p>

		<p>
			<input type="radio" <?php checked( $type, 'latest' ) ?> id="<?php echo $this->get_field_id( 'type' ); ?>" name="<?php echo $this->get_field_name( 'type' ); ?>" value="latest"/><?php esc_html_e( 'Show latest Posts', 'estore' );?><br />
			<input type="radio" <?php checked( $type,'category' ) ?> id="<?php echo $this->get_field_id( 'type' ); ?>" name="<?php echo $this->get_field_name( 'type' ); ?>" value="category"/><?php esc_html_e( 'Show posts from a category', 'estore' );?><br />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'category' ); ?>"><?php esc_html_e( 'Select category', 'estore' ); ?>:</label>
			<?php wp_dropdown_categories( array( 'show_option_none' =>' ','name' => $this->get_field_name( 'category' ), 'selected' => $category ) ); ?>
		</p>

		<?php
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		$instance[ 'title' ]       = sanitize_text_field( $new_instance[ 'title' ] );
		$instance[ 'number' ]      = absint( $new_instance[ 'number' ] );
		$instance[ 'type' ]        = $new_instance[ 'type' ];
		$instance[ 'category' ]    = $new_instance[ 'category' ];

		return $instance;
	}

	function widget( $args, $instance ) {
		extract( $args );
		extract( $instance );

		global $post;
		$title       = apply_filters( 'widget_title', isset( $instance[ 'title' ] ) ? $instance[ 'title' ] : '');
		$number      = empty( $instance[ 'number' ] ) ? 3 : $instance[ 'number' ];
		$type        = isset( $instance[ 'type' ] ) ? $instance[ 'type' ] : 'latest' ;
		$category    = isset( $instance[ 'category' ] ) ? $instance[ 'category' ] : '';

		if( $type == 'latest' ) {
			$get_featured_posts = new WP_Query( array(
				'posts_per_page'        => $number,
				'post_type'             => 'post',
				'ignore_sticky_posts'   => true
			) );
		}
		else {
			$get_featured_posts = new WP_Query( array(
				'posts_per_page'        => $number,
				'post_type'             => 'post',
				'category__in'          => $category
			) );
		}

		echo $before_widget; ?>

		<div class="tg-container">
			<div class="secton-title-wrapper">
				<?php if ( !empty( $title ) ) { echo $before_title . esc_html( $title ) . $after_title; } ?>
			</div>

			<div class="blog-wrapper tg-column-wrapper clearfix">
				<?php
				while( $get_featured_posts->have_posts() ):$get_featured_posts->the_post(); ?>
					<div <?php post_class( 'tg-column-3' ); ?>>
						<div class="blog-block">
							<figure class="entry-thumbnail">
								<?php

								$time_string = '<time class="posted-on" datetime="%1$s"><a href="%3$s" title="%4$s">%2$s</a></time>';

								$time_string = printf( $time_string,
									esc_attr( get_the_date( 'c' ) ),
									esc_html( get_the_date( 'M j' ) ),
									esc_html( esc_url( get_the_permalink() ) ),
									esc_html( the_title_attribute('echo=0') )
								);

								?>

								<?php
								if ( has_post_thumbnail() ) {
									$title_attribute     = esc_attr( get_the_title( $post->ID ) );
									$thumb_id            = get_post_thumbnail_id( get_the_ID() );
									$img_altr            = get_post_meta( $thumb_id, '_wp_attachment_image_alt', true );
									$img_alt             = ! empty( $img_altr ) ? $img_altr : $title_attribute;
									$post_thumbnail_attr = array(
										'alt'   => esc_attr( $img_alt ),
										'title' => esc_attr( $title_attribute ),
									);
									the_post_thumbnail('estore-featured-image', $post_thumbnail_attr);
								} else { ?>
									<img src='<?php echo get_template_directory_uri(); ?>/images/placeholder-blog-380x250.jpg' alt='<?php esc_attr__('Blog Image', 'estore');?>' width="380" height="250" />
								<?php } ?>
							</figure>


							<h4 class="entry-title"> <a href="<?php the_permalink(); ?>" title="<?php the_title_attribute();?>"><?php the_title(); ?></a></h4>

							<div class="entry-content-text-wrapper clearfix">

								<div class="entry-content-wrapper">
									<div class="entry-meta">
										<span class="byline author vcard"><i class="fa fa-user"></i><a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>" title="<?php echo esc_attr(get_the_author()); ?>"><?php echo esc_html( get_the_author() ); ?></a></span>

										<span class="comments-link"><i class="fa fa-comments-o"></i><?php comments_popup_link( esc_html__( '0 Comment', 'estore' ), esc_html__( '1 Comment', 'estore' ), esc_html__( ' % Comments', 'estore' ) ); ?></span>

										<span class="cat-links"><i class="fa fa-folder-open"></i><?php the_category(', '); ?></span>

										<?php $tags_list = get_the_tag_list( '<span class="tag-links">', ', ', '</span>' );
										if ( $tags_list ) echo $tags_list;
										?>
									</div>
									<div class="entry-content">
										<?php the_excerpt(); ?>
									</div>
									<div class="entry-btn">
										<a class="btn" href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"> <?php echo esc_html_e( 'Read more' , 'estore' ) ?></a>
									</div>
								</div>
							</div>
						</div>
					</div><!-- .post_class -->
				<?php
				endwhile;
				?>
			</div><!-- .blog-wrapper -->
		</div><!-- .tg-container -->
		<?php
		// Reset Post Data
		wp_reset_postdata();
		echo $after_widget;
	}
}
