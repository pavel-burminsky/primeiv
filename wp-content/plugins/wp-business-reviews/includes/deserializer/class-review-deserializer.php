<?php
/**
 * Defines the Review_Deserializer class
 *
 * @link https://wpbusinessreviews.com
 *
 * @package WP_Business_Reviews\Includes\Deserializer
 * @since 0.1.0
 */

namespace WP_Business_Reviews\Includes\Deserializer;

use WP_Business_Reviews\Includes\Review;

/**
 * Retrieves reviews from the database.
 *
 * @since 0.1.0
 */
class Review_Deserializer extends Post_Deserializer {
	/**
	 * The post type being retrieved.
	 *
	 * @since 0.1.0
	 * @var string $post_type
	 */
	protected $post_type = 'wpbr_review';

	/**
	 * Registers functionality with WordPress hooks.
	 *
	 * @since 0.2.0
	 */
	public function register() {
		add_action( 'wp_ajax_wpbr_query_reviews', array( $this, 'ajax_query_reviews' ) );
	}

	/**
	 * Gets a single Review object.
	 *
	 * @since 0.2.0 Added `$args` as second parameter.
	 * @since 0.1.0
	 *
	 * @see WP_Query For acceptable query arguments.
	 *
	 * @param string $post_id ID of the post to retrieve.
	 * @param array  $args    Optional. Array of query arguments.
	 * @return Review|false Review object or false if review post not found.
	 */
	public function get_review( $post_id, $args = array() ) {
		$post = $this->get_post( $post_id, $args );

		if ( false === $post || 'publish' !== $post->post_status || $post->post_password ) {
			return false;
		}

		$review = $this->convert_post_to_review( $post );

		return $review;
	}

	/**
	 * Queries reviews.
	 *
	 * @since 0.2.0 Updated to accept collection settings instead of query args.
	 * @since 0.1.0
	 *
	 * @see Collection For accepted settings.
	 *
	 * @param array $settings Array of collection settings.
	 * @return Review[]|false Array of Review objects or false if no posts found.
	 */
	public function query_reviews( $settings ) {
		$reviews      = array();
		$args         = array(
			'order'   => 'desc',
			'orderby' => 'review_date',
		);

		// Maybe set order.
		if ( ! empty( $settings['order'] ) ) {
			$args['order'] = $settings['order'];
		}

		// Maybe set orderby.
		if ( ! empty( $settings['orderby'] ) ) {
			$args['orderby'] = $settings['orderby'];
		}

		// Maybe set meta query
		if ( 'review_date' === $args['orderby'] ) {
			// Sort by date.
			$args['meta_query']['review_date'] = array(
				'key'     => 'wpbr_timestamp',
				'compare' => 'EXISTS',
			);
		} elseif ( 'rating' === $args['orderby'] ) {
			// Sort by rating with date as secondary dimension.
			$args['orderby'] = array(
				'rating'      => $args['order'],
				'review_date' => 'desc',
			);
			$args['meta_query']['rating'] = array(
				'key'     => 'wpbr_rating_normal',
				'compare' => 'EXISTS',
				'type'    => 'NUMERIC'
			);
			$args['meta_query']['review_date'] = array(
				'key'     => 'wpbr_timestamp',
				'compare' => 'EXISTS',
			);
		}

		// Maybe set post parent.
		if ( isset( $settings['post_parent'] ) && 0 < $settings['post_parent'] ) {
			$args['post_parent'] = $settings['post_parent'];
		}

		// Maybe filter by rating.
		if ( isset( $settings['min_rating'] ) && 0 < $settings['min_rating'] ) {
			$min_rating = absint( $settings['min_rating'] );

			if (
				0 < $min_rating
				&& 5 >= $min_rating
				&& version_compare( get_option( 'wpbr_db_version' ), '1.2.0', '<' )
			) {
				// Prior to v1.2.0, the filter uses a taxonomy.
				$args['tax_query'] = array(
					array(
						'taxonomy' => 'wpbr_rating',
						'field'    => 'slug',
						'terms'    => $this->normalize_rating_terms( $min_rating ),
					),
				);
			} else {
				// After v.1.2.0, the filter uses a meta query.
				$args['meta_query']['rating'] = array(
					'key'      => 'wpbr_rating_normal',
					'value'    => $min_rating,
					'compare' => '>=',
					'type'     => 'NUMERIC'
				);
			}
		}

		// Maybe filter or isolate recommendations.
		if ( isset( $settings['review_type'] ) ) {
			if ( 'rating' === $settings['review_type'] ) {
				$args['tax_query'][] = array(
					'taxonomy' => 'wpbr_attribute',
					'field'    => 'slug',
					'terms'    => 'recommendation',
					'operator' => 'NOT IN',
				);
			} elseif ( 'recommendation' === $settings['review_type'] ) {
				$args['tax_query'][] = array(
					'taxonomy' => 'wpbr_attribute',
					'field'    => 'slug',
					'terms'    => 'recommendation',
					'operator' => 'IN',
				);
			}
		}

		// Maybe filter blank reviews.
		if (
			isset( $settings['blank_reviews'] )
			&& 'disabled' === $settings['blank_reviews']
		) {
			$args['tax_query'][] = array(
				'taxonomy' => 'wpbr_attribute',
				'field'    => 'slug',
				'terms'    => 'blank',
				'operator' => 'NOT IN',
			);
		}

		// Maybe filter by review tag.
		if ( ! empty( $settings['review_tags'] ) ) {
			$args['tax_query'][] = array(
				'taxonomy' => 'wpbr_review_tag',
				'field'    => 'term_id',
				'terms'    => $settings['review_tags'],
			);
		}

		// Maybe set maximum reviews.
		if ( isset( $settings['max_reviews'] ) ) {
			$args['posts_per_page'] = absint( $settings['max_reviews'] );
		}

		/**
		 * Filters the arguments used when querying reviews.
		 *
		 * @since 1.2.0
		 *
		 * @link https://codex.wordpress.org/Class_Reference/WP_Query#Parameters
		 *
		 * @param array $args     Array of query args.
		 * @param array $settings Array of collection settings.
		 */
		$args = apply_filters( 'wpbr_review_query_args', $args, $settings );

		$posts = $this->query_posts( $args );

		if ( empty( $posts ) ) {
			return false;
		}

		foreach ( $posts as $post ) {
			$reviews[] = $this->convert_post_to_review( $post );
		}

		return $reviews;
	}

