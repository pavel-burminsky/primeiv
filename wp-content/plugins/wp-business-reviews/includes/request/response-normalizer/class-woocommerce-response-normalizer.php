<?php
/**
 * Defines the WooCommerce_Response_Normalizer class
 *
 * @link https://wpbusinessreviews.com
 *
 * @package WP_Business_Reviews\Includes\Request\Response_Normalizer
 * @since 1.5.0
 */

namespace WP_Business_Reviews\Includes\Request\Response_Normalizer;

use WP_Business_Reviews\Includes\Review;
use WP_Business_Reviews\Includes\Review_Source;
use WP_Business_Reviews\Includes\Location;
use \DateTime;
use \DateTimeZone;

/**
 * Normalizes the structure of a WooCommerce query.
 *
 * @since 1.5.0
 */
class WooCommerce_Response_Normalizer extends Response_Normalizer_Abstract {

	/**
	 * Platform.
	 *
	 * @since 1.5.0
	 * @var string $platform
	 */
	protected $platform = 'woocommerce';

	/**
	 * WooCommerce only has one review source. Passes along the raw review source.
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
	 * @return Review_Source Normalized review source object.
	 * @since 1.5.0
	 *
	 */
	public function normalize_review_source( array $raw_review_source ) {
		$review_source    = null;
		$r                = $raw_review_source;
		$c                = Review_Source::get_default_components();
		$review_source_id = '';

		// Set ID of the review source on the platform.
		if ( isset( $r['id'] ) ) {
			$review_source_id = $this->clean( $r['id'] );
		}

		// Set components.
		$c['name']         = isset( $r['name'] ) ? $this->clean( $r['name'] ) : '';
		$c['url']          = isset( $r['url'] ) ? $this->clean( $r['url'] ) : '';
		$c['rating']       = isset( $r['rating'] ) ? $this->clean( $r['rating'] ) : '';
		$c['rating_count'] = isset( $r['review_count'] ) ? $this->clean( $r['review_count'] ) : '';
		$c['image']        = isset( $r['image_url'] ) ? $this->clean( $r['image_url'] ) : '';
		$c['location']     = new Location( '', [], [], '' );

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
	 * @param array $raw_review Raw data from platform API.
	 * @param string $review_source_id Review source ID associated with the review.
	 *
	 * @return Review Normalized review object.
	 * @since 1.5.0
	 *
	 */
	public function normalize_review( array $raw_review, $review_source_id ) {
		$review = null;
		$r      = $raw_review;
		$c      = Review::get_default_components();

		// Set reviewer name.
		if ( isset( $r['reviewer_name'] ) ) {
			$c['reviewer_name'] = $this->clean( $r['reviewer_name'] );
		}

		// Set reviewer image.
		if ( isset( $r['reviewer_image'] ) ) {
			$c['reviewer_image'] = $this->clean( $r['reviewer_image'] );
		}

		// Set rating.
		if ( isset( $r['rating'] ) ) {
			$c['rating'] = $this->clean( $r['rating'] );
		}

		// Set review URL.
		if ( isset( $r['review_url'] ) ) {
			$c['review_url'] = $this->clean( $r['review_url'] );
		}

		// Set timestamp based on WooCommerce platform time which is GMT.
		if ( isset( $r['time_created'] ) ) {
			$wp_date_format     = get_option( 'date_format' );
			$platform_timestamp = $this->clean( $r['time_created'] );
			$platform_date_time = new DateTime( $platform_timestamp, new DateTimeZone( 'America/Los_Angeles' ) );
			$platform_date_time->setTimezone( new DateTimeZone( 'UTC' ) );
			$utc_timestamp    = $platform_date_time->format( 'Y-m-d H:i:s' );
			$offset_timestamp = get_date_from_gmt( $utc_timestamp );

			$c['timestamp']      = $utc_timestamp;
			$c['formatted_date'] = date_i18n( $wp_date_format, strtotime( $offset_timestamp ) );
		}

		// Set content.
		if ( isset( $r['review_content'] ) ) {
			$c['content'] = $this->clean_multiline( $r['review_content'] );
		}

		$review = new Review( $this->platform, $review_source_id, $c );

		return $review;
	}


}
