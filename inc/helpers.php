<?php
/**
 * eStore helper function
 *
 * @package ThemeGrill
 * @subpackage eStore
 * @since 1.4.2
 */


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
	}

	// For the blog page.
	elseif ( is_home() && $page_for_posts ) {
		$post_id = $page_for_posts;
	}

	// Return the post ID.
	return $post_id;

}
