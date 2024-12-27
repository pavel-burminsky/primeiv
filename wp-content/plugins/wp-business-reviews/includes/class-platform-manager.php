<?php
/**
 * Defines the Platform_Manager class
 *
 * @link https://wpbusinessreviews.com
 *
 * @package WP_Business_Reviews\Includes
 * @since 0.1.0
 */

namespace WP_Business_Reviews\Includes;

use WP_Business_Reviews\Includes\Admin\Admin_Notices;
use WP_Business_Reviews\Includes\Serializer\Option_Serializer;
use WP_Business_Reviews\Includes\Request\Request_Factory;
use WP_Business_Reviews\Includes\Deserializer\Option_Deserializer;

/**
 * Manages the existing, active, and connected platforms.
 *
 * @since 0.1.0
 */
class Platform_Manager {
	/**
	 * Settings saver.
	 *
	 * @since 0.1.0
	 * @var Option_Serializer $serializer
	 */
	protected $serializer;

	/**
	 * Settings retriever.
	 *
	 * @since 0.1.0
	 * @var Option_Deserializer $deserializer
	 */
	protected $deserializer;

	/**
	 * Request factory.
	 *
	 * @since 0.1.0
	 * @var Request_Factory $request_factory
	 */
	protected $request_factory;

	/**
	 * Active platforms.
	 *
	 * @since 0.1.0
	 * @var array $active_platforms
	 */
	private $active_platforms;

	/**
	 * Connected platforms.
	 *
	 * @since 0.1.0
	 * @var array $connected_platforms
	 */
	private $connected_platforms;

	/**
	 * Platforms that have experienced a failed connection.
	 *
	 * @since 1.3.0
	 * @var array $failed_platforms
	 */
	private $failed_platforms;

	/**
	 * Instantiates the Platform_Manager object.
	 *
	 * @param Option_Deserializer $deserializer Settings retriever.
	 * @param Option_Serializer $serializer Settings saver.
	 * @param Request_Factory $request_factory Request factory.
	 */
	public function __construct(
		Option_Deserializer $deserializer,
		Option_Serializer $serializer,
		Request_Factory $request_factory
	) {
		$this->deserializer    = $deserializer;
		$this->serializer      = $serializer;
		$this->request_factory = $request_factory;
	}

	/**
	 * Registers functionality with WordPress hooks.
	 *
	 * @since 0.1.0
	 */
	public function register() {
		// Most platforms have their status saved after settings are saved.
		add_action( 'wpbr_after_settings_save', array( $this, 'save_platform_status' ) );

		// Facebook is a special case because it needs to save status when the token is saved, after redirect.
		add_action( 'wpbr_after_facebook_user_token_save', array( $this, 'save_facebook_platform_status' ) );

		// Save new status after a platform status change.
		add_action( 'wpbr_platform_status_update', array( $this, 'save_platform_status' ), 10, 2 );

		// Filter platforms for plugin settings.
		add_filter( 'wpbr_settings_platforms', array( $this, 'get_platforms' ) );
		add_filter( 'wpbr_settings_default_platforms', array( $this, 'get_default_platforms' ) );

        // Facebook connect notification
        if ( ! get_option( 'wpbr_facebook_connect_notice' ) ) {
            if ( // Fresh install?
                count( $this->get_connected_platforms() ) === 0
                && count( $this->get_failed_platforms() ) === 0
            ) {
                update_option( 'wpbr_facebook_connect_notice', true );
            } else if (
                array_key_exists( 'facebook', $this->get_failed_platforms() )
                && array_key_exists( 'facebook', $this->get_active_platforms() )
            ) {
                Admin_Notices::add_notice( 'facebook_connect' );
            }
        }
	}

	/**
	 * Gets all registered platforms.
	 *
	 * @since 0.2.0 Unset `review_tag` and `custom` for plugin settings.
	 * @since 0.1.0
	 *
	 * @return array Associative array of platforms.
	 */
	public static function get_platforms() {
		$platforms = array(
			'google_places' => __( 'Google', 'wp-business-reviews' ),
			'facebook'      => __( 'Facebook', 'wp-business-reviews' ),
			'trust_pilot'   => __( 'Trustpilot', 'wp-business-reviews' ),
			'woocommerce'   => __( 'WooCommerce', 'wp-business-reviews' ),
			'yelp'          => __( 'Yelp', 'wp-business-reviews' ),
			'yp'            => __( 'YP', 'wp-business-reviews' ),
			'zomato'        => __( 'Zomato', 'wp-business-reviews' ),
			'review_tag'    => __( 'Tagged', 'wp-business-reviews' ),
			'custom'        => __( 'Custom', 'wp-business-reviews' ),
		);

		// Prevent 'Tagged' or 'Custom' from appearing in Active Platform setting.
		if ( 'wpbr_settings_platforms' === current_filter() ) {
			unset( $platforms['review_tag'] );
			unset( $platforms['custom'] );
		}

		/**
		 * Filters the array of registered platforms.
		 *
		 * @since 0.1.0
		 */
		return apply_filters( 'wpbr_platforms', $platforms );
	}

	/**
	 * Transforms a platform ID to the "pretty" version of the platform name.
	 *
	 * @since 0.1.0
	 *
	 * @param string $platform_id Platform ID (e.g. "google_places").
	 *
	 * @return string Pretty platform name (e.g. "Google Places").
	 */
	public static function get_pretty_platform( $platform_id ) {
		$platforms       = self::get_platforms();
		$pretty_platform = '';

		if ( isset( $platforms[ $platform_id ] ) ) {
			$pretty_platform = $platforms[ $platform_id ];
		}

		/**
		 * Filters the pretty version of a platform name.
		 *
		 * @since 0.1.0
		 *
		 * @param string $pretty_platform Pretty platform name (e.g. "Google Places").
		 * @param string $platform_id     Platform ID (e.g. "google_places").
		 */
		return apply_filters( 'wpbr_pretty_platform', $pretty_platform, $platform_id );
	}

