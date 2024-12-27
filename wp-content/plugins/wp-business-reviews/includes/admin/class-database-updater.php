<?php
/**
 * Defines the Database_Updater class
 *
 * @package WP_Business_Reviews\Includes\Admin
 * @since   1.2.0
 */

namespace WP_Business_Reviews\Includes\Admin;

use WP_Business_Reviews\Includes\Libraries\WP_Background_Processing\WP_Background_Process;
use WP_Business_Reviews\Includes\Admin\Admin_Notice;

/**
 * Updates the database to ensure compatibility with the latest plugin version.
 *
 * @since 1.2.0
 */
class Database_Updater extends WP_Background_Process {
	/**
	 * Database updates that need to be run per version.
	 *
	 * @see wpbr-update-functions.php
	 *
 	 * @since 1.2.0
	 * @var array $db_updates
	 */
	protected static $db_updates = array(
		'1.2.0' => array(
			'wpbr_v1_2_0_normalize_review_ratings',
			'wpbr_v1_2_0_update_collections',
			'wpbr_v1_2_0_update_facebook_image_urls',
			'wpbr_v1_2_0_update_db_version',
		),
	);

	/**
	 * Instantiates the Database_Updater object.
	 *
	 * @since 1.2.0
	 */
	public function __construct() {
		// Use unique prefix per blog so each blog has separate queue.
		$this->prefix = 'wp_' . get_current_blog_id();
		$this->action = 'wpbr_db_update';

		parent::__construct();
	}

	/**
	 * Registers functionality with WordPress hooks.
	 *
	 * @since 1.2.0
	 */
	public function register() {
		add_action( 'init', array( $this, 'check_version' ) );
		add_action( 'admin_init', array( $this, 'add_admin_notice' ) );
		add_action( 'admin_init', array( $this, 'check_user_initiated_update' ) );
	}

	/**
	 * Checks whether an update may be necessary based on plugin version.
	 *
	 * @since 1.2.0
	 */
	public function check_version() {
		$db_version = get_option( 'wpbr_db_version' );

		// Ensure a database version has been stored if not previously recorded.
		if ( empty( $db_version ) ) {
			$updated_from = get_option( 'wpbr_updated_from' );
			update_option( 'wpbr_db_version', $updated_from  );
			update_option( 'wpbr_admin_notice_db_update_complete', 'disabled' );
		}

		if ( version_compare( $db_version, WPBR_VERSION, '<' ) ) {
			$this->maybe_update_db_version();
		}
	}

	/**
	 * Adds an admin notice to communicate database update status.
	 *
	 * Once the success notice is dismissed, it stays dismissed until the next
	 * database update is required.
	 *
	 * @since 1.2.0
	 */
	public function add_admin_notice() {
		if ( $this->needs_background_updates() ) {
			if ( ! $this->is_queue_empty() || ! empty( $_GET['wpbr_db_update'] ) ) {
				Admin_Notices::add_notice( 'db_update_in_progress' );
			} else {
				Admin_Notices::add_notice( 'db_update_required' );
			}
		} elseif (
			current_user_can( 'manage_options' )
			&& 'disabled' !== get_option( 'wpbr_admin_notice_db_update_complete' )
		) {
			Admin_Notices::add_notice( 'db_update_complete' );
		}
	}

	/**
	 * Checks whether an update has been manually initiated by a user.
	 *
	 * @since 1.2.0
	 */
	public function check_user_initiated_update() {
		if ( ! empty( $_GET['wpbr_db_update'] ) ) {
			check_admin_referer( 'wpbr_db_update', 'wpbr_db_update_nonce' );
			$this->dispatch_updates();
		}
	}

	/**
	 * Runs an individual task in the queue.
	 *
	 * If the callback returns a truthy value, it will be added to the end
	 * of the queue for re-processing. If the callback returns a falsy value,
	 * it is considered complete and removed from the queue.
	 *
	 * @param string $callback Update callback function.
	 * @return string|bool Name of the callback function or false.
	 */
	protected function task( $callback ) {
		$result = false;

		include_once dirname( __FILE__ ) . '/wpbr-update-functions.php';

		if ( is_callable( $callback ) ) {
			$result = (bool) call_user_func( $callback, $this );
		}

		return $result ? $callback : false;
	}

	/**
	 * Dispatches updates from all versions greater than the database version.
	 *
	 * @since 1.2.0
	 */
	protected function dispatch_updates() {
		$update_queued      = false;
		$current_db_version = get_option( 'wpbr_db_version' );

		foreach ( self::$db_updates as $version => $update_callbacks ) {
			if ( version_compare( $current_db_version, $version, '<' ) ) {
				foreach ( $update_callbacks as $update_callback ) {
					$this->push_to_queue( $update_callback );
					$update_queued = true;
				}
			}
		}

		if ( $update_queued ) {
			$this->save()->dispatch();
		}
	}

	/**
	 * Updates the database version or notifies the user of background updates.
	 *
	 * Not all plugin versions require background updates, so in those cases the
	 * database version can be updated automatically.
	 *
	 * @since 1.2.0
	 */
	protected function maybe_update_db_version() {
		if ( ! $this->needs_background_updates() ) {
			// No background updates available, so update DB version immediately.
			$this->update_db_version();
			update_option( 'wpbr_admin_notice_db_update_complete', 'disabled' );
		} else {
			// Background updates are available, so reset notices.
			delete_option( 'wpbr_admin_notice_db_update_complete' );
		}
	}

	/**
	 * Determines whether background updates are required.
	 *
	 * Background updates are considered necessary if the current database version
	 * is lesser than the highest version present in the `$db_updates` array.
	 *
	 * @since 1.2.0
	 *
	 * @return bool Whether a database update is needed.
	 */
	protected function needs_background_updates() {
		$current_db_version = get_option( 'wpbr_db_version', null );

		return ! is_null( $current_db_version ) && version_compare( $current_db_version, max( array_keys( self::$db_updates ) ), '<' );
	}

	/**
	 * Updates the database version.
	 *
	 * @since 1.2.0
	 *
	 * @param string $version The version being updated.
	 */
	protected function update_db_version( $version = null ) {
		update_option( 'wpbr_db_version', is_null( $version ) ? WPBR_VERSION : $version );
	}
}
