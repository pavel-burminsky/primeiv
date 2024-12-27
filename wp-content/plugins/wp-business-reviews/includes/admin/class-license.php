<?php

/**
 * Defines the licensing class
 *
 * @package WP_Business_Reviews\Includes
 * @since   0.1.0
 */

namespace WP_Business_Reviews\Includes\Admin;

use WP_Business_Reviews\Includes\Deserializer\Option_Deserializer;
use WP_Business_Reviews\Includes\Serializer\Option_Serializer;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Defines the licensing functionality.
 *
 *
 * @since 0.1.0
 */
class License {

	/**
	 * @var array
	 */
	private $plugin_info = [];

	/**
	 * @var Option_Deserializer
	 */
	private $option_deserializer;

	/**
	 * Hooks functionality responsible for building the admin menu.
	 *
	 * @since 0.1.0
	 */
	function register() {

		define( 'WPBR_STORE_URL', 'https://wpbusinessreviews.com' );
		define( 'WPBR_ITEM_ID', 36 );

		add_action( 'admin_init', array( $this, 'plugin_updater' ), 0 );
		add_action( 'admin_init', array( $this, 'activate_license' ) );
		add_action( 'admin_init', array( $this, 'deactivate_license' ) );
		add_filter( 'wpbr_config_settings', array( $this, 'license_field' ), 10, 1 );

	}

	/**
	 * Plugin updater.
	 */
	public function plugin_updater() {

		// retrieve our license key from the DB.
		$this->option_deserializer = new Option_Deserializer();
		$license_key               = $this->option_deserializer->get( 'license_key' );
		$this->plugin_info         = get_plugin_data( WPBR_PLUGIN_FILE );

		// Setup the updater.
		new Plugin_Updater( WPBR_STORE_URL, WPBR_PLUGIN_FILE,
			array(
				'version' => $this->plugin_info['Version'],  // current version number
				'license' => $license_key,   // license key (used get_option above to retrieve from DB)
				'item_id' => WPBR_ITEM_ID,  // ID of the product
				'author'  => 'Impress.org', // author of this plugin
				'beta'    => false,
			)
		);

	}

	/**
	 * Display the license tab withing settings.
	 *
	 * @param $config array
	 *
	 * @return array
	 */
	public function license_field( $config ) {

		$config['license'] = array(
			'name'     => __( 'License', 'wp-business-reviews' ),
			'sections' => [
				'license' => [
					'name'        => __( 'License', 'wp-business-reviews' ),
					'heading'     => __( 'License Settings', 'wp-business-reviews' ),
					'fields'      => [
						'license_key' => [
							'name'          => __( 'License Key', 'wp-business-reviews' ),
							'type'          => 'license',
							'description'  => sprintf(
								/* translators: 1: customer account link, 2: closing anchor tag */
								__( 'Enter the license key found within your %1$sWP Business Reviews account%2$s, where you can also manage the active sites connected to your license. Need help? Reach out to our support team via live chat or our contact form.', 'wp-business-reviews' ),
								'<a href="https://wpbusinessreviews.com/account/licenses/" target="_blank" rel="noopener noreferrer">',
								'</a>'
							),
							'wrapper_class' => 'wpbr-field--spacious',
						],
					],
				],
			],
		);

		return $config;

	}

