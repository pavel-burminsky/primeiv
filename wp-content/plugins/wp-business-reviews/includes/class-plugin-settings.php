<?php
/**
 * Defines the Plugin_Settings class
 *
 * @package WP_Business_Reviews\Includes
 * @since   0.1.0
 */

namespace WP_Business_Reviews\Includes;

use WP_Business_Reviews\Includes\Config;
use WP_Business_Reviews\Includes\Field\Parser\Plugin_Settings_Field_Parser as Field_Parser;
use WP_Business_Reviews\Includes\Platform_Manager;
use WP_Business_Reviews\Includes\Field\Field_Repository;
use WP_Business_Reviews\Includes\View;

/**
 * Retrieves and displays the plugin's settings.
 *
 * @since 0.1.0
 */
class Plugin_Settings {
	/**
	 * Settings config.
	 *
	 * @since 0.1.0
	 * @var Config
	 */
	protected $config;

	/**
	 * Parser of field objects from config.
	 *
	 * @since 0.1.0
	 * @var Field_Parser
	 */
	protected $field_parser;

	/**
	* Platform manager.
	*
	* @since 0.1.0
	* @var Platform_Manager $platform_manager
	*/
	protected $platform_manager;

	/**
	 * Repository that holds field objects.
	 *
	 * @since 0.1.0
	 * @var Field_Repository
	 */
	protected $field_repository;

	/**
	 * Instantiates the Plugin Settings object.
	 *
	 * @since 0.1.0
	 *
	 * @param Config       $config              Plugin settings config.
	 * @param Field_Parser $field_parser        Parser of field objects from config.
	 */
	public function __construct(
		Config $config,
		Field_Parser $field_parser,
		Platform_Manager $platform_manager
	) {
		$this->config           = $config;
		$this->field_parser     = $field_parser;
		$this->platform_manager = $platform_manager;
	}

	/**
	 * Registers functionality with WordPress hooks.
	 *
	 * @since 0.1.0
	 */
	public function register() {
		add_action( 'wpbr_admin_page_wpbr-settings', array( $this, 'init' ) );
		add_action( 'wpbr_admin_page_wpbr-settings', array( $this, 'render' ) );
	}

	/**
	 * Initializes the object for use.
	 *
	 * @since 0.1.0
	 */
	public function init() {
		$this->field_repository = new Field_Repository(
			$this->field_parser->parse_fields( $this->config )
		);
	}

	/**
	 * Renders the plugin settings UI.
	 *
	 * @since  0.1.0
	 */
	public function render() {
		$view_object = new View( WPBR_PLUGIN_DIR . 'views/plugin-settings/main.php' );
		$active_platforms    = $this->platform_manager->get_active_platforms();
		$connected_platforms = $this->platform_manager->get_connected_platforms();

		$view_object->render(
			array(
				'config'              => $this->config,
				'field_repository'    => $this->field_repository,
				'active_platforms'    => $active_platforms,
				'connected_platforms' => $connected_platforms,
			)
		);
	}
}
