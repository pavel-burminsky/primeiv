<?php
/**
 * Defines the Facebook_Image_Refresher class
 *
 * @link https://wpbusinessreviews.com
 *
 * @package WP_Business_Reviews\Includes\Refresher
 * @since 1.3.0
 */

namespace WP_Business_Reviews\Includes\Refresher;

use WP_Business_Reviews\Includes\Libraries\WP_Background_Processing\WP_Background_Process;
use WP_Business_Reviews\Includes\Cron_Scheduler;

/**
 * Refreshes Facebook images over time.
 *
 * @since 1.3.0
 */
class Facebook_Image_Refresher extends WP_Background_Process {
	/**
	 * Array of Facebook page settings.
	 *
	 * @since 1.3.0
	 * @var array
	 */
	protected $facebook_pages;

	/**
	 * Instantiates the Facebook_Image_Refresher object.
	 *
	 * @since 1.3.0
	 *
	 * @param array $facebook_pages Array of Facebook page settings.
	 */
	public function __construct( $facebook_pages ) {
		$this->facebook_pages = $facebook_pages;
		$this->prefix         = 'wp_' . get_current_blog_id();
		$this->action         = 'wpbr_refresh_facebook_images';

		parent::__construct();
	}

	/**
	 * Registers functionality with WordPress hooks.
	 *
	 * @since 1.3.0
	 */
	public function register() {
		/**
		 * Filters whether Facebook images should be periodically refreshed.
		 *
		 * @since 1.3.0
		 *
		 * @param bool $update_facebook_images Whether to update or not.
		 */
		$update_facebook_images = apply_filters( 'wpbr_update_facebook_images', true );

		if ( $update_facebook_images ) {
			add_action( 'wpbr_run_weekly_events', array( $this, 'dispatch_updates' ) );
		}
	}

	/**
	 * Retrieves a batch of Facebook reviews in need of refreshing.
	 *
	 * @since 1.3.0
	 *
	 * @param int $posts_per_page Number of posts to retrieve. Defaults to 10.
	 * @return WP_Post[] Review post objects.
	 */
	public function get_review_posts( $posts_per_page = 10 ) {
		$post_type   = 'wpbr_review';
		$post_status = array( 'any', 'trash' );

		// Limit to posts with reviewer images from the Open Graph API.
		$reviewer_image_meta_query = array(
			'key'     => 'wpbr_reviewer_image',
			'value'   => '',
			'compare' => '!=',
		);

		// Limit to posts that have never been refreshed
		// or have not been refreshed since the defined threshold.
		$refresh_threshold_meta_query = array(
			'relation' => 'OR',
			array(
				'key'     => 'wpbr_review_last_refreshed',
				'compare' => 'NOT EXISTS',
			),
			array(
				'key'     => 'wpbr_review_last_refreshed',
				'value'   => $this->get_refresh_threshold(),
				'compare' => '<',
				'type'    => 'NUMERIC'
			),
		);

		// Limit to Facebook reviews only.
		$platform_tax_query = array(
			'taxonomy' => 'wpbr_platform',
			'field'    => 'slug',
			'terms'    => 'facebook',
		);

		// Bring it all together.
		$args = array(
			'posts_per_page' => $posts_per_page,
			'post_status'    => $post_status,
			'post_type'      => $post_type,
			'meta_query'     => array(
				'relation' => 'AND',
				$reviewer_image_meta_query,
				$refresh_threshold_meta_query
			),
			'tax_query'      => array(
				$platform_tax_query,
			)
		);

		$posts = get_posts( $args );

		return $posts;
	}

	/**
	 * Refreshes multiple review posts.
	 *
	 * @since 1.3.0
	 *
	 * @return bool False if last batch, otherwise true.
	 */
	public function refresh_reviews() {
		/**
		 * Filters the number of Facebook review posts to be refreshed per batch.
		 *
		 * @since 1.3.0
		 *
		 * @param int $batch_size Number of review posts per batch.
		 */
		$batch_size   = apply_filters( 'wpbr_facebook_refresh_batch_size', 10 );
		$review_posts = $this->get_review_posts( $batch_size );

		if ( empty( $review_posts ) ) {
			return false;
		}

		foreach ( $review_posts as $review_post ) {
			$refreshed = $this->refresh_review( $review_post->ID );

			if ( ! $refreshed ) {
				// Review did not refresh successfully, so end the background process.
				return false;
			}
		}

		if ( count( $review_posts ) < $batch_size ) {
			// Return false to signal this is the last batch.
			return false;
		}

		// Return true to signal more batches may be needed.
		return true;
	}

