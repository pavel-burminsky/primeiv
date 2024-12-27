<?php
/**
 * Defines the Builder_Preview class
 *
 * @link https://wpbusinessreviews.com
 *
 * @package WP_Business_Reviews\Includes\Builder
 * @since 0.1.0
 */

namespace WP_Business_Reviews\Includes\Builder;

use WP_Business_Reviews\Includes\Collection;
use WP_Business_Reviews\Includes\View;

/**
 * Previews the collection being built.
 *
 * @since 0.1.0
 */
class Builder_Preview {
	/**
	 * Collection of reviews data and presentation settings.
	 *
	 * @since 0.1.0
	 * @var Collection
	 */
	protected $collection;

	/**
	 * Instantiates the Builder_Preview object.
	 *
	 * @since 0.1.0
	 *
	 * @param Collection $collection Optional. Review collection.
	 */
	public function __construct( Collection $collection = null ) {
		$this->collection = $collection;
	}

	/**
	 * Sets the collection.
	 *
	 * @since 0.1.0
	 *
	 * @param Collection $collection Review collection.
	 */
	public function set_collection( $collection ) {
		$this->collection = $collection;
	}

	/**
	 * Renders the preview UI.
	 *
	 * @since  0.1.0
	 */
	public function render() {
		$view_object = new View( WPBR_PLUGIN_DIR . 'views/builder/preview.php' );

		$view_object->render(
			array(
				'collection' => $this->collection,
			)
		);
	}
}
