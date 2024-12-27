<?php
/**
 * Defines functions for updating data to be executed by the background updater.
 *
 * @package WP_Business_Reviews\Includes
 * @since 1.2.0
 *
 * @see WP_Business_Reviews\Includes\Admin\Database_Updater
 * @link https://wpbusinessreviews.com
 */

defined( 'ABSPATH' ) || exit;

/**
 * Adds normalized rating as post meta for each review based on 5-star scale.
 *
 * Normalized ratings were previously stored as taxonomy terms, which did not
 * allow for sorting. Moving normalized ratings to post meta allows reviews to
 * be sorted using a meta query.
 *
 * @since 1.2.0
 */
function wpbr_v1_2_0_normalize_review_ratings() {
	$args = array(
		'fields'         => 'ids',
		'meta_compare'   => 'NOT EXISTS',
		'meta_key'       => 'wpbr_rating_normal',
		'posts_per_page' => -1,
		'post_status'    => array( 'any', 'trash' ),
		'post_type'      => 'wpbr_review',
	);
	$post_ids = get_posts( $args );

	if ( empty( $post_ids ) ) {
		return;
	}

	// Add normalized rating as review post meta.
	foreach ( $post_ids as $post_id ) {
		$rating            = get_post_meta( $post_id, 'wpbr_rating', true );
		$normalized_rating = absint( $rating ) * 20;
		update_post_meta( $post_id, 'wpbr_rating_normal', $normalized_rating );
	}
}

/**
 * Updates collections to show recommendations and normalize minimum ratings.
 *
 * Minimum ratings were previously stored on a scale from 1-5, which is
 * problematic if the collection includes reviews with different rating scales.
 * Normalizing the minimum ratings allows all
 *
 * @since 1.2.0
 */
function wpbr_v1_2_0_update_collections() {
	$args = array(
		'fields'         => 'ids',
		'posts_per_page' => -1,
		'post_status'    => array( 'any', 'trash' ),
		'post_type'      => 'wpbr_collection',
	);
	$post_ids = get_posts( $args );

	if ( empty( $post_ids ) ) {
		return;
	}

	// Add normalized minimum rating as collection post meta.
	foreach ( $post_ids as $post_id ) {
		$min_rating = get_post_meta( $post_id, 'wpbr_min_rating', true );
		$components = get_post_meta( $post_id, 'wpbr_review_components', true );

		// Ensure recommendations are displayed by default.
		$components['recommendation'] = 'enabled';
		update_post_meta( $post_id, 'wpbr_review_components', $components );

		// Only normalize rating if it is based on 5-star scale.
		if ( 1 < $min_rating && 5 >= $min_rating ) {
			$normalized_min_rating = absint( $min_rating ) * 20;
			update_post_meta( $post_id, 'wpbr_min_rating', $normalized_min_rating );
		}
	}
}

/**
 * Updates the database version to indicate that v1.2.0 updates are complete.
 *
 * @since 1.2.0
 */
function wpbr_v1_2_0_update_db_version() {
	update_option( 'wpbr_db_version', '1.2.0' );
}
