<?php
/**
 * Defines the Collection_Shortcode class
 *
 * @link https://wpbusinessreviews.com
 *
 * @package WP_Business_Reviews\Includes\Shortcode
 * @since 0.1.0
 */

namespace WP_Business_Reviews\Includes\Shortcode;

use WP_Business_Reviews\Includes\Deserializer\Collection_Deserializer as Deserializer;

/**
 * Outputs a result as defined by a Blueprint.
 *
 * @since 0.1.0
 */
class Collection_Shortcode {
	/**
	 * Collection deserializer.
	 *
	 * @since 0.1.0
	 * @var Deserializer $deserializer
	 */
	private $deserializer;

	/**
	 * Instantiates the Collection_Shortcode object.
	 *
	 * @since 0.1.0
	 *
	 * @param Deserializer $deserializer Retriever of collections.
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
		add_shortcode( 'wpbr_collection', array( $this, 'init' ) );
	}

	/**
	 * Initializes the Collection.
	 *
	 * @since 0.1.0
	 *
	 * @param array $atts {
	 *     Shortcode attributes.
	 *
	 *     @type int $id Collection post ID.
	 * }
	 */
	public function init( $atts ) {
		$atts = shortcode_atts( array(
			'id' => 0,
		), $atts, 'wpbr_collection' );

		$collection = $this->deserializer->get_collection( $atts['id'] );

		if ( ! $collection ) {
			return null;
		}

		// Ensure scripts are loaded to render shortcode.
		wp_enqueue_script( 'wpbr-public-main-script' );
		wp_enqueue_style( 'wpbr-public-main-styles' );

		$collection = $this->deserializer->hydrate_review_sources( $collection );
		$collection = $this->deserializer->hydrate_reviews( $collection );

		return $collection->render( false );
	}
}
