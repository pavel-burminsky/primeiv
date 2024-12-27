<?php
/**
 * Defines the Request_Delegator class
 *
 * @link https://wpbusinessreviews.com
 *
 * @package WP_Business_Reviews\Includes
 * @since 0.1.0
 */

namespace WP_Business_Reviews\Includes\Request;

use WP_Business_Reviews\Includes\Request\Request_Factory;
use WP_Business_Reviews\Includes\Request\Response_Normalizer\Response_Normalizer_Factory;
use WP_Business_Reviews\Includes\Deserializer\Review_Deserializer;
use WP_Business_Reviews\Includes\Deduplicator\Review_Deduplicator;

/**
 * Searches a remote reviews platform.
 *
 * @since 0.1.0
 */
class Request_Delegator {
	/**
	 * Factory that creates requests.
	 *
	 * @since 0.1.0
	 *
	 * @var Request_Factory $request_factory
	 */
	private $request_factory;

	/**
	 * Factory that creates response normalizers.
	 *
	 * @since 1.3.0
	 *
	 * @var Review $normalizer_factory
	 */
	private $normalizer_factory;

	/**
	 * Deserializer that retrieves local reviews from the database.
	 *
	 * @since 1.3.0
	 *
	 * @var Review_Deserializer $review_deserializer
	 */
	private $review_deserializer;

	/**
	 * Request that retrieves data from remote API.
	 *
	 * @since 0.1.0
	 *
	 * @var Request $request
	 */
	private $request;

	/**
	 * Response normalizer that sanitizes and normalizes raw API responses.
	 *
	 * @since 0.1.0
	 *
	 * @var Response_Normalizer_Abstract $normalizer
	 */
	private $normalizer;

	/**
	 * Instantiates the Request_Delegator object.
	 *
	 * @since 0.1.0
	 *
	 * @param Request_Factory             $request_factory     Request factory.
	 * @param Response_Normalizer_Factory $normalizer_factory  Normalizer factory.
	 * @param Review_Deserializer         $review_deserializer Review deserializer.
	 */
	public function __construct(
		Request_Factory $request_factory,
		Response_Normalizer_Factory $normalizer_factory,
		Review_Deserializer $review_deserializer
	) {
		$this->request_factory     = $request_factory;
		$this->normalizer_factory  = $normalizer_factory;
		$this->review_deserializer = $review_deserializer;
	}

	/**
	 * Initialize
	 *
	 * @param $platform
	 */
	public function init( $platform ) {
		$this->request    = $this->request_factory->create( $platform );
		$this->normalizer = $this->normalizer_factory->create( $platform );
	}

	/**
	 * Registers functionality with WordPress hooks.
	 *
	 * @since 0.1.0
	 */
	public function register() {
		add_action( 'wp_ajax_wpbr_search_review_source', array( $this, 'ajax_search_review_source' ) );
		add_action( 'wp_ajax_wpbr_get_source_and_reviews', array( $this, 'ajax_get_source_and_reviews' ) );
	}

	/**
	 * Searches a remote reviews platform based on platform and search query.
	 *
	 * @since 0.1.0
	 * @since 1.4.0 the $location parameter was made optional.
	 *
	 * @param string $terms    The search terms.
	 * @param string $location Optional. The search location.
	 * @return array|WP_Error Normalized, sanitized response body, or WP_Error.
	 */
	public function search_review_source( $terms, $location = '' ) {
		$raw_response = $this->request->search_review_source( $terms, $location );

		if ( is_wp_error( $raw_response ) ) {
			return $raw_response;
		}

		$review_sources = $this->normalizer->normalize_review_sources(
			$raw_response
		);

		return $review_sources;
	}

	/**
	 * Requests review source based on the provided platform and review source ID.
	 *
	 * A review source is the business or entity with which individual reviews
	 * are associated.
	 *
	 * @since 0.1.0
	 *
	 * @param string $review_source_id The review source ID.
	 * @return Review_Source|WP_Error Normalized review source object or WP_Error.
	 */
	public function get_review_source( $review_source_id ) {

		$raw_response = $this->request->get_review_source( $review_source_id );

		if ( is_wp_error( $raw_response ) ) {
			return $raw_response;
		}

		$review_source = $this->normalizer->normalize_review_source( $raw_response );

		return $review_source;
	}

	/**
	 * Retrieves reviews belonging to a single review source.
	 *
	 * Remote reviews are compared against local reviews and an array of unique
	 * reviews is returned in reverse-chronological order.
	 *
	 * @since 1.3.0 Include new reviews from API plus existing reviews from DB.
	 * @since 0.1.0
	 *
	 * @param string $review_source_id The review source ID.
	 * @param int    $max_reviews      Maximum number of local reviews to return.
	 * @return array Review[] Array of normalized Review objects.
	 */
	public function get_reviews( $review_source_id, $max_reviews = 24 ) {
		// Get remote reviews based on review source ID.
		$remote_reviews = $this->get_new_remote_reviews( $review_source_id );

		// Get post ID of the WP post with the same review source ID.
		$post_parent = $this->get_post_id_from_review_source_id( $review_source_id );

		// There is no local post parent, so return only remote reviews.
		if ( 0 === $post_parent ) {
			return $remote_reviews;
		}

		// Adjust the number of local reviews to account for remote reviews.
		if ( ! is_wp_error( $remote_reviews ) && ! empty( $remote_reviews ) ) {
			$max_reviews -= count( $remote_reviews );
		}

		// Get local reviews.
		$local_reviews = $this->get_local_reviews(
			array(
				'post_parent' => $post_parent,
				'max_reviews' => $max_reviews,
			)
		);

		if ( false === $local_reviews ) {
			$local_reviews = array();
		}

		$reviews = array_merge( $local_reviews, $remote_reviews );

		// Sort reviews in reverse chronological order.
		usort( $reviews, array( $this, 'compare_timestamps' ) );

		return $reviews;
	}

