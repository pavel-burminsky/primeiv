<?php
/**
 * Defines the Review_Source_Deserializer class
 *
 * @link https://wpbusinessreviews.com
 *
 * @package WP_Business_Reviews\Includes\Deserializer
 * @since 0.1.0
 */

namespace WP_Business_Reviews\Includes\Deserializer;

use WP_Business_Reviews\Includes\Review_Source;
use WP_Business_Reviews\Includes\Location;

/**
 * Retrieves review sources from the database.
 *
 * @since 0.1.0
 */
class Review_Source_Deserializer extends Post_Deserializer {
	/**
	 * The post type being retrieved.
	 *
	 * @since 0.1.0
	 * @var string $post_type
	 */
	protected $post_type = 'wpbr_review_source';

	/**
	 * Gets a single Review_Source object.
	 *
	 * @since 0.1.0
	 *
	 * @param string $post_id ID of the post to retrieve.
	 * @return Review_Source|false Review_Source object or false if not found.
	 */
	public function get_review_source( $post_id ) {
		$post = $this->get_post( $post_id );

		if ( false === $post ) {
			return false;
		}

		$review_source = $this->convert_post_to_review_source( $post );

		return $review_source;
	}

	/**
	 * Queries Review_Source objects.
	 *
	 * @since 0.1.0
	 *
	 * @param string|array $args Optional. URL query string or array of vars.
	 * @return Review_Source[]|false Array of Review_Source objects or false
	 *                                   if no posts found.
	 */
	public function query_review_sources( $args = '' ) {
		$review_sources = array();

		/**
		 * Filters the arguments used when querying review sources.
		 *
		 * @since 1.2.0
		 *
		 * @link https://codex.wordpress.org/Class_Reference/WP_Query#Parameters
		 *
		 * @param array $args Array of query args.
		 */
		$args = apply_filters( 'wpbr_review_source_query_args', $args );

		$posts = $this->query_posts( $args );

		if ( ! empty( $posts ) ) {
			foreach ( $posts as $post ) {
				$review_sources[] = $this->convert_post_to_review_source( $post );
			}
		}

		return $review_sources;
	}

	/**
	 * Converts WP_Post object into Review_Source object.
	 *
	 * @since 0.1.0
	 *
	 * @param WP_Post $post The WP_Post object to be converted.
	 * @return Review_Source The new Review_Source object.
	 */
	protected function convert_post_to_review_source( $post ) {
		$post_id          = $post->ID;
		$review_source_id = $this->get_meta( $post_id, 'review_source_id' );
		$components       = Review_Source::get_default_components();

		// Map meta keys to components.
		foreach ( array_keys( $components ) as $key ) {
			$components[ $key ] = $this->get_meta( $post_id, $key );
		}

		// Set name from post title.
		$components['name'] = $post->post_title;

		// Set location.
		$components['location'] = new Location(
			$this->get_meta( $post_id, 'formatted_address' ),
			array(
				$this->get_meta( $post_id, 'street_address' ),
				$this->get_meta( $post_id, 'city' ),
				$this->get_meta( $post_id, 'state_province' ),
				$this->get_meta( $post_id, 'postal_code' ),
				$this->get_meta( $post_id, 'country' ),
			),
			array(
				$this->get_meta( $post_id, 'latitude' ),
				$this->get_meta( $post_id, 'longitude' ),
			),
			$this->get_meta( $post_id, 'phone' )
		);

		// Create review source.
		$review_source = new Review_Source(
			$post_id,
			$this->get_platform( $post ),
			$review_source_id,
			$components
		);

		return $review_source;
	}
}
