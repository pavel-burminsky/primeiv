<?php
/**
 * Defines the Review_Shortcode class
 *
 * @link https://wpbusinessreviews.com
 *
 * @package WP_Business_Reviews\Includes\Shortcode
 * @since 0.1.0
 */

namespace WP_Business_Reviews\Includes\Shortcode;

use WP_Business_Reviews\Includes\Deserializer\Review_Deserializer as Deserializer;
use WP_Business_Reviews\Includes\View;

/**
 * Outputs a review.
 *
 * @since 0.1.0
 */
class Review_Shortcode {
	/**
	 * Review deserializer.
	 *
	 * @since 0.1.0
	 * @var Deserializer $deserializer
	 */
	private $deserializer;

	/**
	 * Instantiates the Review_Shortcode object.
	 *
	 * @since 0.1.0
	 *
	 * @param Deserializer $deserializer Retriever of reviews.
	 */
	public function __construct( Deserializer $deserializer ) {
		$this->deserializer = $deserializer;
	}

	/**
	 * Registers functionality with WordPress hooks.
	 *
	 * @since 0.1.0
	 */
	public function register() {
		add_shortcode( 'wpbr_review', array( $this, 'init' ) );
	}

	/**
	 * Initializes the Review.
	 *
	 * @since 0.1.0
	 *
	 * @param array $atts {
	 *     Shortcode attributes.
	 *
	 *     @type int $id Review post ID.
	 * }
	 *
	 * @return string HTML output for JS.
	 */
	public function init( $atts ) {
		$atts = shortcode_atts( array(
			'id' => 0,
            'style'             => 'light',
            'format'            => 'review_gallery',
            'max_columns'       => 1,
            'max_characters'    => 280,
            'line_breaks'       => 'disabled',
            'reviewer_image' => 'enabled',
            'reviewer_name'  => 'enabled',
            'rating'         => 'enabled',
            'recommendation' => 'enabled',
            'timestamp'      => 'enabled',
            'content'        => 'enabled',
            'platform_icon'  => 'enabled',
		), $atts, 'wpbr_review' );

		$review = $this->deserializer->get_review( $atts['id'] );

		if ( ! $review ) {
			return null;
		}

        // Defaults
        if ( ! in_array( $atts['style'], ['light', 'dark', 'transparent'] ) ) {
            $atts['style'] = 'light';
        }

        if ( ! in_array( $atts['format'], ['review_gallery', 'review_carousel', 'review_list'] ) ) {
            $atts['format'] = 'review_gallery';
        }


        // Ensure scripts are loaded to render shortcode.
		wp_enqueue_script( 'wpbr-public-main-script' );
		wp_enqueue_style( 'wpbr-public-main-styles' );

		$view_object = new View( WPBR_PLUGIN_DIR . 'views/review.php' );

		return $view_object->render(
			array(
				'unique_id' => $atts['id'],
                'data' => json_encode([
                    'review'   => $review,
                    'settings' => [
                        'post_parent'       => 0,
                        'style'             => $atts['style'],
                        'format'            => $atts['format'],
                        'max_columns'       => $atts['max_columns'],
                        'max_characters'    => $atts['max_characters'],
                        'line_breaks'       => $atts['line_breaks'],
                        'review_components' => [
                            'reviewer_image' => $atts['reviewer_image'],
                            'reviewer_name'  => $atts['reviewer_name'],
                            'rating'         => $atts['rating'],
                            'recommendation' => $atts['recommendation'],
                            'timestamp'      => $atts['timestamp'],
                            'content'        => $atts['content'],
                            'platform_icon'  => $atts['platform_icon'],
                        ],
                    ]
                ])
			),
			false
		);

	}
}