	/**
	 * Retrieves local reviews already in the database.
	 *
	 * @since 1.3.0
	 *
	 * @param array $settings Array of collection settings.
	 * @return Review[] Array of review objects.
	 */
	public function get_local_reviews( $settings ) {
		return $this->review_deserializer->query_reviews( $settings );
	}

	/**
	 * Retrieves new remote reviews from the platform API.
	 *
	 * @since 1.3.0
	 *
	 * @param string $review_source_id The review source ID on the platform.
	 * @return Review[]|WP_Error Array of review objects or WP_Error.
	 */
	public function get_new_remote_reviews( $review_source_id ) {

		$raw_response = $this->request->get_reviews( $review_source_id );

		if ( is_wp_error( $raw_response ) ) {
			return $raw_response;
		}

		$reviews = $this->normalizer->normalize_reviews(
			$raw_response,
			$review_source_id
		);

		$post_parent = $this->get_post_id_from_review_source_id( $review_source_id );
		$new_reviews = Review_Deduplicator::deduplicate( $reviews, $post_parent );

		return $new_reviews;
	}

	/**
	 * Searches a remote reviews platform based on platform and search query.
	 *
	 * @since 0.1.0
	 */
	public function ajax_search_review_source() {
		if ( ! isset( $_POST['platform'], $_POST['terms'] ) ) {
			wp_die();
		}

		// TODO: Verify nonce and permission.

		// Set request parameters from posted Ajax request.
		$platform = sanitize_text_field( wp_unslash( $_POST['platform'] ) );
		$terms    = sanitize_text_field( wp_unslash( $_POST['terms'] ) );
		$location = sanitize_text_field( wp_unslash( $_POST['location'] ) );

		// Initialize the request and normalizer based on the platform.
		$this->init( $platform );

		// Get review source data from remote API.
		$review_source_array = $this->search_review_source( $terms, $location );

		// Make sure response is not an error.
		if ( is_wp_error( $review_source_array ) ) {
			$error_message = $review_source_array->get_error_message();
			wp_send_json_error( $error_message );
		}

		// Send back array of review sources as JSON.
		wp_send_json_success( $review_source_array );
	}

	/**
	 * Requests review source and reviews via Ajax post request.
	 *
	 * Review source and its reviews are frequently requested together. This
	 * function sends data for both in a single response.
	 *
	 * @since 0.1.0
	 */
	public function ajax_get_source_and_reviews() {

		// Check for platform and review source ID in $_POST.
		if ( ! isset( $_POST['platform'], $_POST['reviewSourceId'] ) ) {
			wp_die();
		}

		// TODO: Verify nonce and permission.

		// Set request parameters from posted Ajax request.
		$platform         = sanitize_text_field( wp_unslash( $_POST['platform'] ) );
		$review_source_id = sanitize_text_field( wp_unslash( $_POST['reviewSourceId'] ) );

		// Initialize the request and normalizer based on the platform.
		$this->init( $platform );

		// Get review source data from remote API.
		$review_source = $this->get_review_source( $review_source_id );

		if ( is_wp_error( $review_source ) ) {
			$message = $review_source->get_error_message();
			wp_send_json_error( $message );
		}

		// Get reviews data from remote API.
		$reviews_array = $this->get_reviews( $review_source_id );

		// Make sure response is not an error.
		if ( is_wp_error( $reviews_array ) ) {
			$message = $reviews_array->get_error_message();
			wp_send_json_error( $message );
		}

		// Send it all back together as JSON.
		wp_send_json_success(
			array(
				'review_source' => $review_source,
				'reviews'       => $reviews_array,
			)
		);
	}

	/**
	 * Compares the timestamps of two review objects.
	 *
	 * @since 1.3.0
	 *
	 * @param Review $review1 First review object.
	 * @param Review $review2 Second review object.
	 * @return int Difference between two timestamps.
	 */
	protected function compare_timestamps( $review1, $review2 ) {
		$r1_timestamp = strtotime( $review1->get_component( 'timestamp' ) );
		$r2_timestamp = strtotime( $review2->get_component( 'timestamp' ) );

		return $r2_timestamp - $r1_timestamp;
	}

	/**
	 * Retrieves ID of WordPress post based on review source ID.
	 *
	 * @since 1.3.0
	 *
	 * @param string $review_source_id The review source ID on the platform.
	 * @return int The WordPress post ID of the review source post.
	 */
	protected function get_post_id_from_review_source_id( $review_source_id ) {
		$post_id = 0;

		$args = array(
			'fields'                 => 'ids',
			'meta_key'               => 'wpbr_review_source_id',
			'meta_value'             => $review_source_id,
			'no_found_rows'          => true,
			'posts_per_page'         => 1,
			'post_type'              => 'wpbr_review_source',
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
		);

		$query = new \WP_Query( $args );
		$posts = $query->posts;

		if ( ! empty( $posts ) ) {
			$post_id = $posts[0];
		}

		return $post_id;
	}
}
