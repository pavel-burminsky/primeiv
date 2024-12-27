<?php
/**
 * Defines the Builder class
 *
 * @package WP_Business_Reviews\Includes\Builder
 * @since   0.1.0
 */

namespace WP_Business_Reviews\Includes\Builder;

use WP_Business_Reviews\Includes\Builder\Builder_Inspector;
use WP_Business_Reviews\Includes\Builder\Builder_Preview;
use WP_Business_Reviews\Includes\Deserializer\Collection_Deserializer as Deserializer;
use WP_Business_Reviews\Includes\View;

/**
 * Builds collections of reviews or review sources.
 *
 * @since 0.1.0
 */
class Builder {
	/**
	 * Builder inspector.
	 *
	 * Displays the builder controls in a sidebar.
	 *
	 * @since 0.1.0
	 * @var Builder_Inspector
	 */
	protected $inspector;

	/**
	 * Builder preview.
	 *
	 * Displays a preview of the collection being built.
	 *
	 * @since 0.1.0
	 * @var Builder_Preview
	 */
	protected $preview;

	/**
	 * Collection deserializer.
	 *
	 * Fetches collections from the database if editing an existing collection.
	 *
	 * @since 0.1.0
	 * @var Deserializer
	 */
	protected $deserializer;

	/**
	 * Collection of reviews data and presentation settings.
	 *
	 * @since 0.1.0
	 * @var Collection
	 */
	protected $collection;

	/**
	 * Instantiates the Settings_Abstract object.
	 *
	 * @since 0.1.0
	 *
	 * @param Builder_Inspector $inspector    Builder inspector.
	 * @param Builder_Preview   $preview      Builder preview.
	 * @param Deserializer      $deserializer Collection deserializer.
	 */
	public function __construct(
		Builder_Inspector $inspector,
		Builder_Preview $preview,
		Deserializer $deserializer
	) {
		$this->inspector    = $inspector;
		$this->preview      = $preview;
		$this->deserializer = $deserializer;
	}

	/**
	 * Registers functionality with WordPress hooks.
	 *
	 * @since 0.1.0
	 */
	public function register() {
		add_action( 'wpbr_admin_page_wpbr-builder', array( $this, 'init' ) );
	}

	/**
	 * Initializes the object for use.
	 *
	 * There are three possible scenarios when the builder is loaded:
	 *
	 * 1. Neither a collection ID or platform is defined, so display launcher.
	 * 2. An existing collection ID is defined in the URL, so load it.
	 * 3. A platform is defined but not a collection ID, so display empty builder
	 *    that is configured for that platform.
	 *
	 * @since 0.1.0
	 */
	public function init() {
		// Bail collection ID and platform are not available.
		if (
			! isset( $_GET['wpbr_collection_id'] )
			&& ! isset( $_GET['wpbr_platform'] )
		) {
			return;
		}

		// Initialize existing collection based on collection ID.
		if ( isset( $_GET['wpbr_collection_id'] ) ) {
			$collection_id    = sanitize_text_field( $_GET['wpbr_collection_id'] );
			$this->collection = $this->deserializer->get_collection( $collection_id );

			if ( ! $this->collection ) {
				$this->launcher->init();
				$this->launcher->render();
				return;
			}

			// Hydrate collection.
			$this->collection = $this->deserializer->hydrate_review_sources( $this->collection );
			$this->collection = $this->deserializer->hydrate_reviews( $this->collection );

			// Get platform from collection.
			$platform = $this->collection->get_platform();
			$this->inspector->set_platform( $platform );

			// Make collection available to inspector and preview.
			$this->inspector->set_collection( $this->collection );
			$this->preview->set_collection( $this->collection );

			// Print collection as JS object for front-end consumption.
			$this->collection->print_js_object( 'wpbr-admin-main-script' );
		} elseif ( isset( $_GET['wpbr_platform'] ) ) {
			// Initialize empty collection based on platform.
			$platform = sanitize_text_field( wp_unslash( $_GET['wpbr_platform'] ) );
			$this->inspector->set_platform( $platform );
		}

		// Now that the inspector and preview are prepped, go ahead and render.
		$this->inspector->init();
		$this->render();
	}

	/**
	 * Retrieves a link to trash the current collection.
	 *
	 * @since 0.1.0
	 */
	protected function get_trash_link() {
		if ( empty( $this->collection ) ) {
			return '';
		}

		$trash_link = add_query_arg( array(
			'action' => 'wpbr_collection_trash',
			'post'   => $this->collection->get_post_id(),
		), admin_url( 'admin-post.php' ) );

		return wp_nonce_url( $trash_link, 'wpbr_collection_trash', 'wpbr_collection_nonce' );
	}

	/**
	 * Renders the builder UI.
	 *
	 * @since  0.1.0
	 */
	public function render() {
		$view_object = new View( WPBR_PLUGIN_DIR . 'views/builder/builder.php' );
		$title = $shortcode = '';

		if ( ! empty( $this->collection ) ) {
			$title     = $this->collection->get_title();
			$shortcode = sprintf( '[wpbr_collection id="%s"]', $this->collection->get_post_id() );
		}

		$view_object->render(
			array(
				'inspector'         => $this->inspector,
				'preview'           => $this->preview,
				'title'             => $title,
				'title_default'     => __( 'Untitled Collection', 'wp-business-reviews' ),
				'title_placeholder' => __( 'Enter a collection title', 'wp-business-reviews' ),
				'trash_link'        => $this->get_trash_link(),
				'shortcode'         => $shortcode,
			)
		);
	}
}
