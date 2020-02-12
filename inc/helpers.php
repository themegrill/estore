<?php
/**
 * eStore helper function
 *
 * @package ThemeGrill
 * @subpackage eStore
 * @since 1.4.2
 */


if ( ! function_exists( 'estore_get_post_id' ) ) :
	/**
	 * Store the post ids.
	 *
	 * Since blog page takes the first post as its id,
	 * here we are storing the id of the post and for the blog page,
	 * storing its value via getting the specific page id through:
	 * `get_option( 'page_for_posts' )`
	 *
	 * @return false|int|mixed|string|void
	 */
	function estore_get_post_id() {

		$post_id        = '';
		$page_for_posts = get_option( 'page_for_posts' );

		// For single post and pages.
		if ( is_singular() ) {
			$post_id = get_the_ID();
		} // For the blog page.
		elseif ( is_home() && $page_for_posts ) {
			$post_id = $page_for_posts;
		}

		// Return the post ID.
		return $post_id;

	}
endif;

if ( ! function_exists( 'estore_post_thumbnail' ) ) :
	/**
	 * Displays post thumbnail.
	 */
	function estore_post_thumbnail() {

		if ( has_post_thumbnail() ) {
			$title_attribute     = esc_attr( get_the_title() );
			$thumb_id            = get_post_thumbnail_id( get_the_ID() );
			$img_altr            = get_post_meta( $thumb_id, '_wp_attachment_image_alt', true );
			$img_alt             = ! empty( $img_altr ) ? $img_altr : $title_attribute;
			$post_thumbnail_attr = array(
				'alt'   => esc_attr( $img_alt ),
				'title' => esc_attr( $title_attribute ),
			);

			the_post_thumbnail( 'estore-slider', $post_thumbnail_attr );
		}

	}
endif;
