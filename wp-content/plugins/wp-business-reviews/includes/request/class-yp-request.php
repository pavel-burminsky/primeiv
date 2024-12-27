<?php
/**
 * Defines the YP_Request class
 *
 * @link https://wpbusinessreviews.com
 *
 * @package WP_Business_Reviews\Includes\Request
 * @since 0.1.0
 */

namespace WP_Business_Reviews\Includes\Request;

/**
 * Retrieves data from YP API.
 *
 * @since 0.1.0
 */
class YP_Request extends Request {
	/**
	 * Platform ID.
	 *
	 * @since 0.1.0
	 * @var string $platform
	 */
	protected $platform = 'yp';

	/**
	 * YP API key.
	 *
	 * @since 0.1.0
	 * @var string $key
	 */
	private $key;

	/**
	 * Instantiates the YP_Request object.
	 *
	 * @since 0.1.0
	 *
	 * @param string $key YP API key.
	 */
	public function __construct( $key ) {
		$this->key = $key;
	}

	/**
	 * Retrieves the platform status based on a test request.
	 *
	 * @since 1.0.1
	 *
	 * @return string The platform status.
	 */
	public function get_platform_status() {
		$response = $this->search_review_source( 'PNC Park', 'Pittsburgh' );

		if ( is_wp_error( $response ) ) {
			return 'disconnected';
		} else {
			return 'connected';
		}
	}

	/**
	 * Searches review sources based on search terms and location.
	 *
	 * @since 0.1.0
	 *
	 * @param string $terms    The search terms, usually a business name.
	 * @param string $location The location within which to search.
	 * @return array|WP_Error Associative array containing response or WP_Error
	 *                        if response structure is invalid.
	 */
	public function search_review_source( $terms, $location ) {
		$url = add_query_arg(
			array(
				'term'  => $terms . ' in ' . $location,
				'format' => 'json',
				'key'    => $this->key,
			),
			'http://api2.yp.com/listings/v1/search'
		);

		$response = $this->get( $url );

		if ( ! isset( $response['searchResult']['searchListings']['searchListing'] ) ) {
			return new \WP_Error( 'wpbr_no_review_sources', __( 'No results found. For best results, enter the entire business name, city, and state as they appear on the platform.', 'wp-business-reviews' ) );
		}

		return $response['searchResult']['searchListings']['searchListing'];
	}

	/**
	 * Retrieves review source details based on YP listing ID.
	 *
	 * @since 0.1.0
	 *
	 * @param string $id The YP listing ID.
	 * @return array|WP_Error Associative array containing response or WP_Error
	 *                        if response structure is invalid.
	 */
	public function get_review_source( $id ) {
		$url = add_query_arg(
			array(
				'listingid' => $id,
				'format'     => 'json',
				'key'        => $this->key,
			),
			'http://api2.yp.com/listings/v1/details'
		);

		$response = $this->get( $url );

		return $response['listingsDetailsResult']['listingsDetails']['listingDetail'][0];
	}

	/**
	 * Retrieves reviews based on YP listing ID.
	 *
	 * @since 0.1.0
	 *
	 * @param string $id The YP listing ID.
	 * @return array|WP_Error Associative array containing response or WP_Error
	 *                        if response structure is invalid.
	 */
	public function get_reviews( $id ) {
		$url = add_query_arg(
			array(
				'listingid' => $id,
				'format'     => 'json',
				'key'        => $this->key,
			),
			'http://api2.yp.com/listings/v1/reviews'
		);

		$response = $this->get( $url );

		if ( ! isset( $response['ratingsAndReviewsResult']['reviews']['review'] ) ) {
			return new \WP_Error( 'wpbr_no_reviews', __( 'No reviews found. Although reviews may exist on the platform, none were returned from the platform API.', 'wp-business-reviews' ) );
		}

		return $response['ratingsAndReviewsResult']['reviews']['review'];
	}
}
