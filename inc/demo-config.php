<?php
/**
 * Functions for configuring demo importer.
 *
 * @package Importer/Functions
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Setup demo importer config.
 *
 * @deprecated 1.5.0
 *
 * @param  array $demo_config Demo config.
 * @return array
 */
function estore_demo_importer_packages( $packages ) {
	$new_packages = array(
		// Free theme demo
		'estore-free'        => array(
			'name'    => esc_html__( 'eStore', 'estore' ),
			'preview' => 'https://demo.themegrill.com/estore/',
		),
		// Pro theme demos link
		'estore-pro-default' => array(
			'name'     => esc_html__( 'eStore Pro Default', 'estore' ),
			'preview'  => 'https://demo.themegrill.com/estore-pro/',
			'pro_link' => 'https://demo.themegrill.com/estore/'
		),
		'estore-pro-fashion' => array(
			'name'     => esc_html__( 'eStore Pro Fashion', 'estore' ),
			'preview'  => 'https://demo.themegrill.com/estore-pro-fashion',
			'pro_link' => 'https://demo.themegrill.com/estore/'
		),
		'estore-pro-tech'    => array(
			'name'     => esc_html__( 'eStore Pro Tech', 'estore' ),
			'preview'  => 'https://demo.themegrill.com/estore-pro-tech/',
			'pro_link' => 'https://demo.themegrill.com/estore/'
		),
	);

	return array_merge( $new_packages, $packages );
}

add_filter( 'themegrill_demo_importer_packages', 'estore_demo_importer_packages' );
