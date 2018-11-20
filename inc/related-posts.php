<?php $related_posts = estore_related_posts_function(); ?>

<?php if ( $related_posts->have_posts() ): ?>

	<div class="related-posts-wrapper">

		<h4 class="related-posts-main-title">
			<i class="fa fa-thumbs-up"></i><span><?php esc_html_e( 'You May Also Like', 'estore' ); ?></span>
		</h4>

		<div class="related-posts tg-column-wrapper clearfix">

			<?php
			while ( $related_posts->have_posts() ) : $related_posts->the_post(); ?>
				<div class="tg-column-3">

					<?php if ( has_post_thumbnail() ): ?>
						<?php
						$title_attribute     = esc_attr( get_the_title( $post->ID ) );
						$thumb_id            = get_post_thumbnail_id( get_the_ID() );
						$img_altr            = get_post_meta( $thumb_id, '_wp_attachment_image_alt', true );
						$img_alt             = ! empty( $img_altr ) ? $img_altr : $title_attribute;
						$post_thumbnail_attr = array(
							'alt'   => esc_attr( $img_alt ),
							'title' => esc_attr( $title_attribute ),
						); ?>
						<div class="post-thumbnails">
							<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>">
								<?php the_post_thumbnail( 'estore-featured-image', $post_thumbnail_attr ); ?>
							</a>
						</div>
					<?php endif; ?>

					<div class="wrapper">

						<h3 class="entry-title">
							<a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php the_title(); ?>"><?php the_title(); ?></a>
						</h3><!--/.post-title-->

						<div class="entry-meta">
							<span class="byline author vcard"><i class="fa fa-user"></i><a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>" title="<?php echo esc_attr(get_the_author()); ?>"><?php echo esc_html( get_the_author() ); ?></a></span>
							<?php if ( ! post_password_required() && comments_open() && get_theme_mod('estore_postmeta_comment', '') == '' ) : ?>
							<span class="comments-link"><i class="fa fa-comments-o"></i><?php comments_popup_link( esc_html__( '0 Comment', 'estore' ), esc_html__( '1 Comment', 'estore' ), esc_html__( ' % Comments', 'estore' ) ); ?></span>
							<?php
							endif; ?>
						</div>

					</div>

				</div><!--/.related-->
			<?php
		endwhile; ?>

		</div><!--/.post-related-->

	</div>
<?php endif; ?>

<?php wp_reset_query(); ?>
