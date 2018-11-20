<?php
/**
 * Template part for displaying posts.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package ThemeGrill
 * @subpackage eStore
 * @since eStore 0.1
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php do_action( 'estore_before_post_content' ); ?>

	<div class="entry-thumbnail">
		<?php if ( get_theme_mod('estore_postmeta', '') == '' && get_theme_mod( 'estore_postmeta_date', '') == '' ) { ?>
		<span class="posted-on"><a href="<?php echo esc_url( get_the_permalink() ); ?>"><?php the_time( get_option( 'date_format' ) ); ?></a></span>
		<?php }
		if ( has_post_thumbnail() ) { ?>
			<?php $title_attribute = esc_attr( get_the_title( $post->ID ) );
			$thumb_id              = get_post_thumbnail_id( get_the_ID() );
			$img_altr              = get_post_meta( $thumb_id, '_wp_attachment_image_alt', true );
			$img_alt               = ! empty( $img_altr ) ? $img_altr : $title_attribute;
			$post_thumbnail_attr   = array(
				'alt'   => esc_attr( $img_alt ),
				'title' => esc_attr( $title_attribute ),
			); ?>
			<?php the_post_thumbnail( 'estore-slider', $post_thumbnail_attr ); ?>
		<?php } ?>
	</div>
	<?php
		if ( is_single() ) {
			the_title( '<h1 class="entry-title">', '</h1>' );
		} else {
			the_title( '<h2 class="entry-title"><a href="' . esc_url( get_the_permalink() ) . '" rel="bookmark">', '</a></h2>' );
		}
	?>

	<div class="entry-content-text-wrapper clearfix">
		<div class="entry-content-wrapper">
			<?php estore_entry_meta(); ?>
			<div class="entry-content">
				<?php the_excerpt(); ?>
				<div class="entry-btn">
					<a href="<?php echo esc_url( get_the_permalink() ); ?>" class="btn"><?php esc_html_e( 'Read more', 'estore' ); ?></a>
				</div>
			</div>
		</div>
	</div>

	<?php do_action( 'estore_after_post_content' ); ?>
</article><!-- #post-## -->
