<?php
/**
 * Defines the Option_Serializer class
 *
 * @link https://wpbusinessreviews.com
 *
 * @package WP_Business_Reviews\Includes\Serializer
 * @since 0.1.0
 */

namespace WP_Business_Reviews\Includes\Serializer;

/**
 * Saves options to the database.
 *
 * @since 0.1.0
 */
class Option_Serializer extends Serializer_Abstract {
	/**
	 * Registers functionality with WordPress hooks.
	 *
	 * @since 0.1.0
	 */
	public function register() {
		add_action( 'admin_post_wpbr_settings_save', array( $this, 'save_from_post_array' ) );
	}

	/**
	 * Saves a single sanitized key-value pair.
	 *
	 * @since 0.1.0
	 *
	 * @param string $key   The key being saved.
	 * @param mixed  $value The value being saved.
	 * @return boolean True if value saved successfully, false otherwise.
	 */
	public function save( $key, $value ) {
		return update_option( $this->prefix . $key, $this->clean( $value ) );
	}

	/**
	 * Deletes an option.
	 *
	 * @since 0.1.0
	 *
	 * @param string $key   The key being saved.
	 *
	 * @return boolean True if value deleted successfully, false otherwise.
	 */
	public function delete( $key ) {
		return delete_option( $this->prefix . $key );
	}

	/**
	 * Saves an array of key-value pairs.
	 *
	 * @since 0.1.0
	 *
	 * @param array $values Key-value pairs to be saved.
	 */
	public function save_multiple( array $values ) {
		foreach ( $values as $key => $value ) {
			$this->save( $key, $value );
		}
	}

	/**
	 * Saves settings section to database.
	 *
	 * @since 0.1.0
	 */
	public function save_from_post_array() {
		check_admin_referer( "wpbr_option_save", "wpbr_option_nonce" );

		if ( ! current_user_can( 'manage_options') ) {
			wp_die( __( 'Sorry, you are not allowed to manage settings.' ), 'wp-business-reviews' );
		}

		if ( empty( $_POST['wpbr_subtab'] ) ) {
			$this->redirect_to_tab();
		}

		$section  = sanitize_text_field( $_POST['wpbr_subtab'] );

		if ( empty( $_POST['wpbr_option'] ) ) {
			if ( 'platforms' === $section ) {
				$this->redirect_to_tab( 'settings_platform_required' );
			}

			$this->redirect_to_tab();
		}

		$settings = $_POST['wpbr_option'];

		$this->save_multiple( $settings );

		/**
		 * Fires after all posted settings have been saved.
		 *
		 * @since 0.1.0
		 *
		 * @param string $section Name of the updated setting.
		 */
		do_action( 'wpbr_after_settings_save', $section );

		$this->redirect_to_tab( 'settings_saved' );
	}

	/**
	 * Redirects to the settings tab from which settings were saved.
	 *
	 * @since 0.1.0
	 *
	 * @param string $notice_code Optional. ID of the admin notice to be displayed.
	 */
	public function redirect_to_tab( $notice_code = '' ) {
		if (
			empty( $_POST['wpbr_tab'] )
			|| empty( $_POST['wpbr_subtab'] )
		) {
			wp_safe_redirect( wp_get_referer() );
			exit;
		}

		$tab    = sanitize_text_field( wp_unslash( $_POST['wpbr_tab'] ) );
		$subtab = sanitize_text_field( wp_unslash( $_POST['wpbr_subtab'] ) );

		$query_args = array(
			'wpbr_tab'    => $tab,
			'wpbr_subtab' => $subtab,
		);

		if ( ! empty( $notice_code ) ) {
			$query_args['wpbr_notice'] = $notice_code;
		}

		wp_safe_redirect( add_query_arg( $query_args, wp_get_referer() ) );
		exit;
	}
}
