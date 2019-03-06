/**
 * Remove activate button and replace with activation in progress button.
 *
 * @package eStore
 */

/**
 * Import button
 */
jQuery( document ).ready( function ( $ ) {

	$( '.btn-get-started' ).click( function ( e ) {
		e.preventDefault();
		var extra_uri, redirect_uri, state, dismiss_nonce;

		// Show About > import button while processing.
		if ( jQuery( this ).parents( '.theme-actions' ).length ) {
			jQuery( this ).parents( '.theme-actions' ).css( 'opacity', '1' );
		}

		// Show updating gif icon.
		jQuery( this ).addClass( 'updating-message' );

		// Change button text.
		jQuery( this ).text( estore_redirect_demo_page.btn_text );

		// Assign `TG demo importer` plugin state for processing from PHP.
		if ( $( this ).hasClass( 'tdi-activated' ) ) { // Installed and activated.
			state = 'activated';
		} else if ( $( this ).hasClass( 'tdi-installed' ) ) { // Installed but not activated.
			state = 'installed';
		} else { // Not installed.
			state = '';
		}

		var data = {
			action   : 'import_button',
			security : estore_redirect_demo_page.nonce,
			state    : state
		};

		$.ajax( {
			type    : "POST",
			url     : ajaxurl, // URL to "wp-admin/admin-ajax.php"
			data    : data,
			success : function ( response ) {
				extra_uri = '';
				if ( jQuery( '.estore-message-close' ).length ) {
					dismiss_nonce = jQuery( '.estore-message-close' ).attr( 'href' ).split( '_estore_notice_nonce=' )[1];
					extra_uri     = '&_estore_notice_nonce=' + dismiss_nonce;
				}

				redirect_uri         = response.redirect + extra_uri;
				window.location.href = redirect_uri;
			},
			error   : function ( xhr, ajaxOptions, thrownError ) {
				console.log( thrownError );
			}
		} );

	} );
} );