	/**
	 * Converts a WP_Post object into a Review object.
	 *
	 * @since 0.1.0
	 *
	 * @param WP_Post $post The WP_Post object to be converted.
	 * @return Review The new Review object.
	 */
	protected function convert_post_to_review( $post ) {
		$components         = array();
		$platform           = $this->get_platform( $post );
		$review_source_id   = $this->get_meta( $post->ID, 'review_source_id' );
		$default_components = Review::get_default_components();

		// Map post meta to components.
		foreach ( $default_components as $key => $value ) {
			$components[ $key ] = $this->get_meta( $post->ID, $key ) ?: $value;
		}

		// Get review source components from parent post (for use in recommendations).
		if ( 0 < $post->post_parent ) {
			$components['review_source_name'] = get_the_title( $post->post_parent );
			$components['review_source_url']  = $this->get_meta( $post->post_parent, 'url' );
		}

		// Get formatted date based on WordPress settings.
		$wp_date_format               = get_option('date_format');
		$utc_timestamp                = $this->get_meta( $post->ID, 'timestamp' );
		$offset_timestamp             = get_date_from_gmt( $utc_timestamp );
		$components['formatted_date'] = date_i18n( $wp_date_format, strtotime( $offset_timestamp ) );

		// Add review content from post content field.
		$components['content'] = $post->post_content;

		$review = new Review(
			$platform,
			$review_source_id,
			$components,
			$post->ID
		);

		return $review;
	}

	/**
	 * Queries reviews via AJAX post request.
	 *
	 * @since 0.2.0
	 */
	public function ajax_query_reviews() {
		if ( isset( $_POST['settings'] ) ) {
			$settings_json = sanitize_text_field( wp_unslash( $_POST['settings'] ) );
			$settings = json_decode( $settings_json, true );
		}

		// Get review source and reviews data from remote API.
		$reviews_array = $this->query_reviews( $settings );

		if ( false === $reviews_array ) {
			$reviews_array = array();
		}

		wp_send_json_success(
			array(
				'reviews' => $reviews_array,
			)
		);
	}

	/**
	 * Generates a list of normalized ratings to include in the query.
	 *
	 * If the minimum allowed rating is 3 out of 5 stars, then the method returns:
	 *
	 * ```
	 * array(
	 *     '60', // 3-star equivalent.
	 *     '80', // 4-star equivalent.
	 *     '100', // 5-star equivalent.
	 * )
	 * ```
	 *
	 * @since 0.2.0
	 *
	 * @param int    $min_rating The minimum rating a review can have and still be
	 *                           included in the query.
	 * @param string $platform   The platform ID used for filtering.
	 * @return void
	 */
	protected function normalize_rating_terms( $min_rating, $platform = '' ) {
		$ratings = array();
		/** This filter is documented in includes/serializer/review-serializer.php */
		$max_rating = apply_filters( 'wpbr_max_rating', 5, $platform );
		$multiplier = 100 / $max_rating;
		for ( $i = $min_rating; $i <= $max_rating ; $i++ ) {
			$normalized_rating = $i * $multiplier;
			$ratings[] = (string) $normalized_rating;
		}
		return $ratings;
	}
}
