<?php
/**
 * Defines the Builder_Table class
 *
 * @link https://wpbusinessreviews.com
 *
 * @package WP_Business_Reviews\Includes\Builder
 * @since 0.1.0
 */

namespace WP_Business_Reviews\Includes\Builder;

use WP_Business_Reviews\Includes\Deserializer\Collection_Deserializer as Deserializer;
use WP_Business_Reviews\Includes\Collection;
use WP_Business_Reviews\Includes\View;

/**
 * Displays existing collections in a tabular format.
 *
 * @since 0.1.0
 */
class Builder_Table {
	/**
	 * Collection deserializer.
	 *
	 * Fetches collections from the database if editing an existing collection.
	 *
	 * @since 0.1.0
	 * @var Deserializer $deserializer
	 */
	protected $deserializer;

	/**
	 * Collections.
	 *
	 * Fetches collections from the database if editing an existing collection.
	 *
	 * @since 0.1.0
	 * @var Collection[] $collections
	 */
	protected $collections;

	/**
	 * Instantiates the Builder_Table object.
	 *
	 * @since 0.1.0
	 *
	 * @param Deserializer $deserializer Collection deserializer.
	 */
	public function __construct( Deserializer $deserializer ) {
		$this->deserializer = $deserializer;
	}

	/**
	 * Initializes the object for use.
	 *
	 * @since 0.1.0
	 */
	public function init() {
		$this->collections = $this->deserializer->query_collections();
	}

	/**
	 * Retrieves the collections.
	 *
	 * @since 0.1.0
	 *
	 * @return Collection[] Array of collection objects.
	 */
	public function get_collections() {
		return $this->collections;
	}

	/**
	 * Renders the builder table.
	 *
	 * @since  0.1.0
	 */
	public function render() {
		$view_object = new View( WPBR_PLUGIN_DIR . 'views/builder/table.php' );

		$view_object->render(
			array(
				'collections' => $this->collections,
			)
		);
	}
}
