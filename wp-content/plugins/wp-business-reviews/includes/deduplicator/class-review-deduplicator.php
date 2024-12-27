<?php
/**
 * Defines the Review_Deduplicator class
 *
 * @link https://wpbusinessreviews.com
 *
 * @package WP_Business_Reviews\Includes\Deduplicator
 * @since 1.3.0
 */

namespace WP_Business_Reviews\Includes\Deduplicator;

use WP_Business_Reviews\Includes\Review;

/**
 * Handles duplicate reviews.
 *
 * @since 1.3.0
 */
class Review_Deduplicator {
	/**
	 * Unsets duplicate reviews if they already exist locally.
	 *
	 * @since 1.3.0
	 *
	 * @param Review[] $reviews Array of review objects.
	 * @param int      $post_parent Post ID of the review source with which the
	 *                              provided review objects are associated.
	 * @return Review[] Array of deduplicated review objects.
	 */
	public static function deduplicate( $reviews, $post_parent ) {
		foreach( $reviews as $i => $review ) {
			if ( self::is_duplicate( $review, $post_parent ) ) {
				unset( $reviews[ $i ] );
			}
		}

		return $reviews;
	}

	/**
	 * Determines if a review already exists locally.
	 *
	 * @since 1.3.0
	 *
	 * @param Review[] $reviews Array of review objects.
	 * @param int      $post_parent Post ID of the review source with which the
	 *                              provided review object is associated.
	 * @return Review[] Array of deduplicated review objects.
	 */
	public static function is_duplicate( Review $review, $post_parent ) {
		return (bool) self::get_duplicate( $review, $post_parent );
	}

	/**
	 * Gets a duplicate review.
	 *
	 * A review is considered a duplicate if an existing post is found with the
	 * same post parent and identical timestamp or reviewer name.
	 *
	 * @since 1.3.0
	 *
	 * @param Review $review      A review object.
	 * @param int    $post_parent Post ID of the review source with which the
	 *                            provided review object is associated.
	 * @return int Post ID of the existing post if duplicate, 0 otherwise.
	 */
	public static function get_duplicate( Review $review, $post_parent ) {
		$post_id       = 0;
		$timestamp     = $review->get_component( 'timestamp' );
		$reviewer_name = $review->get_component( 'reviewer_name' );

		// Bail if neither timestamp or reviewer name is available.
		if ( ! $timestamp && ! $reviewer_name ) {
			return $post_id;
		}

		// Set WP_Query args for the most performant query of a single post.
		$args = array(
			'fields'                 => 'ids',
			'no_found_rows'          => true,
			'posts_per_page'         => 1,
			'post_parent'            => $post_parent,
			'post_type'              => 'wpbr_review',
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
		);

		// Set meta query to look for matching timestamp or reviewer name.
		$args['meta_query'] = array(
			'relation'         => 'OR',
			'timestamp_clause' => array(
				'key'   => 'wpbr_timestamp',
				'value' => $timestamp,
			),
			'reviewer_name_clause' => array(
				'key'   => 'wpbr_reviewer_name',
				'value' => $reviewer_name,
			),
		);

		$query = new \WP_Query( $args );
		$posts = $query->posts;

		if ( ! empty( $posts ) ) {
			$post_id = $posts[0];
		}

		return $post_id;
	}
}
