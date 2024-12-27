<?php
/**
 * Defines the Collection class
 *
 * @link https://wpbusinessreviews.com
 *
 * @package WP_Business_Reviews\Includes
 * @since 0.1.0
 */

namespace WP_Business_Reviews\Includes;

use WP_Business_Reviews\Includes\View;

/**
 * Stores a collection of reviews and settings to determine their presentation.
 *
 * @since 0.1.0
 */
class Collection {
	/**
	 * Platform associated with the collection.
	 *
	 * @since 0.1.0
	 * @var string $platform
	 */
	protected $platform;

	/**
	 * Post ID of the collection.
	 *
	 * @since 0.1.0
	 * @var int $post_id
	 */
	protected $post_id;

	/**
	 * Title of the Collection.
	 *
	 * @since 0.1.0
	 * @var string $title
	 */
	protected $title;

	/**
	 * Array of Collection settings.
	 *
	 * These settings determine Review presentation and filtering.
	 *
	 * @since 0.1.0
	 * @var array $settings
	 */
	protected $settings;

	/**
	 * Array of Review_Source objects.
	 *
	 * @since 0.1.0
	 * @var Review_Source[] $review_sources
	 */
	protected $review_sources;

	/**
	 * Array of Review objects.
	 *
	 * @since 0.1.0
	 * @var Review[] $reviews
	 */
	protected $reviews;

	/**
	 * Unique identifier to differentiate between multiple Collection objects.
	 *
	 * This uniqued ID may be passed by a widget or shortcode, which allows more
	 * than one Collection to appear on screen.
	 *
	 * @since 0.1.0
	 * @var string $unique_id
	 */
	protected $unique_id;

	/**
	 * Instantiates the Collection object.
	 *
	 * @since 0.1.0
	 *
	 * @param int          $post_id      Post ID of the collection.
	 * @param string       $platform     Platform.
	 * @param string       $title        Title of the collection.
	 * @param array        $settings {
	 *     Collection settings.
	 *
	 *     @type string $blank reviews     Optional. Whether blank reviews are enabled.
	 *     @type string $format            Optional. The presentation format.
	 *     @type string $line_breaks       Optional. Whether line breaks are enabled.
	 *     @type string $max_characters    Optional. Maximum characters before truncation.
	 *     @type string $max_columns       Optional. Maximum columns.
	 *     @type string $max_reviews       Optional. Maximum number of reviews.
	 *     @type string $min_rating        Optional. Minimum rating to display (0-101).
	 *     @type string $order             Optional. Ascending or descending order.
	 *     @type string $orderby           Optional. Parameter by which reviews are ordered.
	 *     @type array  $post_parent       Optional. Post ID of a review source.
	 *     @type array  $review_components Optional. Array of enabled components.
	 *     @type array  $review_tags       Optional. Review tags for taxonomy query.
	 *     @type array  $review_type       Optional. All, ratings or recommendations.
	 *     @type string $slides_per_view   Optional. Maximum slides per view in carousel.
	 *     @type string $style             Optional. The aesthetic look of the collection.
	 * }
	 */
	public function __construct(
		$post_id,
		$platform,
		$title,
		$settings
	) {
		$this->post_id      = $post_id;
		$this->platform     = $platform;
		$this->title        = $title;
		$this->settings     = $settings;
		$this->unique_id    = wp_rand();
	}

	/**
	 * Gets the default settings for a collection.
	 *
	 * These settings correspond with those provided in the builder config.
	 *
	 * @since 0.1.0
	 */
	public static function get_default_settings() {
		/*
		 * TODO: Source these defaults from the builder config so they're not defined
		 * in two places.
		 */
		return array(
			'blank_reviews'     => 'enabled',
			'format'            => 'review_gallery',
			'line_breaks'       => 'disabled',
			'max_characters'    => 280,
			'max_columns'       => 2,
			'max_reviews'       => 12,
			'min_rating'        => 0,
			'order'             => 'desc',
			'orderby'           => 'review_date',
			'post_parent'       => 0,
			'review_components' => array(
				'reviewer_image'   => 'enabled',
				'reviewer_name'    => 'enabled',
				'rating'           => 'enabled',
				'recommendation'   => 'enabled',
				'timestamp'        => 'enabled',
				'content'          => 'enabled',
				'platform_icon'    => 'enabled',
			),
			'review_tags'       => array(),
			'review_type'       => 'all',
			'slides_per_view'   => 3,
			'style'             => 'light',
		);
	}

	/**
	 * Prints the Collection object as a JavaScript object.
	 *
	 * This makes the Collection available to other scripts on the front end
	 * of the WordPress website.
	 *
	 * @since 0.1.0
	 */
	public function print_js_object( $handle ) {
		wp_localize_script(
			$handle,
			'wpbrCollection' . $this->unique_id,
			array(
				'settings'       => $this->get_settings(),
				'reviews'        => $this->get_reviews(),
				'review_sources' => $this->get_review_sources(),
			)
		);
	}

	/**
	 * Retrieves the platform associated with the collection.
	 *
	 * @since 0.1.0
	 *
	 * @return string Platform.
	 */
	public function get_platform() {
		return $this->platform;
	}

	/**
	 * Retrieves the post ID of the Collection.
	 *
	 * @since 0.1.0
	 *
	 * @return int Post ID of the collection.
	 */
	public function get_post_id() {
		return $this->post_id;
	}

	/**
	 * Retrieves the title of the Collection.
	 *
	 * @since 0.1.0
	 *
	 * @return string Title of the Collection.
	 */
	public function get_title() {
		return $this->title;
	}

	/**
	 * Retrieves the collection settings.
	 *
	 * @since 0.1.0
	 *
	 * @return array Array of collection settings.
	 */
	public function get_settings() {
		return $this->settings;
	}

	/**
	 * Retrieves an array of review sources.
	 *
	 * @since 0.1.0
	 *
	 * @return Review_Source[] Array of Review_Source objects.
	 */
	public function get_review_sources() {
		return $this->review_sources;
	}

	/**
	 * Retrieves an array of reviews.
	 *
	 * @since 0.1.0
	 *
	 * @return Review[] Array of Review objects.
	 */
	public function get_reviews() {
		return $this->reviews;
	}

	/**
	 * Sets an array of review sources.
	 *
	 * @since 0.1.0
	 *
	 * @param Review_Sources[] Array of Review_Source objects.
	 */
	public function set_review_sources( $review_sources ) {
		$this->review_sources = $review_sources;
	}

	/**
	 * Sets an array of reviews.
	 *
	 * @since 0.1.0
	 *
	 * @param Review[] Array of Review objects.
	 */
	public function set_reviews( $reviews ) {
		$this->reviews = $reviews;
	}

	/**
	 * Renders a given view.
	 *
	 * @since 0.1.0
	 *
	 * @param bool $echo Optional. Whether to echo the output immediately. Defaults to true.
	 */
	public function render( $echo = true ) {
		$view_object = new View( WPBR_PLUGIN_DIR . 'views/collection.php' );

		return $view_object->render(
			array(
				'unique_id' => $this->unique_id,
                'data' => json_encode([
                    'settings'       => $this->get_settings(),
                    'reviews'        => $this->get_reviews(),
                    'review_sources' => $this->get_review_sources()
                ]),
			),
			$echo
		);
	}
}
