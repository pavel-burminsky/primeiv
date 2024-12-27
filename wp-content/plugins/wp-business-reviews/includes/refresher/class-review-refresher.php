<?php
/**
 * Defines the Review_Refresher class
 *
 * @link https://wpbusinessreviews.com
 *
 * @package WP_Business_Reviews\Includes
 * @since 1.3.0
 */

namespace WP_Business_Reviews\Includes\Refresher;

use WP_Business_Reviews\Includes\Request\Request_Delegator;
use WP_Business_Reviews\Includes\Serializer\Review_Serializer;

/**
 * Refreshes the available reviews from a given review source.
 *
 * @since 1.3.0
 */
class Review_Refresher {
	/**
	 * Request delegator for retrieving remote reviews.
	 *
	 * @since 1.3.0
	 *
	 * @var Request_Delegator $request_delegator
	 */
	protected $request_delegator;

	/**
	 * Saver of reviews
	 *
	 * @since 1.3.0
	 *
	 * @var Review_Serializer $review_serializer
	 */
	protected $review_serializer;

	/**
	 * Instantiates the Review_Refresher object.
	 *
	 * @since 1.3.0
	 *
	 * @param Request_Delegator $request_delegator Handler of remote requests.
	 * @param Review_Serializer $review_serializer Saver of reviews.
	 */
	public function __construct( $request_delegator, $review_serializer ) {
		$this->request_delegator = $request_delegator;
		$this->review_serializer = $review_serializer;
	}

	/**
	 * Registers functionality with WordPress hooks.
	 *
	 * @since 1.3.0
	 */
	public function register() {
		add_action( 'wp_ajax_wpbr_refresh_reviews', array( $this, 'ajax_refresh_reviews' ) );
	}

	/**
	 * Saves new reviews that do not yet exist locally.
	 *
	 * @since 1.3.0
	 *
	 * @param array  $reviews     Review post data to be saved.
	 * @param int    $post_parent The review source's post ID in WordPress,
	 *                            which is the parent post for new reviews.
	 * @return array IDs of the newly saved reviews.
	 */
	protected function save_reviews( $reviews, $post_parent ) {
		$new_reviews    = array();
		$new_review_ids = array();

		$this->review_serializer->set_post_parent( $post_parent );

		// Prepare only the new reviews to be saved.
		foreach ( $reviews as $review ) {
			// Convert review object to array.
			$review_json = $review->jsonSerialize();

			// Prepare array for saving as a WP post.
			$post_array = $this->review_serializer->prepare_post_array( $review_json );

			if ( ! empty( $post_array ) ) {
				$post_array['tax_input']['wpbr_attribute'][] = 'refreshed';
				$new_reviews[] = $post_array;
			}
		}

		// Save review posts.
		if ( ! empty( $new_reviews ) ) {
			$new_review_ids = $this->review_serializer->save_multiple( $new_reviews );
		}

		return $new_review_ids;
	}

	/**
	 * Adds the latest available reviews from a remote review source.
	 *
	 * @since 1.3.0
	 *
	 * @param string $platform         The review platform.
	 * @param string $review_source_id The review source's ID on the platform.
	 * @param int    $post_parent      The review source's post ID in WordPress,
	 *                                 which is the parent post for new reviews.
	 * @return array Array of newly saved post IDs.
	 */
	public function refresh_reviews( $platform, $review_source_id, $post_parent ) {
		$new_review_ids = array();

		// Get latest available reviews.
		$this->request_delegator->init( $platform );
		$reviews = $this->request_delegator->get_new_remote_reviews( $review_source_id );

		// Mark platform status as "Needs Attention" if error is returned.
		if ( is_wp_error( $reviews ) ) {
			/**
			 * Fires when a platform's status is updated (not necessarily changed).
			 *
			 * @since 1.3.0
			 *
			 * @param string $platform Platform ID.
			 * @param string $status   Platform status code.
			 */
			do_action( 'wpbr_platform_status_update', $platform, "{$platform}_needs_attention" );

			return $reviews;
		}

		// Save reviews.
		if ( ! empty( $reviews ) ) {
			$new_review_ids = $this->save_reviews( $reviews, $post_parent );
		}

		return $new_review_ids;
	}

	/**
	 * Refreshes reviews via AJAX post request.
	 *
	 * @since 1.3.0
	 */
	public function ajax_refresh_reviews() {
		if ( ! isset( $_POST['platform'], $_POST['review_source_id'], $_POST['post_parent'] ) ) {
			wp_die();
		}

		// TODO: Verify nonce and permission.

		// Set request parameters from posted AJAX request.
		$platform         = sanitize_text_field( wp_unslash( $_POST['platform'] ) );
		$review_source_id = sanitize_text_field( wp_unslash( $_POST['review_source_id'] ) );
		$post_parent      = absint( $_POST['post_parent'] );

		$refreshed_reviews = $this->refresh_reviews( $platform, $review_source_id, $post_parent );

		if ( is_wp_error( $refreshed_reviews ) ) {
			$message = $refreshed_reviews->get_error_message();
			wp_send_json_error( $message );
		}

		wp_send_json_success( $refreshed_reviews );
	}
}
