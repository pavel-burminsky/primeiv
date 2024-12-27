<?php
/**
 * Defines the Collection_Deserializer class
 *
 * @link https://wpbusinessreviews.com
 *
 * @package WP_Business_Reviews\Includes\Deserializer
 * @since 0.1.0
 */

namespace WP_Business_Reviews\Includes\Deserializer;

use WP_Business_Reviews\Includes\Collection;

/**
 * Retrieves collections from the database.
 *
 * @since 0.1.0
 */
class Collection_Deserializer extends Post_Deserializer {
	/**
	 * The post type being retrieved.
	 *
	 * @since 0.1.0
	 * @var string $post_type
	 */
	protected $post_type = 'wpbr_collection';

	/**
	 * Retriever of review sources within the collection.
	 *
	 * @since 0.1.0
	 * @var Review_Source_Deserializer $review_source_deserializer
	 */
	protected $review_source_deserializer;

	/**
	 * Retriever of reviews within the collection.
	 *
	 * @since 0.1.0
	 * @var Review_Deserializer $review_deserializer
	 */
	protected $review_deserializer;

	/**
	 * Instantiates the Collection_Deserializer object.
	 *
	 * @since 0.1.0
	 *
	 * @param WP_Query The WP_Query object used to query collection posts.
	 * @param Review_Source_Deserializer $review_source_deserializer Retriever of review sources.
	 * @param Review_Deserializer $review_deserializer Retriever of reviews.
	 */
	public function __construct(
		\WP_Query $wp_query,
		Review_Source_Deserializer $review_source_deserializer,
		Review_Deserializer $review_deserializer
	) {
		$this->wp_query = $wp_query;
		$this->review_source_deserializer = $review_source_deserializer;
		$this->review_deserializer        = $review_deserializer;
	}

	/**
	 * Gets a single Collection object.
	 *
	 * @since 0.2.0 Added `$args` as second parameter.
	 * @since 0.1.0
	 *
	 * @see WP_Query For acceptable query arguments.
	 *
	 * @param string $post_id ID of the post to retrieve.
	 * @param array  $args    Optional. Array of query arguments.
	 * @return Collection|false Collection object or false if not found.
	 */
	public function get_collection( $post_id, $args = array() ) {
		$post = $this->get_post( $post_id, $args );

		if ( false === $post ) {
			return false;
		}

		$collection = $this->convert_post_to_collection( $post );

		return $collection;
	}

	/**
	 * Queries Collection objects.
	 *
	 * @since 0.1.0
	 *
	 * @param string|array $args URL query string or array of vars.
	 * @return Collection[]|false Array of Collection objects or false
	 *                                   if no posts found.
	 */
	public function query_collections( $args = '' ) {
		$collections = array();

		/**
		 * Filters the arguments used when querying collections.
		 *
		 * @since 1.2.0
		 *
		 * @link https://codex.wordpress.org/Class_Reference/WP_Query#Parameters
		 *
		 * @param array $args Array of query args.
		 */
		$args = apply_filters( 'wpbr_collection_query_args', $args );

		$posts = $this->query_posts( $args );

		if ( empty( $posts ) ) {
			return false;
		}

		foreach ( $posts as $post ) {
			$collections[] = $this->convert_post_to_collection( $post );
		}

		return $collections;
	}

	/**
	 * Converts WP_Post object into Collection object.
	 *
	 * @since 0.1.0
	 *
	 * @param WP_Post $post The WP_Post object to be converted.
	 * @return Collection The new Collection object.
	 */
	protected function convert_post_to_collection( $post ) {
		$platform = $this->get_platform( $post );
		$post_id  = $post->ID;
		$title    = $post->post_title;
		$settings = Collection::get_default_settings();

		// Populate settings from post object.
		foreach ( $settings as $key => $default_value ) {
			if ( 'post_parent' === $key ) {
				$value = $post->post_parent;
			} elseif ( is_numeric( $default_value ) ) {
				$value = intval( $this->get_meta( $post_id, $key ) );
			} else {
				$value = $this->get_meta( $post_id, $key );
			}

			$settings[ $key ] = $value;
		}

		$collection = new Collection(
			$post_id,
			$platform,
			$title,
			$settings
		);

		return $collection;
	}

	public function hydrate_review_sources( $collection ) {
		$settings = $collection->get_settings();
		$review_sources = $this->review_source_deserializer->query_review_sources(
			array(
				'post__in' => array(
					$settings['post_parent'],
				),
			)
		);

		$collection->set_review_sources( $review_sources );

		return $collection;
	}

	public function hydrate_reviews( $collection ) {
		$settings = $collection->get_settings();
		$reviews = $this->review_deserializer->query_reviews( $settings );

		$collection->set_reviews( $reviews );

		return $collection;
	}
}
