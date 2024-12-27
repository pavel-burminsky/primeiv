<?php

/**
 * Fired when the plugin is uninstalled.
 *
 * When populating this file, consider the following flow
 * of control:
 *
 * - This method should be static
 * - Check if the $_REQUEST content actually is the plugin name
 * - Run an admin referrer check to make sure it goes through authentication
 * - Verify the output of $_GET makes sense
 * - Repeat with other user roles. Best directly by using the links/query string parameters.
 * - Repeat things for multisite. Once for a single site in the network, once sitewide.
 *
 * This file may be updated more in future version of the Boilerplate; however, this is the
 * general skeleton and outline for how the file should work.
 *
 * For more information, see the following discussion:
 * https://github.com/tommcfarlin/WordPress-Plugin-Boilerplate/pull/123#issuecomment-28541913
 *
 * @package WP_Business_Reviews
 * @since   0.1.0
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

if ( 'remove' === get_option( 'wpbr_uninstall_behavior' ) ) {
	global $wpdb, $wp_roles;
	$wpbr_taxonomies = array( 'wpbr_platform', 'wpbr_rating', 'wpbr_attribute', 'wpbr_review_tag' );
	$wpbr_post_types = array( 'wpbr_collection', 'wpbr_review', 'wpbr_review_source' );

	// Delete all plugin posts.
	foreach ( $wpbr_post_types as $post_type ) {
		$args = array(
			'post_type'      => $post_type,
			'post_status'    => array( 'any', 'trash' ),
			'posts_per_page' => - 1,
			'fields'         => 'ids',
		);

		$query      = new \WP_Query( $args );
		$wpbr_posts = $query->posts;

		if ( ! empty( $wpbr_posts ) ) {
			foreach ( $wpbr_posts as $wpbr_post ) {
				wp_delete_post( $wpbr_post, true );
			}
		}
	}

	// Delete all plugin terms and taxonomies.
	foreach ( $wpbr_taxonomies as $taxonomy ) {
		$terms = $wpdb->get_results( $wpdb->prepare( "SELECT t.*, tt.* FROM $wpdb->terms AS t INNER JOIN $wpdb->term_taxonomy AS tt ON t.term_id = tt.term_id WHERE tt.taxonomy IN ('%s') ORDER BY t.name ASC", $taxonomy ) );

		if ( $terms ) {
			foreach ( $terms as $term ) {
				$wpdb->delete( $wpdb->term_taxonomy, array( 'term_taxonomy_id' => $term->term_taxonomy_id ) );
				$wpdb->delete( $wpdb->terms, array( 'term_id' => $term->term_id ) );
			}
		}

		$wpdb->delete( $wpdb->term_taxonomy, array( 'taxonomy' => $taxonomy ), array( '%s' ) );
	}

	// Delete all plugin options.
	$wpbr_option_names = $wpdb->get_col(
		$wpdb->prepare(
			"SELECT option_name FROM {$wpdb->options} where option_name LIKE '%%%s%%'",
			'wpbr'
		)
	);

	if ( ! empty( $wpbr_option_names ) ) {
		foreach ( $wpbr_option_names as $option ) {
			delete_option( $option );
		}
	}
}
