<?php
/**
 * Defines the Platform_Selector class
 *
 * @link https://wpbusinessreviews.com
 *
 * @package WP_Business_Reviews\Includes
 * @since 0.1.0
 */

namespace WP_Business_Reviews\Includes;

use WP_Business_Reviews\Includes\View;

/**
 * Presents an interface for platform selection.
 *
 * @since 0.1.0
 */
class Platform_Selector {
	/**
	* Platform manager.
	*
	* @since 0.1.0
	* @var Platform_Manager $platform_manager
	*/
	protected $platform_manager;

	/**
	 * Instantiates the Platform_Selector object.
	 *
	 * @since 0.1.0
	 *
	 * @param Platform_Manager $platform_manager Manager of platform statuses.
	 * @param Builder_Table    $table            Table of existing collections.
	 */
	public function __construct( Platform_Manager $platform_manager ) {
		$this->platform_manager = $platform_manager;
	}

	/**
	 * Registers functionality with WordPress hooks.
	 *
	 * @since 0.1.0
	 */
	public function register() {
		add_filter( 'views_edit-wpbr_collection', array( $this, 'prepend_to_list_table' ), 20 );
	}

	/**
	 * Prepends the platform selector to the list table.
	 *
	 * @param array $views An array of available list table views.
	 * @return array An array of available list table views.
	 */
	public function prepend_to_list_table( $views ) {
		$this->render();
		return $views;
	}

	/**
	 * Renders the builder UI.
	 *
	 * @since  0.1.0
	 */
	public function render() {
		$view_object         = new View( WPBR_PLUGIN_DIR . 'views/admin/platform-selector.php' );
		$active_platforms    = $this->platform_manager->get_active_platforms();
		$connected_platforms = $this->platform_manager->get_connected_platforms();

		// Treat tagged collections as a platform for selection.
		$active_platforms['review_tag'] = __( 'Tagged', 'wp-business-reviews' );

		$view_object->render(
			array(
				'active_platforms'    => $active_platforms,
				'connected_platforms' => $connected_platforms,
			)
		);
	}
}
