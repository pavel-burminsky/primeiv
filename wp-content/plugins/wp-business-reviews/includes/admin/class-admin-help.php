<?php
/**
 * Defines the Admin_Help class
 *
 * @package WP_Business_Reviews\Includes\Admin
 * @since   0.1.0
 */

namespace WP_Business_Reviews\Includes\Admin;

use WP_Business_Reviews\Includes\Config;
use WP_Business_Reviews\Includes\View;

/**
 * Manages admin help messages.
 *
 * @since 0.1.0
 */
class Admin_Help {
	/**
	 * Admin help config.
	 *
	 * @since 0.1.0
	 * @var Config $config
	 */
	private $config;

	/**
	 * Admin help messages relevant to the current context.
	 *
	 * Although `$config` contains all help messages in the plugin, `$messages`
	 * only contains the messages relevant to the current context.
	 *
	 * @since 0.1.0
	 * @var array $messages
	 */
	private $messages;

	/**
	 * Instantiates the Admin_Help object.
	 *
	 * @since 0.1.0
	 *
	 * @param Config $config Admin help config.
	 */
	public function __construct( Config $config ) {
		$this->config = $config;
	}

	/**
	 * Registers functionality with WordPress hooks.
	 *
	 * @since 0.1.0
	 */
	public function register() {
		add_action( 'wpbr_admin_page_wpbr-builder', function () {
			$this->messages = $this->config;
			$this->print_js_object( 'wpbr-admin-main-script' );
		} );
	}

	/**
	 * Gets contextual help messages.
	 *
	 * @since 0.1.0
	 *
	 * @return array $messages Array of help messages.
	 */
	public function get_messages() {
		return $this->messages;
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
			'wpbrHelpStrings',
			array(
				'messages' => $this->get_messages(),
			)
		);
	}
}
