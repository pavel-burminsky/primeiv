<?php
/**
 * Defines the Facebook_Response_Normalizer class
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
 * Normalizes the structure of a Facebook API response.
 *
 * @since 0.1.0
 */
class Facebook_Response_Normalizer extends Response_Normalizer_Abstract {
	/**
	 * Platform.
	 *
	 * @since 0.1.0
	 * @var string $platform
	 */
	protected $platform = 'facebook';

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

			// Use `$review_source_id` to get the image.
			$c['image'] = $this->generate_image_url( $review_source_id );
		}

		// Set components.
		$c['name']         = isset( $r['name'] ) ? $this->clean( $r['name'] ) : '';
		$c['url']          = isset( $r['link'] ) ? $this->clean( $r['link'] ) : '';
		$c['rating']       = isset( $r['overall_star_rating'] ) ? $this->clean( $r['overall_star_rating'] ) : '';
		$c['rating_count'] = isset( $r['rating_count'] ) ? $this->clean( $r['rating_count'] ) : '';

		// Set formatted address.
		if ( isset( $r['single_line_address'] ) ) {
			$formatted_address = $this->clean( $r['single_line_address'] );
		}

		// Set street address.
		if ( isset( $r['location']['street'] ) ) {
			$address['street_address'] = $this->clean( $r['location']['street'] );
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
		if ( isset( $r['location']['zip'] ) ) {
			$address['postal_code'] = $this->clean( $r['location']['zip'] );
		}

		// Set country.
		if ( isset( $r['location']['country'] ) ) {
			$address['country'] = $this->clean( $r['location']['country'] );
		}

		// Set latitude.
		if ( isset( $r['location']['latitude']) ) {
			$coordinates['latitude'] = $this->clean( $r['location']['latitude'] );
		}

		// Set longitude.
		if ( isset( $r['location']['longitude']) ) {
			$coordinates['longitude'] = $this->clean( $r['location']['longitude'] );
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
	 * @param string $review_source_id Review Source ID associated with the review.
	 * @return Review Normalized review object.
	 */
	public function normalize_review( array $raw_review, $review_source_id ) {
		$review = null;
		$r = $raw_review;
		$c = Review::get_default_components();

		// Set review URL.
		if ( isset( $r['open_graph_story']['id'] ) ) {
			$c['review_url'] = $this->generate_review_url(
				$this->clean( $r['open_graph_story']['id'] )
			);
		}

		// Set reviewer name.
		if ( isset( $r['reviewer']['name'] ) ) {
			$c['reviewer_name'] = $this->clean( $r['reviewer']['name'] );
		}

		// Set reviewer image.
		if ( isset( $r['reviewer']['picture'] ) ) {
			$c['reviewer_image'] = $this->clean( $r['reviewer']['picture']['data']['url'] );
		}

		// Set rating or fall back to recommendation type.
		if ( isset( $r['rating'] ) ) {
			$c['rating'] = $this->clean( $r['rating'] );
		} elseif ( isset( $r['recommendation_type'] ) ) {
			$c['rating'] = $this->clean( $r['recommendation_type'] );
		}

		// Set timestamp based on Facebook platform time which is UTC.
		if ( isset( $r['created_time'] ) ) {
			$wp_date_format     = get_option( 'date_format' );
			$platform_timestamp = $this->clean( $r['created_time'] );
			$platform_date_time = new DateTime( $platform_timestamp );
			$utc_timestamp      = $platform_date_time->format( 'Y-m-d H:i:s' );
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

	/**
	 * Generates the review URL using the review's Open Graph Story ID.
	 *
	 * @since 0.1.0
	 *
	 * @param int $id The Facebook review's Open Graph Story ID.
	 * @return string URL of the review.
	 */
	private function generate_review_url( $open_graph_story_id ) {
		return "https://www.facebook.com/{$open_graph_story_id}";
	}

	/**
	 * Generates the image URL using a Facebook user ID.
	 *
	 * Facebook image URLs should be stored using the 302 redirect format returned
	 * by this method instead of the actual image URL which is prone to expiration.
	 *
	 * @since 1.2.0 Rename $page_id to $user_id and add $dimensions.
	 * @since 0.1.0
	 *
	 * @link https://developers.facebook.com/docs/graph-api/reference/user/picture/
	 *
	 * @param int $user_id The Facebook user ID of a page or reviewer.
	 * @return string A 302 redirect to the picture image.
	 */
	private function generate_image_url( $user_id, $dimensions = array( 120, 120 ) ) {
		/** This filter is documented in includes/request/class-facebook-request.php */
		$dimensions = apply_filters( 'wpbr_facebook_picture_dimensions', $dimensions );

		return "https://graph.facebook.com/{$user_id}/picture?width={$dimensions[0]}&height={$dimensions[1]}";
	}
}
