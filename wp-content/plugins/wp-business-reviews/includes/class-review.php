<?php
/**
 * Defines the Review class
 *
 * @link https://wpbusinessreviews.com
 *
 * @package WP_Business_Reviews\Includes
 * @since 0.1.0
 */

namespace WP_Business_Reviews\Includes;

/**
 * Represents a single review associated with a review source.
 *
 * @since 0.1.0
 *
 * @property $platform string
 * @property $components array
 */
class Review implements \JsonSerializable {
	/**
	 * Platform.
	 *
	 * @since 0.1.0
	 * @var string $platform
	 */
	public $platform;

	/**
	 * Review Source ID.
	 *
	 * @since 0.1.0
	 * @var string $review_source_id
	 */
	public $review_source_id;

	/**
	 * Review components.
	 *
	 * @since 0.1.0
	 * @var array $components
	 */
	public $components;

	/**
	 * Instantiates the Review object.
	 *
	 * @since 1.2.1 Add formatted date component.
	 * @since 1.2.0 Add review source name and URL components.
	 * @since 0.1.0
	 *
	 * @param string $platform Platform ID.
	 * @param string $review_source_id Review Source ID.
	 * @param array $components {
	 *     Components used to render the review.
	 *
	 *     @type string $content               Optional. Review content.
	 *     @type string $formatted_date        Optional. WordPress-formatted date.
	 *     @type mixed  $rating                Optional. Rating or recommendation type.
	 *     @type string $review_url            Optional. Review URL.
	 *     @type string $review_source_name    Optional. Name of the review source.
	 *     @type string $review_source_url     Optional. URL of the review source.
	 *     @type string $reviewer_image        Optional. URL of the reviewer image.
	 *     @type int    $reviewer_image_custom Optional. ID of the reviewer image.
	 *     @type string $reviewer_name         Optional. Name of the reviewer.
	 *     @type string $timestamp             Optional. Unix timestamp of the review.
	 * }
	 */
	public function __construct(
		$platform,
		$review_source_id,
		array $components,
		$post_id = 0
	) {
		$this->platform         = $platform;
		$this->review_source_id = $review_source_id;
		$this->components       = $components;
		$this->post_id          = $post_id;
	}

	/**
	 * Retrieves default values for review components.
	 *
	 * @since 1.2.1 Add formatted date component.
	 * @since 1.2.0 Add review source name and URL components.
	 * @since 0.1.0
	 *
	 * @return array Associative array of components.
	 */
	public static function get_default_components() {
		return array(
			'content'               => null,
			'formatted_date'        => null,
			'rating'                => 0,
			'review_url'            => null,
			'review_source_name'    => null,
			'review_source_url'     => null,
			'reviewer_image'        => null,
			'reviewer_image_custom' => null,
			'reviewer_name'         => null,
			'timestamp'             => null,
		);
	}

	/**
	 * Retrieves URL of custom reviewer image.
	 *
	 * When the Review object is initialized, a custom image is stored as an ID,
	 * but it must be converted to a URL for front-end consumption.
	 *
	 * @return string URL of the custom image.
	 */
	public function get_custom_reviewer_image_url() {
		$url = wp_get_attachment_url( $this->components['reviewer_image_custom'] );

		/**
		 * Filters the URL of the custom reviewer image.
		 *
		 * @since 0.1.0
		 *
		 * @param string $url      URL of the custom reviewer image.
		 * @param string $platform Platform of the review source.
		 * @param string $post_id  ID of the review source.
		 */
		return apply_filters( 'wpbr_reviewer_image_custom_url', $url, $this->platform, $this->post_id );
	}

	/**
	 * Retrieve platform in kebob-case.
	 *
	 * @since 0.1.0
	 *
	 * @return string Platform string in kebob-case.
	 */
	public function get_platform_slug() {
		return str_replace( '_', '-', $this->platform );
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
	 * Retrieves the post parent ID of the review.
	 *
	 * The post parent ID should point to the WordPress post associated with
	 * a review source such as a Google Place of Facebook Page.
	 *
	 * @since 1.1.0
	 *
	 * @return int|false Post parent ID, otherwise false.
	*/
	public function get_post_parent() {
		return wp_get_post_parent_id( $this->post_id );
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
		$props = get_object_vars( $this );

		// Convert custom image ID to URL prior to serialization.
		if ( ! empty( $props['components']['reviewer_image_custom'] ) ) {
			$props['components']['reviewer_image_custom'] = $this->get_custom_reviewer_image_url();
		}

		return $props;
	}
}
