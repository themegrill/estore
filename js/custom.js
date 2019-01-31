jQuery(document).ready(function() {

	/**
	 * Search box.
	 */
	( function () {
		var searchBox, icon, searchWrapperArr;

		searchBox        = document.getElementsByClassName( 'header-search-box' )[0];
		searchWrapperArr = document.getElementsByClassName( 'search-wrapper' );
		icon             = searchWrapperArr[0] ? searchWrapperArr[0].getElementsByClassName( 'search-icon' )[0] : '';

		// Return if no search icon.
		if ( ! icon ) {
			return;
		}

		var showHideSearchForm = function ( action ) {
			if ( action === 'hide' ) {
				searchBox.classList.remove( 'active' );
				return;
			}
			// Show/hide search form
			searchBox.classList.toggle( 'active' );

			// autofocus
			if ( searchBox.classList.contains( 'active' ) ) {
				searchBox.getElementsByTagName( 'input' )[0].focus();
			}
		};

		// on search icon click
		icon.onclick = function () {
			showHideSearchForm();
		};

		// on esc key
		document.addEventListener( 'keyup', function ( e ) {
			if ( searchBox.classList.contains( 'active' ) && e.keyCode === 27 ) {
				showHideSearchForm( 'hide' );
			}
		} );

		// on click outside form
		document.addEventListener( 'click', function ( ev ) {
			if ( ev.target.closest( '.header-search-box' ) || ev.target.closest( '.search-icon' ) ) {
				return;
			}
			showHideSearchForm( 'hide' );
		} );

	} )(); // END: Search box.

	jQuery('.bottom-header-wrapper .category-toggle').click(function() {
		jQuery('#category-navigation').slideToggle('slow');
		jQuery(this).toggleClass('active');
	});

	// ScrollUp
	jQuery(window).scroll(function() {
		if (jQuery(this).scrollTop() > 1000) {
			jQuery('.scrollup').fadeIn();
		} else {
			jQuery('.scrollup').fadeOut();
		}
	});

	jQuery('.scrollup').click(function() {
		jQuery("html, body").animate({
			scrollTop: 0
		}, 2000);
		return false;
	});

	jQuery(window).on('load', function() {

		var width = Math.max(window.innerWidth, document.documentElement.clientWidth);
		if (width && width >= 768) {
			jQuery('#primary-menu').superfish({
				animation: {
					height: 'show'
				}, // slide-down effect without fade-in
				delay: 0.5 // 1.2 second delay on mouseout
			});
		}

		jQuery('.category-slider .home-slider').bxSlider({
			caption: true,
			auto: true,
			controls: true,
			pager: false,
			nextText: '<i class="fa fa-angle-right"> </i>',
			prevText: '<i class="fa fa-angle-left"> </i>',
			infiniteLoop: true,
			onSliderLoad: function(){
				jQuery(".home-slider").css("visibility", "visible");
			}
		});

		jQuery('.product-slider .home-slider').bxSlider({
			caption: true,
			auto: true,
			controls: true,
			pager: false,
			nextText: '<i class="fa fa-angle-right"> </i>',
			prevText: '<i class="fa fa-angle-left"> </i>',
			infiniteLoop: true,
			onSliderLoad: function(){
				jQuery(".home-slider").css("visibility", "visible");
			}
		});

		jQuery('.featured-slider').bxSlider({
			caption: true,
			auto: false,
			controls: true,
			pager: false,
			nextText: '<i class="fa fa-angle-right"> </i>',
			prevText: '<i class="fa fa-angle-left"> </i>',
			minSlides: 1,
			maxSlides: 5,
			slideWidth: 224,
			slideMargin: 20,
			onSliderLoad: function(){
				jQuery(".featured-slider").css("visibility", "visible");
			}
		});
	});

	jQuery('.add_to_wishlist').click(function(event) {
		jQuery(this).parent('.yith-wcwl-add-to-wishlist').children('.ajax-loading').css('display', 'block !important');
	});

	jQuery('.toggle-wrap .toggle').click(function(event) {
		jQuery('#primary-menu').slideToggle('slow');
	});

	jQuery('#site-navigation .menu-item-has-children, #primary-menu .page_item_has_children').append('<span class="sub-toggle"> <i class="fa fa-angle-right"></i> </span>');

	jQuery('#site-navigation .sub-toggle').click(function() {
		jQuery(this).parent('.menu-item-has-children').children('ul.sub-menu').first().slideToggle('1000');
		jQuery(this).children('.fa-angle-right').first().toggleClass('fa-angle-down');
		jQuery(this).parent('.page_item_has_children').children('ul.children').first().slideToggle('1000');
	});
});
