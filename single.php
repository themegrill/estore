<?php
/**
 * The template for displaying all single posts.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package ThemeGrill
 * @subpackage eStore
 * @since eStore 0.1
 */
get_header();

	$estore_layout = estore_layout_class();
	?>
	<div id="content" class="site-content"><!-- #content.site-content -->
		<div class="page-header clearfix">
			<div class="tg-container">
				<?php estore_entry_title(); ?>
				<h3 class="entry-sub-title"><?php estore_breadcrumbs(); ?></h3>
			</div>
		</div>
		<main id="main" class="clearfix <?php echo esc_attr($estore_layout); ?>">
			<div class="tg-container">
				<div id="primary">
					<?php
					while ( have_posts() ) : the_post(); ?>

						<?php get_template_part( 'template-parts/content', 'single' ); ?>

						<?php the_post_navigation(); ?>

						<?php if ( ( get_theme_mod( 'estore_author_bio_setting', 0 ) == 1 ) && ( get_the_author_meta( 'description' ) ) ) { ?>
							<div class="author-box clearfix">
								<div class="author-img"><?php echo get_avatar( get_the_author_meta( 'user_email' ), '100' ); ?></div>
								<div class="author-description-wrapper">
									<h4 class="author-name"><?php the_author_meta( 'display_name' ); ?></h4>

									<p class="author-description"><?php the_author_meta( 'description' ); ?></p>
								</div>
							</div>
						<?php } ?>

						<?php if ( get_theme_mod( 'estore_related_posts_activate', 0 ) == 1 ) {
						get_template_part( 'inc/related-posts' );
								}
						?>

						<?php
						// If comments are open or we have at least one comment, load up the comment template.
						if ( comments_open() || get_comments_number() ) :
							comments_template();
						endif;

						get_template_part('navigation', 'none');

					endwhile; // End of the loop. ?>
				</div> <!-- Primary end -->
				<?php estore_sidebar_select(); ?>
			</div>
		</main>
	</div>

<?php get_footer(); ?>
