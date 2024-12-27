<?php
/**
 * Defines the Google_Places_Response_Normalizer class
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

/**
 * Normalizes the structure of a Google Places API response.
 *
 * @since 0.1.0
 */
class Google_Places_Response_Normalizer extends Response_Normalizer_Abstract {
	/**
	 * Platform.
	 *
	 * @since 0.1.0
	 * @var string $platform
	 */
	protected $platform = 'google_places';

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

		// Set review source ID.
		if ( isset( $r['place_id'] ) ) {
			$review_source_id = $this->clean( $r['place_id'] );
		}

		// Set components.
		$c['name']   = isset( $r['name'] ) ? $this->clean( $r['name'] ) : '';
		$c['url']    = isset( $r['url'] )  ? $this->clean( $r['url'] ) : '';
		$c['rating'] = isset( $r['rating'] ) ? $this->clean( $r['rating'] ) : '';

		// Set formatted address.
		if ( isset( $r['formatted_address'] ) ) {
			$formatted_address = $this->clean( $r['formatted_address'] );
		}

		// Set address properties.
		if ( isset( $r['address_components'] ) ) {
			// Parse address components per Google Places' unique format.
			$address_components = $this->parse_address_components(
				$this->clean( $r['address_components'] )
			);

			// Assemble normalized street address since it is not provided as a single field.
			$address['street_address'] = $this->normalize_street_address(
				$address_components
			);

			if ( isset( $address_components['city'] ) ) {
				$address['city'] = $address_components['city'];
			}

			if ( isset( $address_components['state_province'] ) ) {
				$address['state_province'] = $address_components['state_province'];
			}

			if ( isset( $address_components['postal_code'] ) ) {
				$address['postal_code'] = $address_components['postal_code'];
			}

			if ( isset( $address_components['country'] ) ) {
				$address['country'] = $address_components['country'];
			}
		}

		// Set coordinates.
		if ( isset( $r['geometry']['location']['lat'] ) ) {
			$coordinates['latitude'] = $this->clean( $r['geometry']['location']['lat'] );
		}

		// Set longitude.
		if ( isset( $r['geometry']['location']['lng'] ) ) {
			$coordinates['longitude'] = $this->clean( $r['geometry']['location']['lng'] );
		}

		// Set phone.
		if ( isset( $r['formatted_phone_number'] ) ) {
			$phone = $this->clean( $r['formatted_phone_number'] );
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
		$r      = $raw_review;
		$c      = Review::get_default_components();

		// Set review URL.
		if ( isset( $r['author_url'] ) ) {
			$c['review_url'] = $this->generate_review_url(
				$this->clean( $r['author_url'] ),
				$review_source_id
			);
		}

		// Set reviewer name.
		if ( isset( $r['author_name'] ) ) {
			$c['reviewer_name'] = $this->clean( $r['author_name'] );
		}

		// Set reviewer image.
		if ( isset( $r['profile_photo_url'] ) ) {
			$c['reviewer_image'] = $this->clean( $r['profile_photo_url'] );
		}

		// Set rating.
		if ( isset( $r['rating'] ) ) {
			$c['rating'] = $this->clean( $r['rating'] );
		}

		// Set timestamp based on Google Places platform which is Unix time.
		if ( isset( $r['time'] ) ) {
			$wp_date_format     = get_option( 'date_format' );
			$platform_timestamp = $this->clean( $r['time'] );
			$utc_timestamp      = date( 'Y-m-d H:i:s', $platform_timestamp );
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
	 * Generates the review URL using the author URL and Place ID.
	 *
	 * @since 0.1.0
	 *
	 * @param string $author_url The reviewer's author URL.
	 * @param string $place_id   The Place ID of the review source.
	 * @return string URL of the review.
	 */
	private function generate_review_url( $author_url, $place_id ) {
		$author_url_home = str_replace( '/reviews', '', $author_url );

		return "{$author_url_home}/place/{$place_id}";
	}

	/**
	 * Normalize street address from Google Places API address components.
	 *
	 * @since 0.1.0
	 *
	 * @param array $address_components Address parts organized by type.
	 * @return string Street address where the Place is located.
	 */
	protected function normalize_street_address( $address_components ) {
		$street_number  = isset( $address_components['street_number'] ) ? $address_components['street_number'] . ' ' : '';
		$route          = isset( $address_components['route'] ) ? $address_components['route'] : '';
		$subpremise     = isset( $address_components['subpremise'] ) ? ' #' . $address_components['subpremise'] : '';
		$street_address = $street_number . $route . $subpremise;

		return $street_address;
	}

	/**
	 * Parse address components specific to the Google Places address format.
	 *
	 * The Google Places API response does not always include the same number
	 * of address components in the same order, so they need parsed by type
	 * before constructing the full address.
	 *
	 * @since 0.1.0
	 *
	 * @param array $address_components Address parts that form a full address.
	 * @return array Address parts organized by type.
	 */
	protected function parse_address_components( array $address_components ) {
		$formatted_components = array();

		foreach ( $address_components as $component ) {
			switch ( $component['types'][0] ) {
				case 'subpremise' :
					$formatted_components['subpremise'] = $component['short_name'];
					break;
				case 'street_number' :
					$formatted_components['street_number'] = $component['short_name'];
					break;
				case 'route' :
					$formatted_components['route'] = $component['short_name'];
					break;
				case 'locality' :
					$formatted_components['city'] = $component['short_name'];
					break;
				case 'administrative_area_level_1' :
					$formatted_components['state_province'] = $component['short_name'];
					break;
				case 'country' :
					$formatted_components['country'] = $component['short_name'];
					break;
				case 'postal_code' :
					$formatted_components['postal_code'] = $component['short_name'];
					break;
			}
		}

		return $formatted_components;
	}
}
