<?php
/**
 * Defines the Yelp_Response_Normalizer class
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
use \DateTimeZone;

/**
 * Normalizes the structure of a Yelp API response.
 *
 * @since 0.1.0
 */
class Yelp_Response_Normalizer extends Response_Normalizer_Abstract {
	/**
	 * Platform.
	 *
	 * @since 0.1.0
	 * @var string $platform
	 */
	protected $platform = 'yelp';

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
		if ( isset( $r['id'] ) ) {
			$review_source_id = $this->clean( $r['id'] );
		}

		// Set components.
		$c['name']         = isset( $r['name'] ) ? $this->clean( $r['name'] ) : '';
		$c['url']          = isset( $r['url'] ) ? $this->clean( $r['url'] ) : '';
		$c['rating']       = isset( $r['rating'] ) ? $this->clean( $r['rating'] ) : '';
		$c['rating_count'] = isset( $r['review_count'] ) ? $this->clean( $r['review_count'] ) : '';

		// Set image.
		if ( isset( $r['image_url'] ) ) {
			$c['image'] = $this->modify_image_size( $this->clean( $r['image_url'] ) );
		}

		// Set formatted address.
		if ( isset( $r['location']['display_address'] ) ) {
			$formatted_address = $this->format_address(
				$this->clean( $r['location']['display_address'] )
			);
		}

		// Set street address.
		if ( isset( $r['location']['address1'] ) ) {
			$address['street_address'] = $this->clean( $r['location']['address1'] );
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
		if ( isset( $r['location']['zip_code'] ) ) {
			$address['postal_code'] = $this->clean( $r['location']['zip_code'] );
		}

		// Set country.
		if ( isset( $r['location']['country'] ) ) {
			$address['country'] = $this->clean( $r['location']['country'] );
		}

		// Set latitude.
		if ( isset( $r['coordinates']['latitude']) ) {
			$coordinates['latitude'] = $this->clean( $r['coordinates']['latitude'] );
		}

		// Set longitude.
		if ( isset( $r['coordinates']['longitude']) ) {
			$coordinates['longitude'] = $this->clean( $r['coordinates']['longitude'] );
		}

		// Set phone.
		if ( isset( $r['display_phone'] ) ) {
			$phone =  $this->clean( $r['display_phone'] );
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

		// Set review URL.
		if ( isset( $r['url'] ) ) {
			$c['review_url'] = $this->clean( $r['url'] );
		}

		// Set reviewer name.
		if ( isset( $r['user']['name'] ) ) {
			$c['reviewer_name'] = $this->clean( $r['user']['name'] );
		}

		// Set reviewer image.
		if ( isset( $r['user']['image_url'] ) ) {
			$c['reviewer_image'] = $this->modify_image_size(
				$this->clean( $r['user']['image_url'] )
			);
		}

		// Set rating.
		if ( isset( $r['rating'] ) ) {
			$c['rating'] = $this->clean( $r['rating'] );
		}

		// Set timestamp based on Yelp platform time which is PST.
		if ( isset( $r['time_created'] ) ) {
			$wp_date_format     = get_option( 'date_format' );
			$platform_timestamp = $this->clean( $r['time_created'] );
			$platform_date_time = new DateTime( $platform_timestamp, new DateTimeZone( 'America/Los_Angeles' ) );
			$platform_date_time->setTimezone( new DateTimeZone( 'UTC' ) );
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
	 * Modify the image URL from API response.
	 *
	 * The image returned by the Yelp Fusion API is 1000px wide, which is
	 * unnecessarily big for this plugin's purposes. Changing the suffix
	 * results in a more appropriate size.
	 *
	 * @since 0.1.0
	 *
	 * @param string $image Image URL.
	 * @return string Modified image URL.
	 */
	protected function modify_image_size( $image ) {
		return str_replace( 'o.jpg', 'ms.jpg', $image );
	}

	/**
	 * Formats address from separate address components.
	 *
	 * @since 0.1.0
	 *
	 * @param array $address_components Associative array of address strings.
	 * @return string Formatted address.
	 */
	protected function format_address( $address_components ) {
		return trim( implode( ', ', $address_components ) );
	}
}
