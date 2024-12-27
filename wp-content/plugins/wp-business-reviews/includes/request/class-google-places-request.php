<?php
/**
 * Defines the Google_Places_Request class
 *
 * @link https://wpbusinessreviews.com
 *
 * @package WP_Business_Reviews\Includes\Request
 * @since 0.1.0
 */

namespace WP_Business_Reviews\Includes\Request;

/**
 * Retrieves data from Google Places API.
 *
 * @since 0.1.0
 */
class Google_Places_Request extends Request {
	/**
	 * Platform ID.
	 *
	 * @since 0.1.0
	 * @var string $platform
	 */
	protected $platform = 'google_places';

	/**
	 * Google Places API key.
	 *
	 * @since 0.1.0
	 * @var string $key
	 */
	private $key;

	/**
	 * Instantiates the Google_Places_Request object.
	 *
	 * @since 0.1.0
	 *
	 * @param string $key Google Places API key.
	 */
	public function __construct( $key ) {
		$this->key = $key;
	}

	/**
	 * Retrieves the platform status based on a test request.
	 *
	 * The test request uses the Googleplex's Place ID and saved API key to
	 * determine the status of the connection to the Google Places API.
	 *
	 * @since 1.0.1
	 *
	 * @return string The platform status.
	 */
	public function get_platform_status() {
		$status = 'disconnected';
		$url    = add_query_arg(
			array(
				'placeid' => 'ChIJj61dQgK6j4AR4GeTYWZsKWw',
				'key'   => $this->key,
			),
			'https://maps.googleapis.com/maps/api/place/details/json'
		);

		$response = $this->get( $url );

		if ( ! is_wp_error( $response ) && isset ( $response['status'] ) ) {
			switch ( $response['status'] ) {
				case 'OK' :
					$status = 'connected';
					break;

				case 'OVER_QUERY_LIMIT' :
					$status = 'google_places_over_query_limit';
					break;

				case 'REQUEST_DENIED' :
					if (
						isset( $response['error_message'] )
						&& strpos( $response['error_message'], 'restriction' )
					) {
						$status = 'google_places_restricted';
					}
					break;
			}
		}

		return $status;
	}

	/**
	 * Searches review sources based on search terms and location.
	 *
	 * @since 1.0.1 Add error handling for billing requirement.
	 * @since 0.1.0
	 *
	 * @param string $terms    The search terms, usually a Place name.
	 * @param string $location The location within which to search.
	 * @return array|object Associative array containing response or WP_Error if response structure is invalid.
	 */
	public function search_review_source( $terms, $location ) {

		// Replace ampersands with "and" to get proper search results.
		// See issue #232 for reference.
		$terms = str_replace( '&', 'and', $terms );

		// Build query by combining terms and location.
		$query = trim( implode( ' in ', array( $terms, $location ) ) );

		$url = add_query_arg(
			array(
				'query' => $query,
				'key'   => $this->key,
			),
			'https://maps.googleapis.com/maps/api/place/textsearch/json'
		);

		$response = $this->get( $url );

		// Handle errors.
		if ( isset( $response['error_message'] ) ) {

			if ( 'OVER_QUERY_LIMIT' === $response['status'] ) {
				// Set billing requirement error message.
				$billing_doc_url = 'https://wpbusinessreviews.com/documentation/platforms/google/#billing-not-enabled';
				$error_message = sprintf(
					__( 'A valid API key was entered, but billing is not enabled. As of July 16th, 2018, Google requires users to enable billing before accessing the Google Places API. %sLearn how to enable billing%s.', 'wp-business-reviews' ),
					'<a href="' . esc_url( $billing_doc_url )  . '" target="_blank" rel="noopener noreferrer">',
					'</a>'
				);

				/**
				 * Fires after a platform error has occurred following a remote request.
				 *
				 * @since 1.0.1
				 *
				 * @param string $platform The platform slug.
				 * @param string $status   The platform status.
				 */
				do_action( 'wpbr_platform_status_update', $this->platform, 'google_places_over_query_limit' );

			} else {
				// Otherwise set error message directly from API response.
				$error_message = sanitize_text_field( $response['error_message'] );
			}

			return new \WP_Error( 'wpbr_platform_error', $error_message );

		} elseif ( empty( $response['results'] ) ) {

			// Handle error if no results found.
			return new \WP_Error( 'wpbr_no_review_sources', __( 'No results found. For best results, enter the business name and location as they appear on Google. Alternatively you may enter the Place ID of the business.', 'wp-business-reviews' ) );

		}

		return $response['results'];
	}

	/**
	 * Retrieves review source details based on Google Place ID.
	 *
	 * @since 0.1.0
	 *
	 * @param string $review_source_id The Google Place ID.
	 * @return array|WP_Error Associative array containing response or WP_Error
	 *                        if response structure is invalid.
	 */
	public function get_review_source( $review_source_id ) {
		$url = add_query_arg(
			array(
				'placeid' => $review_source_id,
				'key'     => $this->key,
			),
			'https://maps.googleapis.com/maps/api/place/details/json'
		);

		$response = $this->get( $url );

		if ( ! isset( $response['result'] ) ) {
			return new \WP_Error( 'wpbr_invalid_response_structure', __( 'Invalid response structure.', 'wp-business-reviews' ) );
		}

		return $response['result'];
	}

	/**
	 * Retrieves reviews based on Google Place ID.
	 *
	 * Since Google Places API returns place and reviews data together, the
	 * same method can be used to return reviews.
	 *
	 * @since 1.2.0 Return reviews in reverse chronological order.
	 * @since 0.1.0
	 *
	 * @param string $review_source_id The Google Place ID.
	 * @return array|WP_Error Associative array containing response or WP_Error
	 *                        if response structure is invalid.
	 */
	public function get_reviews( $review_source_id ) {
		$reviews  = array();
		$response = $this->get_review_source( $review_source_id );

		if ( ! is_array( $response ) || ! isset( $response['reviews'] ) ) {
			return new \WP_Error( 'wpbr_no_reviews', __( 'No reviews found. Although reviews may exist on the platform, none were returned from the platform API.', 'wp-business-reviews' ) );
		}

		$reviews = $response['reviews'];
		usort( $reviews, array( $this, 'compare_timestamps' ) );

		return $reviews;
	}

	/**
	 * Compares the timestamps of two reviews.
	 *
	 * @since 1.2.0
	 *
	 * @param array $review1 Array of review data with a timestamp.
	 * @param array $review2 Array of review data with a timestamp.
	 * @return int Difference between two timestamps.
	 */
	protected function compare_timestamps( $review1, $review2 ) {
		return $review2['time'] - $review1['time'];
	}
}
