wp.customize.controlConstructor[ 'estore-editor' ] = wp.customize.Control.extend( {

	ready : function () {

		'use strict';

		var control = this,
			id      = 'editor_' + control.id.replace( '[', '-' ).replace( ']', '' );

		wp.editor.initialize( id, {
			tinymce      : {
				wpautop : true
			},
			quicktags    : true,
			mediaButtons : true
		} );

	},

	onChangeActive : function ( active, args ) {

		'use strict';

		var control = this,
			id      = 'editor_' + control.id.replace( '[', '-' ).replace( ']', '' ),
			element = control.container.find( 'textarea' ),
			editor;

		editor = tinyMCE.get( id );

		if ( editor ) {

			editor.onChange.add( function ( ed ) {
				var content;

				ed.save();
				content = editor.getContent();
				element.val( content ).trigger( 'change' );
				wp.customize.instance( control.id ).set( content );
			} );

		}

	}

} );