	/**
	 * Gets the default platforms.
	 *
	 * @since 0.1.0
	 *
	 * @return array Associative array of default platforms.
	 */
	public static function get_default_platforms() {
		$default_platform_ids = array(
			'google_places',
			'facebook',
			'trust_pilot',
			'woocommerce',
			'yelp',
			'zomato',
		);

		$default_platforms = array_intersect_key( self::get_platforms(), array_flip( $default_platform_ids ) );

		/**
		 * Filters the array of default platforms.
		 *
		 * @since 0.1.0
		 *
		 * @param array Associative array of default platforms.
		 */
		return apply_filters( 'wpbr_default_platforms', $default_platforms );
	}

	/**
	 * Gets the active platforms.
	 *
	 * @since 0.2.0 Ensure 'Tagged' collections are always enabled.
	 * @since 0.1.0
	 *
	 * @return array Associative array of active platforms.
	 */
	public function get_active_platforms() {
		$platforms        = self::get_platforms();
		$active_platforms = $this->deserializer->get( 'active_platforms', array() );

		return array_intersect_key( $platforms, $active_platforms );
	}

	/**
	 * Gets the currently connected platforms.
	 *
	 * If connected platforms have not been set, they will be retrieved from the
	 * database. Each platform has its own status key in the database, so this
	 * method will retrieve the options and combine statuses into one array.
	 *
	 * @since 0.1.0
	 *
	 * @return array Array of connected platform slugs.
	 */
	public function get_connected_platforms() {
		$platforms           = self::get_platforms();
		$platform_ids        = array_keys( $platforms );
		$connected_platforms = array();

		if ( isset( $this->connected_platforms ) ) {
			$connected_platforms = $this->connected_platforms;
		} else {
			foreach ( $platform_ids as $platform_id ) {
				$platform_status = $this->deserializer->get(
					"{$platform_id}_platform_status",
					array()
				);

				if (
					isset( $platform_status['status'] )
					&& 'connected' === $platform_status['status']
				) {
					$connected_platforms[] = $platform_id;
				}
			}
		}

		return array_intersect_key( $platforms, array_flip( $connected_platforms ) );
	}

	/**
	 * Gets failed platforms.
	 *
	 * @return array Associative array of failed platforms.
	 */
	public function get_failed_platforms() {
		$platforms        = self::get_platforms();
		$active_platforms = $this->deserializer->get( 'failed_platforms' ) ?: array();

		return array_intersect_key( $platforms, array_flip( $active_platforms ) );
	}

	/**
	 * Saves the connection status of a platform.
	 *
	 * Since the daily quota of a free Google account is `1`, the platform
	 * status gets checked twice in order to alert the user that billing is
	 * not enabled as soon as the API key is saved.
	 *
	 * @since 1.0.1 Add `$status` parameter to explicitly set status. Double-check
	 *              status of Google Places API due to new billing requirement.
	 * @since 0.1.0
	 *
	 * @param string $platform Platform ID.
	 * @param string $status   Platform status code.
	 * @return boolean True if status saved, false otherwise.
	 */
	public function save_platform_status( $platform, $status = '' ) {
		$platforms = self::get_platforms();

		// Return early if platform is not available.
		if ( ! isset( $platforms[ $platform ] ) ) {
			return false;
		}

		// If a status is not provided, fire test request(s) to determine it.
		if ( empty( $status ) ) {
			$status = $this->get_platform_status( $platform );

			if ( 'google_places' === $platform ) {
				$status = $this->get_platform_status( $platform );
			}
		}

		return $this->serializer->save(
			$platform . '_platform_status',
			array(
				'status'       => $status,
				'last_checked' => time(),
			)
		);
	}

	/**
	 * Saves the connection status of the Facebook platform.
	 *
	 * Since Facebook saves a token following a redirect, providing the
	 * platform with its own method allows the status to be checked immediately
	 * after the token is saved.
	 *
	 * @since 0.1.0
	 */
	public function save_facebook_platform_status() {
		$this->save_platform_status( 'facebook' );
	}

	/**
	 * Determines if platform has been marked active by the user.
	 *
	 * @since 0.1.0
	 *
	 * @param string $platform The platform slug.
	 *
	 * @return bool
	 */
	private function is_active( $platform ) {
		return in_array( $platform, array_keys( $this->get_active_platforms() ) );
	}

	/**
	 * Determines if a platform is connected.
	 *
	 * @since 1.0.1 Leverage get_platform_status() method to determine status.
	 * @since 0.1.0
	 *
	 * @param string $platform The platform slug.
	 *
	 * @return bool True if connected, false otherwise.
	 */
	private function is_connected( $platform ) {
		$status = $this->get_platform_status( $platform );

		return 'connected' === $status;
	}

	/**
	 * Retrieves the platform status based on a test request.
	 *
	 * @since 1.0.1
	 *
	 * @param string $platform The platform slug.
	 *
	 * @return string The platform status. Possible values are 'connected',
	 *                'disconnected', or platform-specific strings.
	 */
	private function get_platform_status( $platform ) {
		$request = $this->request_factory->create( $platform );

		return $request->get_platform_status();
	}
}
