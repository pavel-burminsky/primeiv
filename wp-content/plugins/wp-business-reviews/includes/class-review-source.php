<?php
/**
 * Defines the Review_Source class
 *
 * @link https://wpbusinessreviews.com
 *
 * @package WP_Business_Reviews\Includes
 * @since 0.1.0
 */

namespace WP_Business_Reviews\Includes;

use WP_Business_Reviews\Includes\Location;

/**
 * Represents a review source from which reviews originate.
 *
 * @since 0.1.0
 */
class Review_Source implements \JsonSerializable {
	/**
	 * Post ID.
	 *
	 * @since 1.3.0 Changed visibility to public.
	 * @since 0.1.0
	 * @var string $post_id
	 */
	public $post_id;

	/**
	 * Platform.
	 *
	 * @since 1.3.0 Changed visibility to public.
	 * @since 0.1.0
	 * @var string $platform
	 */
	public $platform;

	/**
	 * Review Source ID.
	 *
	 * @since 1.3.0 Changed visibility to public.
	 * @since 0.1.0
	 * @var string $review_source_id
	 */
	public $review_source_id;

	/**
	 * Review source components.
	 *
	 * @since 1.3.0 Changed visibility to public.
	 * @since 0.1.0
	 * @var array $components
	 */
	public $components;

	/**
	 * Instantiates the Review_Source object.
	 *
	 * @since 0.1.0
	 *
	 * @param int      $post_id          Post ID.
	 * @param string   $platform         Platform ID.
	 * @param string   $review_source_id Review source ID.
	 * @param array    $components {
	 *     Review Source components.
	 *
	 *     @type string   $name         Name of the review source.
	 *     @type string   $url          URL of the review source on the platform.
	 *     @type float    $rating       Overall rating.
	 *     @type int      $rating_count Total number of ratings.
	 *     @type string   $image        Review source image.
	 *     @type Location $location     Location object.
	 * }
	 */
	public function __construct(
		$post_id,
		$platform,
		$review_source_id,
		array $components
	) {
		$this->post_id          = $post_id;
		$this->platform         = $platform;
		$this->review_source_id = $review_source_id;
		$this->components       = wp_parse_args(
			$components,
			$this->get_default_components()
		);
	}

	/**
	 * Retrieves default values for review source components.
	 *
	 * @since 0.1.0
	 *
	 * @return array Associative array of components.
	 */
	public static function get_default_components() {
		return array(
			'name'         => null,
			'url'          => null,
			'rating'       => null,
			'rating_count' => null,
			'image'        => null,
			'location'     => null,
		);
	}

	/**
	 * Retrieves the post ID of the review source.
	 *
	 * @since 1.1.0
	 *
	 * @return int The post ID.
	 */
	public function get_post_id() {
		return $this->post_id;
	}

	/**
	 * Retrieves the review platform.
	 *
	 * @since 1.1.0
	 *
	 * @return string The platform ID.
	 */
	public function get_platform() {
		return $this->platform;
	}

	/**
	 * Retrieves the review source ID.
	 *
	 * Examples include a Place ID for Google or a Page ID for Facebook.
	 *
	 * @since 1.1.0
	 *
	 * @return string The review source ID.
	 */
	public function get_review_source_id() {
		return $this->review_source_id;
	}

	/**
	 * Retrieves all review source components.
	 *
	 * @since 1.1.0
	 *
	 * @return array The review source components.
	 */
	public function get_components() {
		return $this->components;
	}

	/**
	 * Retrieves the value of a single review source component.
	 *
	 * @since 1.1.0
	 *
	 * @return mixed The value of the review source component or null.
	 */
	public function get_component( $component ) {
		if ( isset( $this->components[ $component ] ) ) {
			return $this->components[ $component ];
		}

		return null;
	}

	/**
	 * Prepares object for JSON serialization.
	 *
	 * @since 0.1.0
	 *
	 * @return array Array of object properties.
	 */
    #[\ReturnTypeWillChange]
	public function jsonSerialize() {
        return get_object_vars( $this );
    }
}
