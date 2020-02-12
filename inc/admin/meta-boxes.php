<?php
/**
 * This function is responsible for rendering metaboxes in single post area
 *
 * @package ThemeGrill
 * @subpackage eStore
 * @since eStore 1.0.0
 */


/**
 * Add Meta Boxes.
 */
function estore_add_custom_box() {
	// Layout meta option for post and page.
	add_meta_box( 'page-layout', esc_html__( 'Select Layout', 'estore' ), 'estore_layout_call', array(
		'post',
		'page'
	), 'side', 'default' );
}
add_action( 'add_meta_boxes', 'estore_add_custom_box' );

/**
 * Displays metabox to for select layout option
 */
function estore_layout_call() {
	global $post;

	$page_specific_layout = array(
		'default-layout'              => array(
			'value' => 'default_layout',
			'label' => esc_html__( 'Default Layout', 'estore' )
		),
		'right-sidebar'               => array(
			'value' => 'right_sidebar',
			'label' => esc_html__( 'Right Sidebar', 'estore' )
		),
		'left-sidebar'                => array(
			'value' => 'left_sidebar',
			'label' => esc_html__( 'Left Sidebar', 'estore' )
		),
		'no-sidebar-full-width'       => array(
			'value' => 'no_sidebar_full_width',
			'label' => esc_html__( 'No Sidebar Full Width', 'estore' )
		),
		'no-sidebar-content-centered' => array(
			'value' => 'no_sidebar_content_centered',
			'label' => esc_html__( 'No Sidebar Content Centered', 'estore' )
		)
	);

	// Use nonce for verification.
	wp_nonce_field( basename( __FILE__ ), 'custom_meta_box_nonce' );

	foreach ( $page_specific_layout as $field ) {
		$layout_meta = get_post_meta( $post->ID, 'estore_page_specific_layout', true );

		if ( empty( $layout_meta ) ) {
			$layout_meta = 'default_layout';
		}
		?>
		<input class="post-format" type="radio" id="<?php echo esc_attr( $field['value'] ); ?>"
		       name="<?php echo esc_attr( 'estore_page_specific_layout' ); ?>"
		       value="<?php echo esc_attr( $field['value'] ); ?>" <?php checked( $field['value'], $layout_meta ); ?>/>
		<label class="post-format-icon"
		       for="<?php echo esc_attr( $field['value'] ); ?>"><?php echo esc_html( $field['label'] ); ?></label>
		<br/>
		<?php
	}
}

/**
 * save the custom metabox data
 * @hooked to save_post hook
 */
function estore_save_custom_meta( $post_id ) {
	// Verify the nonce before proceeding.
	if ( ! isset( $_POST['custom_meta_box_nonce'] ) || ! wp_verify_nonce( $_POST['custom_meta_box_nonce'], basename( __FILE__ ) ) ) {
		return;
	}

	// Stop WP from clearing custom fields on autosave.
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( 'page' === $_POST['post_type'] ) {
		if ( ! current_user_can( 'edit_page', $post_id ) ) {
			return $post_id;
		}
	} elseif ( ! current_user_can( 'edit_post', $post_id ) ) {
		return $post_id;
	}

	// Execute this saving function.
	$old = get_post_meta( $post_id, 'estore_page_specific_layout', true );
	$new = sanitize_key( $_POST[ 'estore_page_specific_layout' ] );

	if ( $new && $new != $old ) {
		update_post_meta( $post_id, 'estore_page_specific_layout', $new );
	}
}
add_action( 'save_post', 'estore_save_custom_meta' );
