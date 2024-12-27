<?php
/**
 * Defines the admin pages config.
 *
 * @package WP_Business_Reviews\Config
 * @since   0.1.0
 */

namespace WP_Business_Reviews\Config;

/**
 * Filters the admin pages config.
 *
 * @since 0.1.0
 *
 * @param array $config Admin pages config containing menu and submenu pages.
 */
$config = apply_filters(
	'wpbr_config_admin_pages',
	array(
		'builder' => array(
			'page_parent' => 'wpbr',
			'page_title' => __( 'Builder', 'wp-business-reviews' ),
			'menu_title' => __( 'Builder', 'wp-business-reviews' ),
			'capability' => 'edit_posts',
			'menu_slug'  => 'wpbr-builder',
		),
		'settings' => array(
			'page_parent' => 'wpbr',
			'page_title' => __( 'Settings', 'wp-business-reviews' ),
			'menu_title' => __( 'Settings', 'wp-business-reviews' ),
			'capability' => 'manage_options',
			'menu_slug'  => 'wpbr-settings',
		),
		'system_info' => array(
			'page_parent' => 'wpbr',
			'page_title' => __( 'System Info', 'wp-business-reviews' ),
			'menu_title' => __( 'System Info', 'wp-business-reviews' ),
			'capability' => 'manage_options',
			'menu_slug'  => 'wpbr-system-info',
		),
	)
);

return $config;
