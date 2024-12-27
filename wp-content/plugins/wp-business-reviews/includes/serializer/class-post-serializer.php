<?php
/**
 * Defines the Post_Serializer class
 *
 * @link https://wpbusinessreviews.com
 *
 * @package WP_Business_Reviews\Includes\Serializer
 * @since 0.1.0
 */

namespace WP_Business_Reviews\Includes\Serializer;

/**
 * Saves posts to the database.
 *
 * @since 0.1.0
 */
class Post_Serializer extends Serializer_Abstract {
	/**
	 * The post type being saved.
	 *
	 * @since 0.1.0
	 * @var string $post_type
	 */
	protected $post_type = 'post';

	/**
	 * The parent post ID of the post being saved.
	 *
	 * @since 0.1.0
	 * @var string $post_parent
	 */
	protected $post_parent = 0;

	/**
	 * Saves a single review to the database.
	 *
	 * @since 0.1.0
	 *
	 * @param array $post_array Array of elements that make up a post.
	 * @return int|WP_Error The post ID on success. The value 0 or WP_Error
	 *                          on failure.
	 */
	function save( array $post_array ) {
		$tax_input = array();

		// Pull out terms because they cannot be assigned via WP Cron.
		// @see https://core.trac.wordpress.org/ticket/19373#comment:48
		if ( ! empty( $post_array['tax_input'] ) ) {
			$tax_input = $post_array['tax_input'];
			unset( $post_array['tax_input'] );
		}

		$post_id = wp_insert_post( $post_array );

		if ( is_numeric( $post_id) && 0 < $post_id ) {
			// Assign terms in a way that works with WP Cron.
			foreach( $tax_input as $taxonomy => $terms ) {
				wp_set_post_terms( $post_id, $terms, $taxonomy  );
			}

			if ( 'admin_post_wpbr_collection_save' === current_action() ) {
				/**
				 * Fires after the saved post ID has been determined.
				 *
				 * @since 0.1.0
				 *
				 * @param int $post_id ID of the saved post.
				 */
				do_action( "{$this->post_type}_determine_post_id", $post_id );

				if ( 'wpbr_collection' === $post_array['post_type'] ) {
					wp_safe_redirect(
						add_query_arg( array(
							'wpbr_notice'        => 'collection_saved',
							'wpbr_collection_id' => $post_id,
						), wp_get_referer() )
					);
					exit;
				}
			}
		}

		return $post_id;
	}

	/**
	 * Saves multiple posts to the database.
	 *
	 * @since 1.3.0 Return array of saved post IDs.
	 * @since 0.1.0
	 *
	 * @param array $posts_array Array of post arrays.
	 * @return array Successfully saved post IDS.
	 */
	public function save_multiple( array $posts_array ) {
		$saved_post_ids = array();

		foreach ( $posts_array as $post_array ) {
			$post_id = $this->save( $post_array );

			if ( is_numeric( $post_id) && 0 < $post_id ) {
				$saved_post_ids[] = $post_id;
			}
		}

		return $saved_post_ids;
	}

	/**
	 * Saves WordPress posts from stringified JSON in $_POST data.
	 *
	 * This method should be used if the posted data is in the form of
	 * stringified JSON. If so, it is decoded into an array before saving.
	 *
	 * @since 0.1.0
	 */
	public function save_from_post_json() {
		if ( empty( $_POST[ $this->post_type ] ) ) {
			return;
		}

		check_admin_referer( "{$this->post_type}_save", "{$this->post_type}_nonce" );

		if ( ! current_user_can( 'edit_posts') ) {
			wp_die( __( 'Sorry, you are not allowed to save posts.' ), 'wp-business-reviews' );
		}

		$raw_data_json  = wp_unslash( $_POST[ $this->post_type ] );
		$raw_data_array = json_decode( $raw_data_json, true );

		if ( is_array( current( $raw_data_array ) ) ) {
			// More than one post is being saved.
			$reversed = array_reverse( $raw_data_array );

			$posts_array = array();

			foreach ( $reversed as $item ) {
				$posts_array[] = $this->prepare_post_array( $item );
			}

			$this->save_multiple( $posts_array );
		} else {
			// Only one post is being saved.
			$post_array = $this->prepare_post_array( $raw_data_array );
			$this->save( $post_array );
		}
	}

	/**
	 * Saves WordPress posts from array in $_POST data.
	 *
	 * This method should be used if the posted data is in the form of an array.
	 *
	 * @since 0.1.0
	 */
	public function save_from_post_array() {
		if ( empty( $_POST[ $this->post_type ] ) ) {
			return;
		}

		check_admin_referer( "{$this->post_type}_save", "{$this->post_type}_nonce" );

		if ( ! current_user_can( 'edit_posts') ) {
			wp_die( __( 'Sorry, you are not allowed to save posts.' ), 'wp-business-reviews' );
		}

		$raw_data_array = wp_unslash( $_POST[ $this->post_type ] );

		if (
			is_array( current( $raw_data_array ) )
			&& isset( current( $raw_data_array )['components'] )
		) {
			// More than one post is being saved.
			$posts_array = array();

			foreach ( $raw_data_array as $data ) {
				$posts_array[] = $this->prepare_post_array( $data );
			}

			$this->save_multiple( $posts_array );
		} else {
			// Only one post is being saved.
			$post_array = $this->prepare_post_array( $raw_data_array );
			$this->save( $post_array );
		}
	}

	/**
	 * Sets the post parent.
	 *
	 * @since 0.1.0
	 *
	 * @param int $post_id The post parent ID.
	 */
	public function set_post_parent( $post_id ) {
		$this->post_parent = $post_id;
	}

	/**
	 * Normalizes a rating for sorting and filtering purposes.
	 *
	 * This allows all reviews to be queried based on a common rating scale.
	 *
	 * @since 1.2.0
	 *
	 * @param int    $rating   The actual rating assigned to the review.
	 * @param string $platform The platform ID used for filtering.
	 * @return int Normalized rating out of 100.
	 */
	public function normalize_rating( $rating, $platform ) {
		if ( 'positive' === $rating ) {
			return 101;
		} elseif ( 'negative' === $rating ) {
			return 1;
		}

		// Rating must be numerical since it is not a recommendation.
		$rating = filter_var(
			$rating,
			FILTER_SANITIZE_NUMBER_FLOAT,
			FILTER_FLAG_ALLOW_FRACTION
		);

		if ( ! $rating ) {
			return 0;
		}

		/**
		 * Filters the max rating used to normalize reviews.
		 *
		 * @since 0.1.0
		 *
		 * @param int    $max_rating The max rating (e.g. 5 if a 5-star scale).
		 * @param string $platform   The platform ID.
		 */
		$max_rating = apply_filters( 'wpbr_max_rating', 5, $platform );
		$multiplier = 100 / $max_rating;
		$normalized_rating = $rating * $multiplier;

		return $normalized_rating;
	}
}
