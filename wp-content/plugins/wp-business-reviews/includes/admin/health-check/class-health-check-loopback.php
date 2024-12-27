<?php
/**
 * Defines the Health_Check_Loopback class
 *
 * @link https://github.com/WordPress/health-check
 *
 * @package WP_Business_Reviews\Includes\Admin\Health_Check
 * @since   1.2.0
 */

namespace WP_Business_Reviews\Includes\Admin\Health_Check;

/**
 * Class Health_Check_Loopback
 */
class Health_Check_Loopback {
	/**
	 * Run a loopback test on our site.
	 *
	 * @uses wp_unslash()
	 * @uses base64_encode()
	 * @uses admin_url()
	 * @uses add_query_arg()
	 * @uses is_array()
	 * @uses implode()
	 * @uses wp_remote_get()
	 * @uses compact()
	 * @uses is_wp_error()
	 * @uses wp_remote_retrieve_response_code()
	 * @uses sprintf()
	 *
	 * @param null|string       $disable_plugin_hash Optional. A hash to send with our request to disable any plugins.
	 * @param null|string|array $allowed_plugins     Optional. A string or array of approved plugin slugs that can run even when we globally ignore plugins.
	 *
	 * @return object
	 */
	static function can_perform_loopback( $disable_plugin_hash = null, $allowed_plugins = null ) {
		$cookies = wp_unslash( $_COOKIE );
		$timeout = 10;
		$headers = array(
			'Cache-Control' => 'no-cache',
		);

		// Include Basic auth in loopback requests.
		if ( isset( $_SERVER['PHP_AUTH_USER'] ) && isset( $_SERVER['PHP_AUTH_PW'] ) ) {
			$headers['Authorization'] = 'Basic ' . base64_encode( wp_unslash( $_SERVER['PHP_AUTH_USER'] ) . ':' . wp_unslash( $_SERVER['PHP_AUTH_PW'] ) );
		}

		$url = admin_url();

		if ( ! empty( $disable_plugin_hash ) ) {
			$url = add_query_arg( array(
				'health-check-disable-plugin-hash' => $disable_plugin_hash,
			), $url );
		}
		if ( ! empty( $allowed_plugins ) ) {
			if ( ! is_array( $allowed_plugins ) ) {
				$allowed_plugins = (array) $allowed_plugins;
			}

			$url = add_query_arg(
				array(
					'health-check-allowed-plugins' => implode( ',', $allowed_plugins ),
				),
				$url
			);
		}

		$r = wp_remote_get( $url, compact( 'cookies', 'headers', 'timeout' ) );

		if ( is_wp_error( $r ) ) {
			return (object) array(
				'status'  => 'error',
				'message' => sprintf(
					'%s %s',
					esc_html__( 'The loopback request to your site failed, this may prevent WP_Cron from working, along with theme and plugin editors.', 'wp-business-reviews' ),
					sprintf(
						/* translators: %1$d: The HTTP response code. %2$s: The error message returned. */
						esc_html__( 'Error encountered: (%1$d) %2$s', 'wp-business-reviews' ),
						wp_remote_retrieve_response_code( $r ),
						$r->get_error_message()
					)
				),
			);
		}

		if ( 200 !== wp_remote_retrieve_response_code( $r ) ) {
			return (object) array(
				'status'  => 'warning',
				'message' => sprintf(
					/* translators: %d: The HTTP response code returned. */
					esc_html__( 'The loopback request returned an unexpected status code, %d, this may affect tools such as WP_Cron, or theme and plugin editors.', 'wp-business-reviews' ),
					wp_remote_retrieve_response_code( $r )
				),
			);
		}

		return (object) array(
			'status'  => 'good',
			'message' => __( 'The loopback request to your site completed successfully.', 'wp-business-reviews' ),
		);
	}
}
