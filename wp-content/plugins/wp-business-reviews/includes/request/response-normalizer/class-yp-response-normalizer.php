<?php
/**
 * Defines the YP_Response_Normalizer class
 *
 * @link https://wpbusinessreviews.com
 *
 * @package WP_Business_Reviews\Includes\Request\Response_Normalizer
 * @since 0.1.0
 */

namespace WP_Business_Reviews\Includes\Request\Response_Normalizer;

use WP_Business_Reviews\Includes\Review;
use WP_Business_Reviews\Includes\Review_Source;
use WP_Business_Reviews\Includes\Location;
use \DateTime;

/**
 * Normalizes the structure of a YP API response.
 *
 * @since 0.1.0
 */
class YP_Response_Normalizer extends Response_Normalizer_Abstract {
	/**
	 * Platform.
	 *
	 * @since 0.1.0
	 * @var string $platform
	 */
	protected $platform = 'yp';

	/**
	 * Normalizes and sanitizes a raw review source from the platform API.
	 *
	 * @since 0.1.0
	 *
	 * @param array $raw_review_source Raw data from platform API.
	 * @return Review_Source Normalized review source object.
	 */
	public function normalize_review_source( array $raw_review_source ) {
		$review_source     = null;
		$r                 = $raw_review_source;
		$c                 = Review_Source::get_default_components();
		$review_source_id  = '';
		$formatted_address = '';
		$address           = array();
		$coordinates       = array();
		$phone             = '';

		// Set ID of the review source on the platform.
		if ( isset( $r['listingId'] ) ) {
			$review_source_id = $this->clean( $r['listingId'] );
		}

		// Set components.
		$c['name']         = isset( $r['businessName'] ) ? $this->clean( $r['businessName'] ) : '';
		$c['url']          = isset( $r['attribution'] ) ? $this->clean( $r['attribution'] ) : '';
		$c['rating']       = isset( $r['averageRating'] ) ? $this->clean( $r['averageRating'] ) : '';
		$c['rating_count'] = isset( $r['ratingCount'] ) ? $this->clean( $r['ratingCount'] ) : '';

		// Set street address.
		if ( isset( $r['street'] ) ) {
			$address['street_address'] = $this->clean( $r['street'] );
		}

		// Set city.
		if ( isset( $r['city'] ) ) {
			$address['city'] = $this->clean( $r['city'] );
		}

		// Set state.
		if ( isset( $r['state'] ) ) {
			$address['state_province'] = $this->clean( $r['state'] );
		}

		// Set postal code.
		if ( isset( $r['zip'] ) ) {
			$address['postal_code'] = $this->clean( $r['zip'] );
		}

		// Set formatted address by concatenating address components.
		if (
			isset(
				$address['street_address'],
				$address['city'],
				$address['state_province'],
				$address['postal_code']
			)
		) {
			$formatted_address = $this->format_address(
				$address['street_address'],
				$address['city'],
				$address['state_province'],
				$address['postal_code']
			);
		}

		// Set latitude.
		if ( isset( $r['latitude'] ) ) {
			$coordinates['latitude'] = $this->clean( $r['latitude'] );
		}

		// Set longitude.
		if ( isset( $r['longitude'] ) ) {
			$coordinates['longitude'] = $this->clean( $r['longitude'] );
		}

		// Set phone.
		if ( isset( $r['phone'] ) ) {
			$phone = $this->clean( $r['phone'] );
		}

		// Create location.
		$c['location'] = new Location(
			$formatted_address,
			$address,
			$coordinates,
			$phone
		);

		// Create review source.
		$review_source = new Review_Source(
			0,
			$this->platform,
			$review_source_id,
			$c
		);

		return $review_source;
	}

	/**
	 * Normalizes and sanitizes a raw review from the platform API.
	 *
	 * @since 0.1.0
	 *
	 * @param array  $raw_review       Raw data from platform API.
	 * @param string $review_source_id Review source ID associated with the review.
	 * @return Review Normalized review object.
	 */
	public function normalize_review( array $raw_review, $review_source_id ) {
		$review = null;
		$r = $raw_review;
		$c = Review::get_default_components();

		// Set reviewer.
		if ( isset( $r['reviewer'] ) ) {
			$c['reviewer_name'] = $this->clean( $r['reviewer'] );
		}

		// Set rating.
		if ( isset( $r['rating'] ) ) {
			$c['rating'] = $this->clean( $r['rating'] );
		}

		// Set timestamp based on YP platform time which is UTC.
		if ( isset( $r['reviewDate'] ) ) {
			$wp_date_format     = get_option( 'date_format' );
			$platform_timestamp = $this->clean( $r['reviewDate'] );
			$platform_date_time = new DateTime( $platform_timestamp );
			$utc_timestamp      = $platform_date_time->format( 'Y-m-d H:i:s' );
			$offset_timestamp   = get_date_from_gmt( $utc_timestamp );

			$c['timestamp']      = $utc_timestamp;
			$c['formatted_date'] = date_i18n( $wp_date_format, strtotime( $offset_timestamp ) );
		}

		// Set content.
		if ( isset( $r['reviewBody'] ) ) {
			$c['content'] = $this->clean_multiline( $r['reviewBody'] );
		}

		$review = new Review( $this->platform, $review_source_id, $c );

		return $review;
	}

	/**
	 * Formats address from separate address components.
	 *
	 * @param string $street_address Street address.
	 * @param string $city           City.
	 * @param string $state_province State.
	 * @param string $postal_code    Zip code.
	 * @return string Concatenated, formatted address.
	 */
	protected function format_address( $street_address, $city, $state_province, $postal_code ) {
		return  "{$street_address}, {$city}, {$state_province} {$postal_code}";
	}
}
