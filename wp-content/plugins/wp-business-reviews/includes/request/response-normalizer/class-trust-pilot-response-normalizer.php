<?php
/**
 * Defines the Trust_Pilot_Response_Normalizer class
 *
 * @link https://wpbusinessreviews.com
 *
 * @package WP_Business_Reviews\Includes\Request\Response_Normalizer
 * @since 1.3.0
 */

namespace WP_Business_Reviews\Includes\Request\Response_Normalizer;

use WP_Business_Reviews\Includes\Request\Trust_Pilot_Request;
use WP_Business_Reviews\Includes\Review;
use WP_Business_Reviews\Includes\Review_Source;
use WP_Business_Reviews\Includes\Location;
use \DateTime;

/**
 * Normalizes the structure of a Trustpilot API response.
 *
 * @since 1.3.0
 */
class Trust_Pilot_Response_Normalizer extends Response_Normalizer_Abstract {
	/**
	 * Platform.
	 *
	 * @since 1.3.0
	 * @var string $platform
	 */
	protected $platform = 'trust_pilot';

	/**
	 * Trust pilot only has one review source. Passes along the raw review source.
	 *
	 * @param array $raw_review_source
	 *
	 * @return \WP_Business_Reviews\Includes\Request\Response_Normalizer\Review_Source|Review
	 */
	public function normalize_review_sources( array $raw_review_source ) {
		$review_sources[] = $this->normalize_review_source( $raw_review_source );

		return $review_sources;
	}

	/**
	 * Normalizes and sanitizes a raw review source from the platform API.
	 *
	 * @param array $raw_review_source Raw data from platform API.
	 *
	 * @return object Review_Source Review Normalized Review object.
	 * @since 1.3.0
	 *
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
		if ( isset( $r['id'] ) ) {
			$review_source_id = $this->clean( $r['id'] );
		}

		// Set components.
		$c['name']         = isset( $r['displayName'] ) ? $this->clean( $r['displayName'] ) : '';
		$c['url']          = isset( $r['profileUrl'] ) ? $this->clean( $r['profileUrl'] ) : '';
		$c['rating']       = isset( $r['stars'] ) ? $this->clean( $r['stars'] ) : '';
		$c['rating_count'] = isset( $r['numberOfReviews']['total'] ) ? $this->clean( $r['numberOfReviews']['total'] ) : '';

		// Set phone.
		if ( isset( $r['phone'] ) ) {
			$c['phone'] = $this->clean( $r['phone'] );
			$phone      = $this->clean( $r['phone'] );
		}

		// Set formatted address.
		if ( isset( $r['address']['display_address'] ) ) {
			$formatted_address = $this->format_address(
				$this->clean( $r['address']['display_address'] )
			);
		}

		// Set street address.
		if ( isset( $r['address']['street'] ) ) {
			$address['street_address'] = $this->clean( $r['address']['street'] );
		}

		// Set city.
		if ( isset( $r['address']['city'] ) ) {
			$address['city'] = $this->clean( $r['address']['city'] );
		}

		// Set state.
		if ( isset( $r['address']['state'] ) ) {
			$address['state_province'] = $this->clean( $r['address']['state'] );
		}

		// Set postal code.
		if ( isset( $r['address']['zip_code'] ) ) {
			$address['postal_code'] = $this->clean( $r['address']['zip_code'] );
		}

		// Set country.
		if ( isset( $r['address']['country'] ) ) {
			$address['country'] = $this->clean( $r['address']['country'] );
		}

		// Set formatted address.
		if ( isset( $r['address']['country'] ) ) {
			$formatted_address = $this->format_address(
				$this->clean( $address )
			);
		}

		// Set business profile image.
		if ( isset( $r['logoUrl'] ) ) {
			$c['image'] = $this->clean( $r['logoUrl'] );
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
	 * @param array $raw_response
	 * @param string $review_source_id
	 *
	 * @return array|void
	 */
	public function normalize_reviews( $raw_reviews, $review_source_id ) {
		$reviews = array();

		foreach ( $raw_reviews['reviews'] as $raw_review ) {
			$reviews[] = $this->normalize_review(
				$raw_review,
				$review_source_id
			);
		}

		return $reviews;
	}

	/**
	 * Normalizes and sanitizes a raw review from the platform API.
	 *
	 * @param array $raw_review
	 * @param string $review_source_id
	 *
	 * @return \WP_Business_Reviews\Includes\Request\Response_Normalizer\Review|Review|null
	 * @throws \Exception
	 * @since 1.3.0
	 *
	 */
	public function normalize_review( array $raw_review, $review_source_id ) {
		$review = null;
		$r      = $raw_review;
		$c      = Review::get_default_components();

		// Set review URL.
		if ( isset( $r['url'] ) ) {
			$c['review_url'] = "https://www.trustpilot.com/reviews/{$this->clean($r['id'])}";
		}

		// Set reviewer name.
		if ( isset( $r['consumer']['displayName'] ) ) {
			$c['reviewer_name'] = $this->clean( $r['consumer']['displayName'] );
		}

		// Set reviewer image.
		if ( isset( $r['consumer']['links'][1]['href'] ) ) {
			$c['reviewer_image'] = $this->clean( $r['consumer']['links'][1]['href'] );
		}

		// Set rating.
		if ( isset( $r['stars'] ) ) {
			$c['rating'] = $this->clean( $r['stars'] );
		}

		// Set timestamp based on UTC which I think is what TP is based on...
		if ( isset( $r['createdAt'] ) ) {
			$wp_date_format     = get_option( 'date_format' );
			$platform_timestamp = $this->clean( $r['createdAt'] );
			$platform_date_time = new DateTime( $platform_timestamp );
			$utc_timestamp      = $platform_date_time->format( 'Y-m-d H:i:s' );
			$offset_timestamp   = get_date_from_gmt( $utc_timestamp );

			$c['timestamp']      = $utc_timestamp;
			$c['formatted_date'] = date_i18n( $wp_date_format, strtotime( $offset_timestamp ) );
		}

		// Set content.
		if ( isset( $r['text'] ) ) {
			$c['content'] = $this->clean_multiline( $r['text'] );
		}

		$review = new Review( $this->platform, $review_source_id, $c );

		return $review;
	}

	/**
	 * Formats address from separate address components.
	 *
	 * @param array $address_components Associative array of address strings.
	 *
	 * @return string Formatted address.
	 * @since 1.3.0
	 *
	 */
	protected function format_address( $address_components ) {
		return trim( implode( array_filter( $address_components ), ', ' ) );
	}
}
