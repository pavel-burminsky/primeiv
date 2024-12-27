<?php
/**
 * Defines the Zomato_Response_Normalizer class
 *
 * @link https://wpbusinessreviews.com
 *
 * @package WP_Business_Reviews\Includes\Request\Response_Normalizer
 * @since 1.3.0
 */

namespace WP_Business_Reviews\Includes\Request\Response_Normalizer;

use WP_Business_Reviews\Includes\Review;
use WP_Business_Reviews\Includes\Review_Source;
use WP_Business_Reviews\Includes\Location;
use \DateTime;
use \DateTimeZone;

/**
 * Normalizes the structure of a Zomato API response.
 *
 * @since 1.3.0
 */
class Zomato_Response_Normalizer extends Response_Normalizer_Abstract {
	/**
	 * Platform.
	 *
	 * @since 1.3.0
	 * @var string $platform
	 */
	protected $platform = 'zomato';

	/**
	 * Normalizes and sanitizes multiple review sources.
	 *
	 * @since 1.3.0
	 *
	 * @param array $raw_review_sources Raw data from platform API.
	 * @return Review_Source[] Array of normalized Review_Source objects.
	 */
	public function normalize_review_sources( array $raw_review_sources ) {
		$review_sources = array();

		foreach ( $raw_review_sources as $raw_review_source ) {
			$review_sources[] = $this->normalize_review_source( $raw_review_source['restaurant'] );
		}

		return $review_sources;
	}

	/**
	 * Normalizes and sanitizes a raw review source from the platform API.
	 *
	 * @since 1.3.0
	 *
	 * @param array $raw_review_source Raw data from platform API.
	 *
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
		if ( isset( $r['id'] ) ) {
			$review_source_id = $this->clean( $r['id'] );
		}

		// Set components.
		$c['name']         = isset( $r['name'] ) ? $this->clean( $r['name'] ) : '';
		$c['url']          = isset( $r['url'] ) ? $this->clean( $r['url'] ) : '';
		$c['rating']       = isset( $r['user_rating']['aggregate_rating'] ) ? $this->clean( $r['user_rating']['aggregate_rating'] ) : '';
		$c['rating_count'] = isset( $r['user_rating']['votes'] ) ? $this->clean( round($r['user_rating']['votes'] ) ) : '';

		// Set image.
		if ( isset( $r['thumb'] ) ) {
			$c['image'] = $this->clean( $r['thumb'] );
		}

		// Set formatted address.
		if ( isset( $r['location']['address'] ) ) {
			$formatted_address = $this->clean( $r['location']['address'] );
		}

		// Set street address.
		if ( isset( $r['location']['address'] ) ) {
			$address['street_address'] = $this->clean( $r['location']['address'] );
		}

		// Set city.
		if ( isset( $r['location']['city'] ) ) {
			$address['city'] = $this->clean( $r['location']['city'] );
		}

		// Set state.
		if ( isset( $r['location']['state'] ) ) {
			$address['state_province'] = $this->clean( $r['location']['state'] );
		}

		// Set postal code.
		if ( isset( $r['location']['zipcode'] ) ) {
			$address['postal_code'] = $this->clean( $r['location']['zipcode'] );
		}

		// Set country.w
		if ( isset( $r['location']['country_id'] ) ) {
			$address['country'] = $this->clean( $r['location']['country_id'] );
		}

		// Set latitude.
		if ( isset( $r['location']['latitude'] ) ) {
			$coordinates['latitude'] = $this->clean( $r['location']['latitude'] );
		}

		// Set longitude.
		if ( isset( $r['location']['longitude'] ) ) {
			$coordinates['longitude'] = $this->clean( $r['location']['longitude'] );
		}

		// Set phone.
		// TODO: Zomato doesn't offer phone number to non-partner API keys and I don't know what they mean in the API about CSV format.
		if ( isset( $r['display_phone'] ) ) {
			$phone = $this->clean( $r['display_phone'] );
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
	 * @since 1.3.0
	 *
	 * @param array  $raw_review       Raw data from platform API.
	 * @param string $review_source_id Review source ID associated with the review.
	 *
	 * @return Review Normalized Review object.
	 */
	public function normalize_review( array $raw_review, $review_source_id ) {
		$review = null;
		$r      = $raw_review['review'];
		$c      = Review::get_default_components();

		// Set review URL.
		if ( isset( $r['id'] ) ) {
			$c['review_url'] = 'https://www.zomato.com/review/' . $this->clean( $r['id'] );
		}

		// Set reviewer name.
		if ( isset( $r['user']['name'] ) ) {
			$c['reviewer_name'] = $this->clean( $r['user']['name'] );
		}

		// Set reviewer image.
		if ( isset( $r['user']['profile_image'] ) ) {
			$c['reviewer_image'] = $this->clean( $r['user']['profile_image'] );
		}

		// Set rating or fall back to recommendation type.
		if ( isset( $r['rating'] ) && 0 !== $r['rating'] ) {
			$c['rating'] = sprintf( '%.1f', floatval( $r['rating'] ) );
		}

		// Set timestamp based on Zomato platform time which is PST.
		if ( isset( $r['timestamp'] ) ) {
			$wp_date_format     = get_option( 'date_format' );
			$platform_timestamp = $this->clean( $r['timestamp'] );
			$utc_timestamp      = date( 'Y-m-d H:i:s', $platform_timestamp );
			$offset_timestamp   = get_date_from_gmt( $utc_timestamp );

			$c['timestamp']      = $utc_timestamp;
			$c['formatted_date'] = date_i18n( $wp_date_format, strtotime( $offset_timestamp ) );
		}

		// Set content.
		if ( isset( $r['review_text'] ) ) {
			$c['content'] = $this->clean_multiline( $r['review_text'] );
		}

		$review = new Review( $this->platform, $review_source_id, $c );

		return $review;
	}

}
