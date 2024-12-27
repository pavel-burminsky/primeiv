<?php
/**
 * Defines the Trust_Pilot class
 *
 * @link https://wpbusinessreviews.com
 *
 * @package WP_Business_Reviews\Includes\Request
 * @since 1.4.0
 */

namespace WP_Business_Reviews\Includes\Request;

/**
 * Retrieves data from Trustpilot API.
 *
 * @since 1.4.0
 */
class Trust_Pilot_Request extends Request {

	/**
	 * @inheritDoc
	 */
	protected $platform = 'trust_pilot';

	/**
	 * Trustpilot API key.
	 *
	 * @since 1.4.0
	 * @var string $key
	 */
	private $key;

	/**
	 * Instantiates the Trust_Pilot object.
	 *
	 * @param string $key Trustpilot API key.
	 *
	 * @since 1.4.0
	 */
	public function __construct( $key ) {
		$this->key = $key;
	}

	/**
	 * Retrieves the platform status based on a test request.
	 *
	 * @return string The platform status.
	 * @since 1.4.0
	 *
	 */
	public function get_platform_status() {

		// Now check API response.
		$response = $this->find_business( 'trustpilot.com' );

		if ( is_wp_error( $response ) ) {
			return 'disconnected';
		}

		return 'connected';
	}


	/**
	 * Retrieves review source details based on Trustpilot business ID.
	 *
	 * @see https://developers.trustpilot.com/business-units-api#get-a-business-unit's-reviews
	 *
	 * @since 1.4.0
	 *
	 * @param string $id The Trustpilot Business ID.
	 *
	 * @return array|\WP_Error
	 */
	public function get_review_source( $id ) {

		$profile_info   = $this->get_profile_info( $id );
		$public_profile = $this->get_public_profile( $id );
		$web_links      = $this->get_web_links( $id );
		$logo           = $this->get_logo_url( $id );

		$tp_response = array_merge( $profile_info, $public_profile, $web_links, $logo );

		return $tp_response;

	}

	/**
	 * Searches review sources based on search terms and location.
	 *
	 * @param string $domain The domain of the business on Trustpilot.
	 * @param string $location Unused for Trustpilot, search is based on URL.
	 *
	 * @return array|\WP_Error Associative array containing response or WP_Error
	 *                        if response structure is invalid.
	 * @since 1.4.0
	 *
	 */
	public function search_review_source( $domain, $location = '' ) {

		$response = false;

		// Find business result.
		$search_result = $this->find_business( $domain );

		// Must return business ID.
		if ( isset( $search_result['id'] ) && ! empty( $search_result['id'] ) ) {
			$response = $this->get_review_source( $search_result['id'] );
		}

		return $response;

	}

	/**
	 * Searches review sources based on search terms and location.
	 *
	 * @param string $domain The domain of the business on Trustpilot.
	 * @param string $location Unused for Trustpilot, search is based on URL.
	 *
	 * @return array|\WP_Error Associative array containing response or WP_Error
	 *                        if response structure is invalid.
	 * @since 1.4.0
	 *
	 */
	public function find_business( $domain, $location = '' ) {

		$url = add_query_arg(
			array(
				'name'   => $domain,
				'apikey' => $this->key,
			),
			'https://api.trustpilot.com/v1/business-units/find'
		);

		$response = $this->get( $url, array() );

		if ( ! isset( $response['id'] ) ) {
			return new \WP_Error( 'wpbr_no_review_sources', __( 'No results found. Enter the primary domain of your business as it appears on Trustpilot for best results.', 'wp-business-reviews' ) );
		}

		return $response;
	}

	/**
	 * Retrieves review source public details based on Trustpilot business ID.
	 *
	 * @see https://developers.trustpilot.com/business-units-api#get-public-business-unit
	 *
	 * @since 1.4.0
	 *
	 * @param string $id The domain of the business.
	 *
	 * @return array|\WP_Error Associative array containing response or WP_Error
	 *                        if response structure is invalid.
	 */
	public function get_public_profile( $id ) {
		$url = add_query_arg(
			array(
				'apikey' => $this->key,
			),
			"https://api.trustpilot.com/v1/business-units/{$id}"
		);

		$response = $this->get( $url, array() );

		return $response;
	}

	/**
	 * Retrieves review source web links details based on Trustpilot business ID.
	 *
	 * @see https://developers.trustpilot.com/business-units-api#get-a-business-unit's-web-links
	 *
	 * @since 1.4.0
	 *
	 * @param string $id The domain of the business.
	 *
	 * @return array|\WP_Error Associative array containing response or WP_Error
	 *                        if response structure is invalid.
	 */
	public function get_web_links( $id ) {
		$url = add_query_arg(
			array(
				'apikey' => $this->key,
				// For some reason TP uses "en-US" rather than the "en_US" WP returns.
				'locale' => str_replace( '_', '-', get_locale() ),
			),
			"https://api.trustpilot.com/v1/business-units/{$id}/web-links"
		);

		$response = $this->get( $url, array() );

		return $response;
	}

	/**
	 * Returns the business logo.
	 *
	 * @see https://developers.trustpilot.com/business-units-api#get-business-unit-company-logo
	 *
	 * @param $id
	 *
	 * @return array
	 */
	public function get_logo_url( $id ) {
		$url = add_query_arg(
			array(
				'apikey' => $this->key,
			),
			"https://api.trustpilot.com/v1/business-units/{$id}/images/logo"
		);

		$response = $this->get( $url, array() );

		return $response;
	}

	/**
	 * Retrieves review source details based on Trustpilot business ID.
	 *
	 * @see https://developers.trustpilot.com/business-units-api#get-a-business-unit's-reviews
	 *
	 * @since 1.4.0
	 *
	 * @param string $id The Trustpilot Business ID.
	 *
	 * @return array
	 */
	public function get_profile_info( $id ) {
		$url = add_query_arg(
			array(
				'apikey' => $this->key,
			),
			"https://api.trustpilot.com/v1/business-units/{$id}/profileinfo"
		);

		$response = $this->get( $url, array() );
		// Ensure we pass along the ID for normalizer.
		$response['id'] = $id;

		return $response;
	}

	/**
	 * Retrieves reviews based on Trustpilot business ID.
	 *
	 * @param string $id The Trustpilot business ID.
	 *
	 * @return array|\WP_Error Associative array containing response or WP_Error
	 *                        if response structure is invalid.
	 * @since 1.4.0
	 *
	 */
	public function get_reviews( $id ) {

		$url = add_query_arg(
			array(
				'apikey'  => $this->key,
				'perPage' => 24, // Max allowed
			),
			"https://api.trustpilot.com/v1/business-units/{$id}/reviews"
		);

		$response = $this->get( $url, array() );

		if ( ! isset( $response['reviews'] ) ) {
			return new \WP_Error( 'wpbr_no_reviews', __( 'No reviews found. Although reviews may exist on the platform, none were returned from the platform API.', 'wp-business-reviews' ) );
		}

		return $response;
	}

}