	/**
	 * Refreshes a single review post.
	 *
	 * @since 1.3.0
	 *
	 * @param int $post_id The review post ID.
	 * @return bool True if the post was refreshed, false otherwise.
	 */
	public function refresh_review( $post_id ) {
		$page_token = $this->get_page_token_from_post( $post_id );

		if ( ! $page_token ) {
			/** This action is documented in includes/refresher/class-review-refresher.php */
			do_action( 'wpbr_platform_status_update', 'facebook', 'facebook_needs_attention' );

			return false;
		}

		$reviewer_id = $this->get_reviewer_id_from_post( $post_id );

		if ( ! $reviewer_id ) {
			return false;
		}

		$request_url = $this->generate_request_url( $reviewer_id, $page_token );
		$response    = wp_safe_remote_get( $request_url );

		if ( is_wp_error( $response ) || 200 !== $response['response']['code'] ) {
			/** This action is documented in includes/refresher/class-review-refresher.php */
			do_action( 'wpbr_platform_status_update', 'facebook', 'facebook_needs_attention' );

			return false;
		}

		$new_image_url = esc_url_raw( $response['http_response']->get_response_object()->url );

		// Ensure reviewer ID is part of URL for future use.
		if ( ! strpos( $new_image_url, $reviewer_id ) ) {
			return false;
		}

		update_post_meta( $post_id, 'wpbr_reviewer_image', $new_image_url );
		update_post_meta( $post_id, 'wpbr_review_last_refreshed', time() );

		return true;
	}

	/**
	 * Begins the background update process.
	 *
	 * @since 1.3.0
	 */
	public function dispatch_updates() {
		Cron_Scheduler::update_last_scheduled_event();
		$this->push_to_queue( 'refresh_reviews' );
		$this->save()->dispatch();
	}

	/**
	 * Runs an individual task in the queue.
	 *
	 * If the callback returns a truthy value, it will be added to the end
	 * of the queue for re-processing. If the callback returns a falsy value,
	 * it is considered complete and removed from the queue.
	 *
	 * @since 1.3.0
	 *
	 * @param string $callback Update callback function.
	 * @return string|bool Name of the callback function or false.
	 */
	protected function task( $callback ) {
		$result = false;

		if ( is_callable( array( $this, $callback ) ) ) {
			$result = (bool) call_user_func( array( $this, $callback ) );
		}

		return $result ? $callback : false;
	}

	/**
	 * Generates the request URL used to access the Facebook reviewer image.
	 *
	 * The returned URL is a 302 redirect that can be used to access the full
	 * reviewer image URL. The request URL should not be used directly in the
	 * browser because it would expose the page access token.
	 *
	 * @since 1.3.0
	 *
	 * @param string $reviewer_id The Facebook reviewer ID.
	 * @param string $page_token The Facebook page access token.
	 * @return string The Facebook request URL.
	 */
	protected function generate_request_url( $reviewer_id, $page_token ) {
		/** This filter is documented in includes/request/class-facebook-request.php */
		$dimensions = apply_filters( 'wpbr_facebook_picture_dimensions', array( 120, 120 ) );

		$url = add_query_arg(
			array(
				'width'        => $dimensions[0],
				'height'       => $dimensions[1],
				'access_token' => $page_token,
			),
			"https://graph.facebook.com/{$reviewer_id}/picture"
		);

		return $url;
	}

	/**
	 * Retrieves the refresh threshold as a Unix timestamp.
	 *
	 * @since 1.3.0
	 *
	 * @return int The Unix timestamp.
	 */
	protected function get_refresh_threshold() {
		/**
		 * Filters the Unix timestamp that determines if a review gets refreshed.
		 *
		 * The default timestamp is set to two weeks ago, meaning any review that
		 * has not been refreshed in the past two weeks should be refreshed.
		 *
		 * @since 1.3.0
		 *
		 * @param int $refresh_threshold The Unix timestamp.
		 */
		$refresh_threshold = apply_filters(
			'wpbr_facebook_refresh_threshold',
			strtotime( '-2 weeks', time() ) // Default to two weeks ago.
		);

		return $refresh_threshold;
	}

	/**
	 * Retrieves the Facebook reviewer ID based on WordPress post ID.
	 *
	 * @since 1.3.0
	 *
	 * @param int $post_id The review post ID.
	 * @return string The Facebook reviewer ID.
	 */
	protected function get_reviewer_id_from_post( $post_id ) {
		$reviewer_id = '';
		$image_url   = get_post_meta( $post_id, 'wpbr_reviewer_image', true );

		if ( ! $image_url ) {
			return $reviewer_id;
		}

		if ( preg_match('/id=([0-9]+)&/', $image_url, $match ) === 1 ) {
			// Matches https://platform-lookaside.fbsbx.com/platform/profilepic/?psid={$reviewer_id}&...
			$reviewer_id = $match[1];
		} elseif ( preg_match('/([0-9]+)\/picture/', $image_url, $match ) === 1 ) {
			// Matches https://graph.facebook.com/{$reviewer_id}/picture...
			$reviewer_id = $match[1];
		}

		return $reviewer_id;
	}

	/**
	 * Retrieves the appropriate page access token to update the review post.
	 *
	 * @since 1.3.0
	 *
	 * @param int $post_id The review post ID.
	 * @return string The Facebook page access token.
	 */
	protected function get_page_token_from_post( $post_id ) {
		$page_token       = '';
		$review_source_id = get_post_meta( $post_id, 'wpbr_review_source_id', true );

		if ( isset( $this->facebook_pages[ $review_source_id ] ) ) {
			$page_token = $this->facebook_pages[ $review_source_id ]['token'];
		}

		return $page_token;
	}
}
