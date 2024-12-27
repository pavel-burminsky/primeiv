<?php
/**
 * Defines the Response_Normalizer_Factory class
 *
 * @link https://wpbusinessreviews.com
 *
 * @package WP_Business_Reviews\Includes\Request\Response_Normalizer
 * @since 0.1.0
 */

namespace WP_Business_Reviews\Includes\Request\Response_Normalizer;

/**
 * Creates a new response normalizer based on the provided platform.
 *
 * @since 0.1.0
 */
class Response_Normalizer_Factory {
	/**
	 * Creates a new response normalizer based on the provided platform.
	 *
	 * @since 0.1.0
	 *
	 * @param string $platform Platform from which the response originated.
	 * @return Response_Normalizer_Abstract Instance of Response_Normalizer.
	 */
	public function create( $platform ) {
		switch ( $platform ) {
			case 'google_places':
				$response_normalizer = new Google_Places_Response_Normalizer();
				break;
			case 'facebook':
				$response_normalizer = new Facebook_Response_Normalizer();
				break;
			case 'trust_pilot':
				$response_normalizer = new Trust_Pilot_Response_Normalizer();
				break;
			case 'woocommerce':
				$response_normalizer = new WooCommerce_Response_Normalizer();
				break;
			case 'yelp':
				$response_normalizer = new Yelp_Response_Normalizer();
				break;
			case 'yp':
				$response_normalizer = new YP_Response_Normalizer();
				break;
			case 'zomato':
				$response_normalizer = new Zomato_Response_Normalizer();
				break;
		}

		return $response_normalizer;
	}
}
