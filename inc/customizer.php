<?php
/**
 * estore Theme Customizer.
 *
 * @package estore
 */

/**
 * Loads custom control for layout settings
 */
function estore_custom_controls() {

	require_once get_template_directory() . '/inc/admin/customize-image-radio-control.php';
	require_once get_template_directory() . '/inc/admin/customize-custom-css-control.php';
	require_once get_template_directory() . '/inc/admin/customize-texteditor-control.php';

}

add_action( 'customize_register', 'estore_custom_controls' );
/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function estore_customize_register( $wp_customize ) {
	// Transport postMessage variable set
	$customizer_selective_refresh = isset( $wp_customize->selective_refresh ) ? 'postMessage' : 'refresh';

	$wp_customize->get_setting( 'blogname' )->transport        = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport = 'postMessage';

	if ( isset( $wp_customize->selective_refresh ) ) {
		$wp_customize->selective_refresh->add_partial(
			'blogname',
			array(
				'selector'        => '#site-title a',
				'render_callback' => 'estore_customize_partial_blogname',
			)
		);

		$wp_customize->selective_refresh->add_partial(
			'blogdescription',
			array(
				'selector'        => '#site-description',
				'render_callback' => 'estore_customize_partial_blogdescription',
			)
		);
	}

	/**
	 * Class to include upsell link campaign for theme.
	 *
	 * Class ESTORE_Upsell_Section
	 */
	class ESTORE_Upsell_Section extends WP_Customize_Section {
		public $type = 'estore-upsell-section';
		public $url  = '';
		public $id   = '';

		/**
		 * Gather the parameters passed to client JavaScript via JSON.
		 *
		 * @return array The array to be exported to the client as JSON.
		 */
		public function json() {
			$json        = parent::json();
			$json['url'] = esc_url( $this->url );
			$json['id']  = $this->id;

			return $json;
		}

		/**
		 * An Underscore (JS) template for rendering this section.
		 */
		protected function render_template() {
			?>
			<li id="accordion-section-{{ data.id }}" class="estore-upsell-accordion-section control-section-{{ data.type }} cannot-expand accordion-section">
				<h3 class="accordion-section-title"><a href="{{{ data.url }}}" target="_blank">{{ data.title }}</a></h3>
			</li>
			<?php
		}
	}

	// Register `ESTORE_Upsell_Section` type section.
	$wp_customize->register_section_type( 'ESTORE_Upsell_Section' );

	// Add `ESTORE_Upsell_Section` to display pro link.
	$wp_customize->add_section(
		new ESTORE_Upsell_Section(
			$wp_customize,
			'estore_upsell_section',
			array(
				'title'      => esc_html__( 'View PRO version', 'estore' ),
				'url'        => 'https://themegrill.com/themes/estore/?utm_source=estore-customizer&utm_medium=view-pro-link&utm_campaign=view-pro#free-vs-pro',
				'capability' => 'edit_theme_options',
				'priority'   => 1,
			)
		)
	);

	// Header Media Placement.
	$wp_customize->add_setting(
		'estore_header_media_placement',
		array(
			'default'           => 'header_media_below_main_menu',
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'estore_sanitize_radio',
		)
	);

	$wp_customize->add_control(
		'estore_header_media_placement',
		array(
			'label'   => esc_html__( 'Choose the required option for Header Media placement', 'estore' ),
			'section' => 'header_image',
			'type'    => 'radio',
			'choices' => array(
				'header_media_above_site_title' => esc_html__( 'Position One: Display Header Media just above the site Title/Text', 'estore' ),
				'header_media_below_main_menu'  => esc_html__( 'Postion Two: Display Header Media just below the Main/Primary Menu', 'estore' ),
			),
		)
	);

	/**
	 * Header Options
	 */
	$wp_customize->add_panel(
		'estore_header_options',
		array(
			'capabitity'  => 'edit_theme_options',
			'description' => esc_html__( 'Change Header Settings here', 'estore' ),
			'priority'    => 160,
			'title'       => esc_html__( 'Header Options', 'estore' ),
		)
	);

	// Header Integrations.
	$wp_customize->add_section(
		'estore_header_integrations',
		array(
			'priority' => 30,
			'title'    => esc_html__( 'Header Integrations', 'estore' ),
			'panel'    => 'estore_header_options',
		)
	);

	// WPML Languages.
	$wp_customize->add_setting(
		'estore_header_lang',
		array(
			'default'           => '',
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'estore_sanitize_checkbox',
		)
	);

	$wp_customize->add_control(
		'estore_header_lang',
		array(
			'label'           => esc_html__( 'Enable Language Selection (WPML)', 'estore' ),
			'section'         => 'estore_header_integrations',
			'type'            => 'checkbox',
			'active_callback' => 'estore_is_wpml_activate',
			'priority'        => 40, // 10,20,30 for woocommerce settings.
		)
	);

	// Logo Section.
	$wp_customize->add_section(
		'estore_header_logo',
		array(
			'priority' => 10,
			'title'    => esc_html__( 'Header Logo', 'estore' ),
			'panel'    => 'estore_header_options',
		)
	);

	// Logo Placement.
	$wp_customize->add_setting(
		'estore_logo_placement',
		array(
			'default'           => 'header_logo_only',
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'estore_sanitize_radio',
		)
	);

	$wp_customize->add_control(
		'estore_logo_placement',
		array(
			'label'   => esc_html__( 'Choose the required option', 'estore' ),
			'section' => 'title_tagline',
			'type'    => 'radio',
			'choices' => array(
				'header_logo_only' => esc_html__( 'Header Logo Only', 'estore' ),
				'header_text_only' => esc_html__( 'Header Text Only', 'estore' ),
				'show_both'        => esc_html__( 'Show both header logo and text', 'estore' ),
				'disable'          => esc_html__( 'Disable', 'estore' ),
			),
		)
	);

	// Retina Logo Option.
	$wp_customize->add_setting(
		'estore_different_retina_logo',
		array(
			'default'           => 0,
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'estore_sanitize_checkbox',
		)
	);

	$wp_customize->add_control(
		'estore_different_retina_logo',
		array(
			'type'     => 'checkbox',
			'priority' => 8,
			'label'    => esc_html__( 'Different Logo for Retina Devices?', 'estore' ),
			'section'  => 'title_tagline',
		)
	);

	// Retina Logo Upload.
	$wp_customize->add_setting(
		'estore_retina_logo_upload',
		array(
			'default'           => '',
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'esc_url_raw',
		)
	);

	$wp_customize->add_control(
		new WP_Customize_Image_Control(
			$wp_customize,
			'estore_retina_logo_upload',
			array(
				'label'           => esc_html__( 'Retina Logo', 'estore' ),
				'description'     => esc_html__( 'Please upload the retina logo double the size of default logo. For eg: If you upload 100 * 100px for default logo then use 200 * 200px for retina logo.', 'estore' ),
				'priority'        => 8,
				'setting'         => 'estore_retina_logo_upload',
				'section'         => 'title_tagline',
				'active_callback' => 'estore_retina_logo',
			)
		)
	);

	// Header Top Bar Section.
	$wp_customize->add_section(
		'estore_header_bar',
		array(
			'priority' => 20,
			'title'    => esc_html__( 'Header Top Bar', 'estore' ),
			'panel'    => 'estore_header_options',
		)
	);

	// Header Bar Activation.
	$wp_customize->add_setting(
		'estore_bar_activation',
		array(
			'default'           => '',
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'estore_sanitize_checkbox',
		)
	);

	$wp_customize->add_control(
		'estore_bar_activation',
		array(
			'label'   => esc_html__( 'Activate the header top bar', 'estore' ),
			'section' => 'estore_header_bar',
			'type'    => 'checkbox',
		)
	);

	// Header Bar Left Section.
	$wp_customize->add_setting(
		'estore_bar_text',
		array(
			'default'           => '',
			'capability'        => 'edit_theme_options',
			'transport'         => $customizer_selective_refresh,
			'sanitize_callback' => 'estore_sanitize_text',
		)
	);

	$wp_customize->add_control(
		new Estore_Text_Editor_Control(
			$wp_customize,
			'estore_bar_text',
			array(
				'label'       => esc_html__( 'Header Text', 'estore' ),
				'description' => esc_html__( 'Paste the Font Awesome icon font', 'estore' ),
				'section'     => 'estore_header_bar',
				'setting'     => 'estore_bar_text',
			)
		)
	);

	// Selective refresh for header top bar text.
	if ( isset( $wp_customize->selective_refresh ) ) {
		$wp_customize->selective_refresh->add_partial(
			'estore_bar_text',
			array(
				'selector'        => '#header-ticker',
				'render_callback' => 'estore_bar_text',
			)
		);
	}

	// Header Search.
	$wp_customize->add_section(
		'estore_header_search',
		array(
			'priority' => 40,
			'title'    => esc_html__( 'Header Search', 'estore' ),
			'panel'    => 'estore_header_options',
		)
	);
	if ( class_exists( 'WooCommerce' ) ) :
		// Search option
		$wp_customize->add_setting(
			'estore_header_search_option',
			array(
				'default'           => 'wp_search',
				'capability'        => 'edit_theme_options',
				'sanitize_callback' => 'estore_sanitize_select',
			)
		);
		$wp_customize->add_control(
			'estore_header_search_option',
			array(
				'label'   => esc_html__( 'Choose a search option', 'estore' ),
				'section' => 'estore_header_search',
				'type'    => 'select',
				'choices' => array(
					'wp_search' => esc_html__( 'WordPress search', 'estore' ),
					'wc_search' => esc_html__( 'WooCommerce search', 'estore' ),
				),
			)
		);
	endif;

	/**
	 * Design Related Options
	 */
	$wp_customize->add_panel(
		'estore_design_options',
		array(
			'capability'  => 'edit_theme_options',
			'description' => esc_html__( 'Design Related Settings', 'estore' ),
			'priority'    => 180,
			'title'       => esc_html__( 'Design Options', 'estore' ),
		)
	);

	// Primary Color Setting.
	$wp_customize->add_section(
		'estore_primary_color_section',
		array(
			'priority' => 40,
			'title'    => esc_html__( 'Primary Color Option', 'estore' ),
			'panel'    => 'estore_design_options',
		)
	);

	$wp_customize->add_setting(
		'estore_primary_color',
		array(
			'default'              => '#00a9e0',
			'capability'           => 'edit_theme_options',
			'transport'            => 'postMessage',
			'sanitize_callback'    => 'estore_hex_color_sanitize',
			'sanitize_js_callback' => 'estore_color_escaping_sanitize',
		)
	);

	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'estore_primary_color',
			array(
				'label'   => esc_html__( 'This will reflect in links, buttons and many others. Choose a color to match your site', 'estore' ),
				'section' => 'estore_primary_color_section',
			)
		)
	);

	// Default Layout.
	$wp_customize->add_section(
		'estore_global_layout_section',
		array(
			'priority' => 10,
			'title'    => esc_html__( 'Default Layout', 'estore' ),
			'panel'    => 'estore_design_options',
		)
	);

	$wp_customize->add_setting(
		'estore_global_layout',
		array(
			'default'           => 'right_sidebar',
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'estore_sanitize_radio',
		)
	);

	$wp_customize->add_control(
		new Estore_Image_Radio_Control(
			$wp_customize,
			'estore_global_layout',
			array(
				'label'   => esc_html__( 'Select default layout. This layout will be reflected in whole site archives, categories, search page etc. The layout for a single post and page can be controlled from below options', 'estore' ),
				'section' => 'estore_global_layout_section',
				'type'    => 'radio',
				'choices' => array(
					'right_sidebar'               => Estore_ADMIN_IMAGES_URL . '/right-sidebar.png',
					'left_sidebar'                => Estore_ADMIN_IMAGES_URL . '/left-sidebar.png',
					'no_sidebar_full_width'       => Estore_ADMIN_IMAGES_URL . '/no-sidebar-full-width-layout.png',
					'no_sidebar_content_centered' => Estore_ADMIN_IMAGES_URL . '/no-sidebar-content-centered-layout.png',
				),
			)
		)
	);

	// Default Pages Layout.
	$wp_customize->add_section(
		'estore_default_page_layout_section',
		array(
			'priority' => 20,
			'title'    => esc_html__( 'Default Page Layout', 'estore' ),
			'panel'    => 'estore_design_options',
		)
	);

	$wp_customize->add_setting(
		'estore_default_page_layout',
		array(
			'default'           => 'right_sidebar',
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'estore_sanitize_radio',
		)
	);

	$wp_customize->add_control(
		new Estore_Image_Radio_Control(
			$wp_customize,
			'estore_default_page_layout',
			array(
				'label'   => esc_html__( 'Select default layout for pages. This layout will be reflected in all pages unless unique layout is set for specific page', 'estore' ),
				'section' => 'estore_default_page_layout_section',
				'type'    => 'radio',
				'choices' => array(
					'right_sidebar'               => Estore_ADMIN_IMAGES_URL . '/right-sidebar.png',
					'left_sidebar'                => Estore_ADMIN_IMAGES_URL . '/left-sidebar.png',
					'no_sidebar_full_width'       => Estore_ADMIN_IMAGES_URL . '/no-sidebar-full-width-layout.png',
					'no_sidebar_content_centered' => Estore_ADMIN_IMAGES_URL . '/no-sidebar-content-centered-layout.png',
				),
			)
		)
	);

	// Default Single Post Layout.
	$wp_customize->add_section(
		'estore_default_single_post_layout_section',
		array(
			'priority' => 30,
			'title'    => esc_html__( 'Default Single Post Layout', 'estore' ),
			'panel'    => 'estore_design_options',
		)
	);

	$wp_customize->add_setting(
		'estore_default_single_post_layout',
		array(
			'default'           => 'right_sidebar',
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'estore_sanitize_radio',
		)
	);

	$wp_customize->add_control(
		new Estore_Image_Radio_Control(
			$wp_customize,
			'estore_default_single_post_layout',
			array(
				'label'   => esc_html__( 'Select default layout for single posts. This layout will be reflected in all single posts unless unique layout is set for specific post', 'estore' ),
				'section' => 'estore_default_single_post_layout_section',
				'type'    => 'radio',
				'choices' => array(
					'right_sidebar'               => Estore_ADMIN_IMAGES_URL . '/right-sidebar.png',
					'left_sidebar'                => Estore_ADMIN_IMAGES_URL . '/left-sidebar.png',
					'no_sidebar_full_width'       => Estore_ADMIN_IMAGES_URL . '/no-sidebar-full-width-layout.png',
					'no_sidebar_content_centered' => Estore_ADMIN_IMAGES_URL . '/no-sidebar-content-centered-layout.png',
				),
			)
		)
	);

	// Default Single Post Layout.
	$wp_customize->add_section(
		'estore_archive_page_section',
		array(
			'priority' => 40,
			'title'    => esc_html__( 'Blog Layout', 'estore' ),
			'panel'    => 'estore_design_options',
		)
	);

	$wp_customize->add_setting(
		'estore_archive_page_style',
		array(
			'default'           => 'archive-list',
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'estore_sanitize_choices',
		)
	);

	$wp_customize->add_control(
		'estore_archive_page_style',
		array(
			'label'   => esc_html__( 'Choose the layout style for archive pages.', 'estore' ),
			'section' => 'estore_archive_page_section',
			'type'    => 'select',
			'choices' => array(
				'archive-list' => esc_html__( 'List View', 'estore' ),
				'archive-grid' => esc_html__( 'Grid View', 'estore' ),
			),
		)
	);

	if ( ! function_exists( 'wp_update_custom_css_post' ) ) {
		// Custom CSS Section.
		$wp_customize->add_section(
			'estore_custom_css_section',
			array(
				'priority' => 50,
				'title'    => esc_html__( 'Custom CSS', 'estore' ),
				'panel'    => 'estore_design_options',
			)
		);

		$wp_customize->add_setting(
			'estore_custom_css',
			array(
				'default'              => '',
				'capability'           => 'edit_theme_options',
				'sanitize_callback'    => 'wp_filter_nohtml_kses',
				'sanitize_js_callback' => 'wp_filter_nohtml_kses',
			)
		);

		$wp_customize->add_control(
			new eStore_Custom_CSS_Control(
				$wp_customize,
				'estore_custom_css',
				array(
					'label'   => esc_html__( 'Write your Custom CSS here', 'estore' ),
					'section' => 'estore_custom_css_section',
				)
			)
		);
	}

	// Footer Widget Section.
	$wp_customize->add_section(
		'estore_footer_widget_section',
		array(
			'priority' => 60,
			'title'    => esc_html__( 'Footer Widgets', 'estore' ),
			'panel'    => 'estore_design_options',
		)
	);

	$wp_customize->add_setting(
		'estore_footer_widgets',
		array(
			'default'           => 4,
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'estore_sanitize_integer',
		)
	);

	$wp_customize->add_control(
		'estore_footer_widgets',
		array(
			'label'   => esc_html__( 'Choose the number of widget area you want in footer', 'estore' ),
			'section' => 'estore_footer_widget_section',
			'type'    => 'select',
			'choices' => array(
				'1' => esc_html__( '1 Footer Widget Area', 'estore' ),
				'2' => esc_html__( '2 Footer Widget Area', 'estore' ),
				'3' => esc_html__( '3 Footer Widget Area', 'estore' ),
				'4' => esc_html__( '4 Footer Widget Area', 'estore' ),
			),
		)
	);

	/**
	 * Additional Options
	 */
	$wp_customize->add_panel(
		'estore_additional_options',
		array(
			'capability'  => 'edit_theme_options',
			'description' => esc_html__( 'Some additional settings.', 'estore' ),
			'priority'    => 180,
			'title'       => esc_html__( 'Additional Options', 'estore' ),
		)
	);

	// Author bio.
	$wp_customize->add_section(
		'estore_author_bio_section',
		array(
			'priority' => 7,
			'title'    => esc_html__( 'Author Bio Option', 'estore' ),
			'panel'    => 'estore_additional_options',
		)
	);

	$wp_customize->add_setting(
		'estore_author_bio_setting',
		array(
			'default'           => 0,
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'estore_sanitize_checkbox',
		)
	);

	$wp_customize->add_control(
		'estore_author_bio_setting',
		array(
			'type'    => 'checkbox',
			'label'   => esc_html__( 'Check to display the author bio.', 'estore' ),
			'setting' => 'estore_author_bio_setting',
			'section' => 'estore_author_bio_section',
		)
	);

	// Related post
	$wp_customize->add_section(
		'estore_related_posts_section',
		array(
			'priority' => 245,
			'title'    => esc_html__( 'Related Posts', 'estore' ),
			'panel'    => 'estore_additional_options',
		)
	);

	$wp_customize->add_setting(
		'estore_related_posts_activate',
		array(
			'default'           => 0,
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'estore_sanitize_checkbox',
		)
	);

	$wp_customize->add_control(
		'estore_related_posts_activate',
		array(
			'type'     => 'checkbox',
			'label'    => esc_html__( 'Check to activate the related posts', 'estore' ),
			'section'  => 'estore_related_posts_section',
			'settings' => 'estore_related_posts_activate',
		)
	);

	$wp_customize->add_setting(
		'estore_related_posts',
		array(
			'default'           => 'categories',
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'estore_sanitize_radio',
		)
	);

	$wp_customize->add_control(
		'estore_related_posts',
		array(
			'type'     => 'radio',
			'label'    => esc_html__( 'Related Posts Must Be Shown As:', 'estore' ),
			'section'  => 'estore_related_posts_section',
			'settings' => 'estore_related_posts',
			'choices'  => array(
				'categories' => esc_html__( 'Related Posts By Categories', 'estore' ),
				'tags'       => esc_html__( 'Related Posts By Tags', 'estore' ),
			),
		)
	);

	// Category Color Section.
	$wp_customize->add_section(
		'estore_category_color_setting',
		array(
			'priority' => 1,
			'title'    => esc_html__( 'Category Color Settings', 'estore' ),
			'panel'    => 'estore_additional_options',
		)
	);

	$priority         = 1;
	$categories       = get_terms( 'category' ); // Get all Categories.
	$wp_category_list = array();

	foreach ( $categories as $category_list ) {

		$wp_customize->add_setting(
			'estore_category_color_' . esc_html( strtolower( $category_list->name ) ),
			array(
				'default'              => '',
				'capability'           => 'edit_theme_options',
				'sanitize_callback'    => 'estore_hex_color_sanitize',
				'sanitize_js_callback' => 'estore_color_escaping_sanitize',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'estore_category_color_' . esc_html( strtolower( $category_list->name ) ),
				array(
					'label'    => sprintf( esc_html__( ' %s', 'estore' ), esc_html( $category_list->name ) ),
					'section'  => 'estore_category_color_setting',
					'settings' => 'estore_category_color_' . esc_html( strtolower( $category_list->name ) ),
					'priority' => $priority,
				)
			)
		);
		$priority ++;
	}

	// Post Meta Section.
	$wp_customize->add_section(
		'estore_postmeta_section',
		array(
			'priority'    => 30,
			'title'       => esc_html__( 'Post Meta Settings', 'estore' ),
			'panel'       => 'estore_additional_options',
			'description' => esc_html__( 'Note: This will only work in single posts.', 'estore' ),
		)
	);

	// Post Meta Setting.
	$wp_customize->add_setting(
		'estore_postmeta',
		array(
			'default'           => '',
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'estore_sanitize_checkbox',
		)
	);

	$wp_customize->add_control(
		'estore_postmeta',
		array(
			'label'    => esc_html__( 'Hide all post meta data under post title.', 'estore' ),
			'section'  => 'estore_postmeta_section',
			'priority' => 10,
			'type'     => 'checkbox',
		)
	);

	// Post Meta Date Setting.
	$wp_customize->add_setting(
		'estore_postmeta_date',
		array(
			'default'           => '',
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'estore_sanitize_checkbox',
		)
	);

	$wp_customize->add_control(
		'estore_postmeta_date',
		array(
			'label'    => esc_html__( 'Hide date under post title.', 'estore' ),
			'section'  => 'estore_postmeta_section',
			'priority' => 20,
			'type'     => 'checkbox',
		)
	);

	// Post Meta Author Setting.
	$wp_customize->add_setting(
		'estore_postmeta_author',
		array(
			'default'           => '',
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'estore_sanitize_checkbox',
		)
	);

	$wp_customize->add_control(
		'estore_postmeta_author',
		array(
			'label'    => esc_html__( 'Hide author under post title.', 'estore' ),
			'section'  => 'estore_postmeta_section',
			'priority' => 30,
			'type'     => 'checkbox',
		)
	);

	// Post Meta Comment Count Setting.
	$wp_customize->add_setting(
		'estore_postmeta_comment',
		array(
			'default'           => '',
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'estore_sanitize_checkbox',
		)
	);

	$wp_customize->add_control(
		'estore_postmeta_comment',
		array(
			'label'    => esc_html__( 'Hide comment count under post title.', 'estore' ),
			'section'  => 'estore_postmeta_section',
			'priority' => 40,
			'type'     => 'checkbox',
		)
	);

	// Post Meta Category Setting.
	$wp_customize->add_setting(
		'estore_postmeta_category',
		array(
			'default'           => '',
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'estore_sanitize_checkbox',
		)
	);

	$wp_customize->add_control(
		'estore_postmeta_category',
		array(
			'label'    => esc_html__( 'Hide category under post title.', 'estore' ),
			'section'  => 'estore_postmeta_section',
			'priority' => 50,
			'type'     => 'checkbox',
		)
	);

	// Post Meta Tags Setting.
	$wp_customize->add_setting(
		'estore_postmeta_tags',
		array(
			'default'           => '',
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'estore_sanitize_checkbox',
		)
	);

	$wp_customize->add_control(
		'estore_postmeta_tags',
		array(
			'label'    => esc_html__( 'Hide tags under post title.', 'estore' ),
			'section'  => 'estore_postmeta_tags',
			'priority' => 60,
			'type'     => 'checkbox',
		)
	);

	// Payment Partners Logo Section.
	$wp_customize->add_section(
		'estore_payment_logo_section',
		array(
			'priority' => 40,
			'title'    => esc_html__( 'Payment Partners Logo', 'estore' ),
			'panel'    => 'estore_additional_options',
		)
	);

	for ( $i = 1; $i < 5; $i ++ ) {
		// Logo Upload
		$wp_customize->add_setting(
			'estore_payment_logo' . $i,
			array(
				'default'           => '',
				'capability'        => 'edit_theme_options',
				'transport'         => $customizer_selective_refresh,
				'sanitize_callback' => 'esc_url_raw',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Image_Control(
				$wp_customize,
				'estore_payment_logo' . $i,
				array(
					'label'   => esc_html__( 'Upload logo', 'estore' ) . $i,
					'section' => 'estore_payment_logo_section',
					'setting' => 'estore_payment_logo' . $i,
				)
			)
		);

		// Selective refresh for payment logo.
		if ( isset( $wp_customize->selective_refresh ) ) {
			$wp_customize->selective_refresh->add_partial(
				'estore_payment_logo' . $i,
				array(
					'selector'        => '.payment-partner-wrapper',
					'render_callback' => '',
				)
			);
		}
	}

	// Active Callback for Retina Logo.
	function estore_retina_logo() {
		if ( get_theme_mod( 'estore_different_retina_logo', 0 ) == 1 ) {
			return true;
		}

		return false;
	}

	// Check if WPML Active.
	function estore_is_wpml_activate() {
		if ( function_exists( 'icl_object_id' ) ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Sanitize callbacks
	 */
	function estore_sanitize_select( $input, $setting ) {
		// check for valid key
		$input = sanitize_key( $input );

		// Get all choices from control
		$choices = $setting->manager->get_control( $setting->id )->choices;

		// return selected input: if valid, default value if invalid
		return ( array_key_exists( $input, $choices ) ? $input : $setting->default );
	}

	// Sanitize Radio Button.
	function estore_sanitize_radio( $input, $setting ) {
		// Ensure input is a slug.
		$input = sanitize_key( $input );

		// Get list of choices from the control associated with the setting.
		$choices = $setting->manager->get_control( $setting->id )->choices;

		// If the input is a valid key, return it; otherwise, return the default.
		return ( array_key_exists( $input, $choices ) ? $input : $setting->default );
	}

	// Sanitize Checkbox.
	function estore_sanitize_checkbox( $input ) {
		if ( 1 == $input ) {
			return 1;
		} else {
			return '';
		}
	}

	// Sanitize Integer.
	function estore_sanitize_integer( $input ) {
		if ( is_numeric( $input ) ) {
			return intval( $input );
		}
	}

	// Sanitize Text.
	function estore_sanitize_text( $input ) {
		return wp_kses_post( force_balance_tags( $input ) );
	}

	// Sanitize Color.
	function estore_hex_color_sanitize( $color ) {
		if ( $unhashed = sanitize_hex_color_no_hash( $color ) ) {
			return '#' . $unhashed;
		}

		return $color;
	}

	// Escape Color.
	function estore_color_escaping_sanitize( $input ) {
		$input = esc_attr( $input );

		return $input;
	}

	// Sanitize Choices.
	function estore_sanitize_choices( $input, $setting ) {
		global $wp_customize;

		$control = $wp_customize->get_control( $setting->id );

		if ( array_key_exists( $input, $control->choices ) ) {
			return $input;
		} else {
			return $setting->default;
		}
	}
}

add_action( 'customize_register', 'estore_customize_register' );

/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
 *
 * @since eStore 1.2.3
 */
function estore_customize_preview_js() {
	wp_enqueue_script(
		'estore-customizer',
		get_template_directory_uri() . '/js/customizer.js',
		array( 'customize-preview' ),
		false,
		true
	);
}

add_action( 'customize_preview_init', 'estore_customize_preview_js' );

/**
 * Render the site title for the selective refresh partial.
 *
 * @return void
 */
function estore_customize_partial_blogname() {
	bloginfo( 'name' );
}

/**
 * Render the site tagline for the selective refresh partial.
 *
 * @return void
 */
function estore_customize_partial_blogdescription() {
	bloginfo( 'description' );
}

// Function for top header text selective refresh support
function estore_bar_text() {
	$header_bar_text = get_theme_mod( 'estore_bar_text' );
	echo wp_kses_post( $header_bar_text );
}


/*
 * Custom Scripts
 */
add_action( 'customize_controls_print_footer_scripts', 'estore_customizer_custom_scripts' );

function estore_customizer_custom_scripts() {
	?>
	<style>
		/* Theme Instructions Panel CSS */
		li#accordion-section-estore_upsell_section h3.accordion-section-title {
			background-color: #289DCC !important;
			border-left-color: #0073aa !important;
		}

		#accordion-section-estore_upsell_section h3 a:after {
			content: '\f345';
			color: #fff;
			position: absolute;
			top: 12px;
			right: 10px;
			z-index: 1;
			font: 400 20px/1 dashicons;
			speak: none;
			display: block;
			-webkit-font-smoothing: antialiased;
			-moz-osx-font-smoothing: grayscale;
			text-decoration: none!important;
		}

		li#accordion-section-estore_upsell_section h3.accordion-section-title a {
			display: block;
			color: #fff !important;
			text-decoration: none;
		}

		li#accordion-section-estore_upsell_section h3.accordion-section-title a:focus {
			box-shadow: none;
		}

		li#accordion-section-estore_upsell_section h3.accordion-section-title:hover {
			background-color: #1b90bf !important;
			color: #fff !important;
		}

		li#accordion-section-estore_upsell_section h3.accordion-section-title:after {
			color: #fff !important;
		}

		/* Upsell button CSS */
		.customize-control-estore-important-links a {
			/* Permalink - use to edit and share this gradient: http://colorzilla.com/gradient-editor/#8fc800+0,8fc800+100;Green+Flat+%232 */
			background: #008EC2;
			color: #fff;
			display: block;
			margin: 15px 0 0;
			padding: 5px 0;
			text-align: center;
			font-weight: 600;
		}

		.customize-control-estore-important-links a {
			padding: 8px 0;
		}

		.customize-control-estore-important-links a:hover {
			color: #ffffff;
			/* Permalink - use to edit and share this gradient: http://colorzilla.com/gradient-editor/#006e2e+0,006e2e+100;Green+Flat+%233 */
			background: #2380BA;
		}
	</style>

	<script>
		( function ( $, api ) {
			api.sectionConstructor['estore-upsell-section'] = api.Section.extend( {

				// No events for this type of section.
				attachEvents : function () {
				},

				// Always make the section active.
				isContextuallyActive : function () {
					return true;
				}
			} );
		} )( jQuery, wp.customize );

	</script>
	<?php
}