	/**
	 * Activate the License
	 */
	public function activate_license() {

		// listen for our activate button to be clicked & not deactivating
		if (
			isset( $_POST['wpbr_option']['license_key'] )
			&& ! isset( $_POST['edd_license_deactivate'] )
		) {

			$license_key = sanitize_text_field( $_POST['wpbr_option']['license_key'] );

			// data to send in our API request
			$api_params = array(
				'edd_action' => 'activate_license',
				'license'    => $license_key,
				'item_name'  => urlencode( $this->plugin_info['Name'] ),
				'url'        => home_url()
			);

			// Call the custom API.
			$response = wp_remote_post( WPBR_STORE_URL, array(
				'timeout'   => 15,
				'sslverify' => true,
				'body'      => $api_params
			) );

			// make sure the response came back okay
			if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {

				if ( is_wp_error( $response ) ) {
					$message = $response->get_error_message();
				} else {
					$message = __( 'An error occurred, please try again.', 'wp-business-reviews' );
				}

			} else {

				$license_data = json_decode( wp_remote_retrieve_body( $response ) );

				if ( false === $license_data->success ) {

					switch ( $license_data->error ) {

						case 'expired' :

							$message = sprintf(
								__( 'Your license key expired on %s.', 'wp-business-reviews' ),
								date_i18n( get_option( 'date_format' ), strtotime( $license_data->expires, current_time( 'timestamp' ) ) )
							);
							break;

						case 'disabled' :
						case 'revoked' :

							$message = __( 'Your license key has been disabled.', 'wp-business-reviews' );
							break;

						case 'missing' :

							$message = __( 'The license key entered is not a valid license.', 'wp-business-reviews' );
							break;

						case 'invalid' :
						case 'site_inactive' :

							$message = __( 'Your license is not active for this URL.', 'wp-business-reviews' );
							break;

						case 'item_name_mismatch' :

							$message = sprintf( __( 'This appears to be an invalid license key for %s.', 'wp-business-reviews' ), $this->plugin_info['Name'] );
							break;

						case 'no_activations_left':

							$message = __( 'Your license key has reached its activation limit.', 'wp-business-reviews' );
							break;

						default :

							$message = __( 'An error occurred, please try again.', 'wp-business-reviews' );
							break;
					}

				}

			}

			$redirect = admin_url( 'admin.php?page=wpbr-settings&wpbr_tab=license' );

			// Check if anything passed on a message constituting a failure
			if ( ! empty( $message ) ) {
				$redirect = add_query_arg( array(
					'sl_activation'   => 'false',
					'license_message' => urlencode( $message )
				), $redirect );

				wp_redirect( $redirect );

				exit;
			}

			// $license_data->license will be either "valid" or "invalid"
			$option_serializer = new Option_Serializer();
			$option_serializer->save( 'license_key', $license_key );
			$option_serializer->save( 'license_status', $license_data->license );

			wp_redirect( $redirect );
			exit;

		}
	}


	/**
	 * Deactivate the license.
	 */
	public function deactivate_license() {
		$message = '';

		// listen for our deactivate button to be clicked AND deactivating requested.
		if (
			isset( $_POST['wpbr_option']['license_key'] )
			&& ( isset( $_POST['edd_license_deactivate'] )
			&& 'deactivate_license' === $_POST['edd_license_deactivate'] )
		) {
			// retrieve the license from the database.
			$license = $this->option_deserializer->get( 'license_key' );

			// data to send in our API request.
			$api_params = array(
				'edd_action' => 'deactivate_license',
				'license'    => $license,
				'item_name'  => urlencode( $this->plugin_info['Name'] ),
				'url'        => home_url()
			);

			// Call the custom API.
			$response = wp_remote_post( WPBR_STORE_URL, array(
				'timeout'   => 15,
				'sslverify' => false,
				'body'      => $api_params
			) );

			$redirect = admin_url( 'admin.php?page=wpbr-settings&wpbr_tab=license' );

			if ( is_wp_error( $response ) ) {
				// An error occurred while connecting to the server.
				$message = $response->get_error_message();
			} elseif ( 200 !== wp_remote_retrieve_response_code( $response ) ) {
				// The server did not respond as expected.
				$message = 'failed';
			} else {
				// A connection to the server was made.
				$license_data = json_decode( wp_remote_retrieve_body( $response ) );

				// $license_data->license will be either "deactivated" or "failed".
				if ( 'deactivated' !== $license_data->license ) {
					$message = 'failed';
				}
			}

			// Delete license key regardless regardless of the response.
			$option_serializer = new Option_Serializer();
			$option_serializer->delete( 'license_key' );
			$option_serializer->delete( 'license_status' );

			// Add messaging if available.
			if ( ! empty( $message ) ) {
				$redirect = add_query_arg( array(
					'sl_activation'   => 'false',
					'license_message' => urlencode( $message ),
				), $redirect );
			}

			wp_redirect( $redirect );
			exit();
		}
	}

}
