<?php
/**
 * Defines the Review_Source_Serializer class
 *
 * @link https://wpbusinessreviews.com
 *
 * @package WP_Business_Reviews\Includes\Serializer
 * @since 0.1.0
 */

namespace WP_Business_Reviews\Includes\Serializer;

/**
 * Saves reviews to the database.
 *
 * @since 0.1.0
 */
class Review_Source_Serializer extends Post_Serializer {
	/**
	 * The post type being saved.
	 *
	 * @since 0.1.0
	 * @var string $post_type
	 */
	protected $post_type = 'wpbr_review_source';

	/**
	 * Registers functionality with WordPress hooks.
	 *
	 * @since 0.1.0
	 */
	public function register() {
		add_action( 'admin_post_wpbr_collection_save', array( $this, 'save_from_post_json' ), 10 );
	}

	/**
	 * Prepares the post data in a ready-to-save format.
	 *
	 * @since 0.1.0
	 *
	 * @param array $raw_data Raw, unstructured post data.
	 * @return array Array of elements that make up a post.
	 */
	public function prepare_post_array( array $raw_data ) {
		$platform      = '';
		$rating        = 0;
		$rating_normal = 0;

		// Define the raw data ($r) from which a post will be created.
		$r = $raw_data;

		// Check for duplicate review source.
		$duplicate = $this->get_duplicate( $r );

		// Bail early if review source already exists.
		if ( $duplicate ) {
			/** This action is documented in includes/serializer/post-serializer.php */
			do_action( "{$this->post_type}_determine_post_id", $duplicate );

			// Bail early because review source already exists.
			return array();
		}

		// Define the post array ($p) that will hold all post elements.
		$p = array(
			'post_type'   => $this->post_type,
			'post_status' => 'publish',
		);

		// Set platform.
		if ( isset( $r['platform'] ) ) {
			$platform                        = $this->clean( $r['platform'] );
			$p['tax_input']['wpbr_platform'] = $platform;
		}

		// Process rating.
		if ( isset( $r['components']['rating'] ) ) {
			$rating = $this->clean( $r['components']['rating'] );
			unset( $r['components']['rating'] );

			$rating_normal = $this->normalize_rating(
				$rating,
				$platform
			);
		}

		// Set rating and normalized rating.
		$p['meta_input']["{$this->prefix}rating"]        = $rating;
		$p['meta_input']["{$this->prefix}rating_normal"] = $rating_normal;

		if ( isset( $r['review_source_id'] ) ) {
			$p['meta_input']["{$this->prefix}review_source_id"] = $this->clean(
				$r['review_source_id']
			);
		}

		if ( isset( $r['components']['name'] ) ) {
			$p['post_title'] = $this->clean( $r['components']['name'] );
			unset( $r['components']['name'] );
		}

		if ( isset( $r['components']['location'] ) ) {
			if ( isset( $r['components']['location']['formatted_address'] ) ) {
				$p['meta_input']["{$this->prefix}formatted_address"] = $this->clean(
					$r['components']['location']['formatted_address']
				);
			}

			if ( isset( $r['components']['location']['address'] ) ) {
				foreach ( $r['components']['location']['address'] as $key => $value ) {
					$p['meta_input']["{$this->prefix}{$key}"] = $this->clean( $value );
				}
			}

			if ( isset( $r['components']['location']['coordinates'] ) ) {
				foreach ( $r['components']['location']['coordinates'] as $key => $value ) {
					$p['meta_input']["{$this->prefix}{$key}"] = $this->clean( $value );
				}
			}

			if ( isset( $r['components']['location']['phone'] ) ) {
				$p['meta_input']["{$this->prefix}phone"] = $this->clean(
					$r['components']['location']['phone']
				);
			}

			unset( $r['components']['location'] );
		}

		// Store all remaining components as post meta.
		if ( isset( $r['components'] ) ) {
			foreach ( $r['components'] as $key => $value ) {
				if ( null !== $value ) {
					$p['meta_input']["{$this->prefix}{$key}"] = $this->clean( $value );
				}
			}
		}

		return $p;
	}

	/**
	 * Determines if a review source is a duplicate.
	 *
	 * A review source is considered a duplicate if an existing post is found with
	 * the same review source ID.
	 *
	 * @since 1.1.0
	 *
	 * @param array $review Array of review data from the platform API.
	 * @return int|bool Post ID of the existing post if the new post source is a
	 *             duplicate, false otherwise.
	 */
	protected function get_duplicate( $review_source ) {
		$args = array(
			'no_found_rows'          => true,
			'posts_per_page'         => 1,
			'post_status'            => array( 'any', 'trash' ),
			'post_type'              => $this->post_type,
			'update_post_meta_cache' => false,
		);

		// Only check for dupes if review source ID is set.
		if ( ! empty( $review_source['review_source_id'] ) ) {
			$args['meta_query']['wpbr_review_source_id'] = array(
				'key'   => 'wpbr_review_source_id',
				'value' => $review_source['review_source_id'],
			);
		} else {
			return false;
		}

		$posts = get_posts( $args );

		if ( ! empty( $posts ) ) {
			return $posts[0]->ID;
		}

		return false;
	}
}
