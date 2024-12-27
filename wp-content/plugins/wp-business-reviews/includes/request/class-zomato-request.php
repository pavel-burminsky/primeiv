<?php
/**
 * Defines the Zomato_Request class
 *
 * @link https://wpbusinessreviews.com
 *
 * @package WP_Business_Reviews\Includes\Request
 * @since 1.3.0
 */

namespace WP_Business_Reviews\Includes\Request;

/**
 * Retrieves data from Zomato API.
 *
 * @since 1.3.0
 */
class Zomato_Request extends Request {
	/**
	 * Platform ID.
	 *
	 * @since 1.3.0
	 * @var string $platform
	 */
	protected $platform = 'zomato';

	/**
	 * Zomato API key.
	 *
	 * @since 1.3.0
	 * @var string $key
	 */
	private $key;

	/**
	 * Instantiates the Zomato_Request object.
	 *
	 * @since 1.3.0
	 *
	 * @param string $key Zomato API key.
	 */
	public function __construct( $key ) {
		$this->key = $key;
	}

	/**
	 * Retrieves the platform status based on a test request.
	 *
 	 * @since 1.3.0
	 *
	 * @return string The platform status.
	 */
	public function get_platform_status() {
		$response = $this->search_review_source( 'pizza' );

		if ( is_wp_error( $response ) ) {
			return 'disconnected';
		}

		return 'connected';
	}

	/**
	 * Searches review sources based on search terms and location.
	 *
	 * @since 1.3.0
	 *
	 * @param string $name     Name of the business.
	 * @param string $location Location of the business. Optional.
	 * @return array|WP_Error Associative array containing response or WP_Error
	 *                        if response structure is invalid.
	 */
	public function search_review_source( $name, $location = '' ) {
		$city_id = '';

		if ( $location ) {
			$city_id = $this->get_city_id( $location );

			if ( is_wp_error( $city_id ) ) {
				return $city_id;
			}
		}

		$url = add_query_arg(
			array(
				'q'           => $name,
				'entity_id'   => $city_id,
				'entity_type' => 'city'
			),
			'https://developers.zomato.com/api/v2.1/search'
		);

		$args = array(
			'user-agent' => '',
			'headers'    => array(
				'user-key' => $this->key,
			),
		);

		$response = $this->get( $url, $args );

		if ( empty( $response['restaurants'] ) ) {
			return new \WP_Error( 'wpbr_no_restaurants', __( 'No results found. For best results, enter the restaurant name and city as they appear on the platform.', 'wp-business-reviews' ) );
		}

		return $response['restaurants'];
	}

	/**
	 * Searches Zomato for City ID.
	 *
	 * @since 1.3.0
	 *
	 * @param string $name
	 * @param string $location
	 *
	 * @return array|WP_Error Associative array containing response or WP_Error
	 *                        if response structure is invalid.
	 */
	public function get_city_id( $location ) {
		$url = add_query_arg(
			array(
				'q'     => $location,
				'count' => 1,
			),
			'https://developers.zomato.com/api/v2.1/cities'
		);

		$args = array(
			'user-agent' => '',
			'headers'    => array(
				'user-key' => $this->key,
			),
		);

		$response = $this->get( $url, $args );

		if ( ! isset( $response['location_suggestions'][0]['id'] ) ) {
			return new \WP_Error( 'wpbr_no_cities', __( 'No results found. For best results, enter the city as it appears on the platform.', 'wp-business-reviews' ) );
		}

		return $response['location_suggestions'][0]['id'];
	}

	/**
	 * Retrieves review source details based on Zomato business ID.
	 *
	 * @since 1.3.0
	 *
	 * @param string $id The Zomato Restaurant ID.
	 *
	 * @return array|WP_Error Associative array containing response or WP_Error
	 *                        if response structure is invalid.
	 */
	public function get_review_source( $id ) {
		$url = add_query_arg(
			array(
				'res_id' => $id,
			),
			'https://developers.zomato.com/api/v2.1/restaurant'
		);

		$args = array(
			'user-agent' => '',
			'headers'    => array(
				'user-key' => $this->key,
			),
		);

		$response = $this->get( $url, $args );

		if ( ! isset( $response['R'] ) ) {
			return new \WP_Error( 'wpbr_no_review_sources', __( 'No results found. For best results, enter the restaurant name and city as they appear on the platform.', 'wp-business-reviews' ) );
		}

		return $response;
	}

	/**
	 * Retrieves reviews based on Zomato business ID.
	 *
	 * @since 1.3.0
	 *
	 * @param string $id The Zomato business ID.
	 *
	 * @return array|WP_Error Associative array containing response or WP_Error
	 *                        if response structure is invalid.
	 */
	public function get_reviews( $id ) {
		$url = add_query_arg(
			array(
				'res_id' => $id,
			),
			'https://developers.zomato.com/api/v2.1/reviews'
		);

		$args = array(
			'user-agent' => '',
			'headers'    => array(
				'user-key' => $this->key,
			),
		);

		$response = $this->get( $url, $args );

		if ( ! isset( $response['user_reviews'] ) ) {
			return new \WP_Error( 'wpbr_no_reviews', __( 'No reviews found. Although reviews may exist on the platform, none were returned from the platform API.', 'wp-business-reviews' ) );
		}

		$reviews = $response['user_reviews'];

		return $reviews;
	}
}
