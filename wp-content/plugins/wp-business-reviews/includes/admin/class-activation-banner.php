<?php
/**
 * Defines the Activation_Banner class
 *
 * @package WP_Business_Reviews\Includes\Admin
 * @since   0.1.0
 */

namespace WP_Business_Reviews\Includes\Admin;

use WP_Business_Reviews\Includes\View;

/**
 * Creates the activation banner for the plugin.
 *
 * @since 0.1.0
 */
class Activation_Banner {
	/**
	 * The unique ID of the current user.
	 *
	 * @since 0.1.0
	 * @var int
	 */
	protected $user_id = 0;

	/**
	 * User meta key that stores whether banner is enabled for user.
	 *
	 * @since 0.1.0
	 * @var string
	 */
	protected $user_meta_key = 'wpbr_activation_banner';

	/**
	 * Registers functionality with WordPress hooks.
	 *
	 * @since 0.1.0
	 */
	public function register() {
		add_action( 'pre_current_active_plugins', array( $this, 'init' ) );
		add_action( 'pre_current_active_plugins', array( $this, 'print_nonce' ) );
		add_action( 'pre_current_active_plugins', array( $this, 'maybe_render' ) );
		add_action( 'wp_ajax_wpbr_disable_activation_banner', array( $this, 'init' ) );
		add_action( 'wp_ajax_wpbr_disable_activation_banner', array( $this, 'ajax_disable' ) );
	}

	/**
	 * Initializes the object for use.
	 *
	 * @since 0.1.0
	 */
	public function init() {
		$this->user_id = get_current_user_id();
	}

	/**
	 * Renders the activation banner if enabled for the current user.
	 *
	 * @since 0.1.0
	 */
	public function maybe_render() {
		if ( $this->is_enabled() ) {
			$this->render();
		}
	}

	/**
	 * Renders the activation banner.
	 *
	 * @since  0.1.0
	 */
	public function render() {
		$view_object = new View( WPBR_PLUGIN_DIR . 'views/admin/activation-banner.php' );
		$view_object->render();
	}

	/**
	 * Enables the activation banner for a user.
	 *
	 * @since  0.1.0
	 */
	public function enable() {
		update_user_meta( $this->user_id, $this->user_meta_key, true );
	}

	/**
	 * Disables the activation banner for a user.
	 *
	 * @since  0.1.0
	 */
	public function disable() {
		update_user_meta( $this->user_id, $this->user_meta_key, false );
	}

	/**
	 * Disables the activation banner via AJAX.
	 *
	 * @since  0.1.0
	 */
	public function ajax_disable() {
		check_ajax_referer( 'wpbr_disable_activation_banner', 'nonce' );
		$this->disable();
		wp_die();
	}

	/**
	 * Prints a nonce for use when disabling the banner via AJAX.
	 *
	 * @since 0.1.0
	 */
	public function print_nonce() {
		wp_localize_script( 'wpbr-admin-main-script', 'wpbr_activation_banner_nonce', [
			'nonce' => wp_create_nonce( 'wpbr_disable_activation_banner' )
		] );
	}

	/**
	 * Determines whether the activation banner is enabled for a user.
	 *
	 * @since  0.1.0
	 */
	protected function is_enabled() {
		$meta_value = get_user_meta( $this->user_id, $this->user_meta_key );

		// Return false only if meta_key exists and is set to a falsy value.
		if ( ! empty( $meta_value ) && ! $meta_value[0] ) {
			return false;
		}

		return true;
	}
}
