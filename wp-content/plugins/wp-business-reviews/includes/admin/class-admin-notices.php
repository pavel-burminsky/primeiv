<?php
/**
 * Defines the Admin_Notices class
 *
 * @package WP_Business_Reviews\Includes\Admin
 * @since 1.2.0
 */

namespace WP_Business_Reviews\Includes\Admin;

use WP_Business_Reviews\Includes\View;

/**
 * Displays admin notices for the plugin.
 *
 * @since 1.2.0
 */
class Admin_Notices {
	/**
	 * Admin notices to be rendered.
	 *
	 * @since 1.2.0
	 * @var array $notices
	 */
	private static $notices = array();

	/**
	 * Preset plugin notices and corresponding callbacks.
	 *
	 * @since 1.2.0
	 * @var array $plugin_notices
	 */
	private static $plugin_notices = array(
		'collection_saved'           => 'collection_saved_notice',
		'collection_trashed'         => 'collection_trashed_notice',
		'db_update_complete'         => 'db_update_complete_notice',
		'db_update_in_progress'      => 'db_update_in_progress_notice',
		'db_update_required'         => 'db_update_required_notice',
		'settings_platform_required' => 'settings_platform_required_notice',
		'settings_saved'             => 'settings_saved_notice',
		'facebook_page_tokens_error' => 'facebook_page_tokens_error_notice',
		'facebook_connect'           => 'facebook_connect_notice',
	);

	/**
	 * Registers functionality with WordPress hooks.
	 *
	 * @since 1.2.0
	 */
	public function register() {
		add_filter( 'removable_query_args', array( $this, 'remove_query_args' ) );
		add_action( 'admin_notices', array( $this, 'parse_notices_from_url' ) );
		add_action( 'admin_notices', array( $this, 'render_notices' ) );
		add_action( 'wp_ajax_wpbr_disable_admin_notice', array( $this, 'ajax_disable' ) );
	}

	/**
	 * Removes query variables from a URL immediately after page load.
	 *
	 * @since 1.2.0
	 *
	 * @param array $args Array of query variables to remove from a URL.
	 * @return array Modified array of query variables.
	 */
	public function remove_query_args( $args ) {
		return array_merge( $args, array(
			'wpbr_notice',
		) );
	}

	/**
	 * Adds a notice while preventing duplicates.
	 *
	 * @since 1.2.0
	 *
	 * @param string $id Notice ID.
	 */
	public static function add_notice( $id ) {
		// Bail if notice has already been added or does not have a callback.
		if (
			array_key_exists( $id, self::$notices )
			|| ! array_key_exists( $id, self::$plugin_notices )
		) {
			return;
		}

		$callback = self::$plugin_notices[ $id ];
		self::$notices[ $id ] = self::$callback();
	}

	/**
	 * Parses notices based on a URL parameter that defines the notice ID.
	 *
	 * @since 1.2.0
	 */
	public function parse_notices_from_url() {
		if ( ! isset( $_GET['wpbr_notice'] ) ) {
			return;
		}

		$notice_id = sanitize_text_field( wp_unslash( $_GET['wpbr_notice'] ) );
		self::add_notice( $notice_id );
	}

	/**
	 * Renders all plugin notices using their corresponding callbacks.
	 *
	 * @since 1.2.0
	 */
	public function render_notices() {
		if ( empty( self::$notices ) || ! $this->is_valid_screen() ) {
			return;
		}

		// Move admin notice to a later hook if necessary based on page layout.
		if ( doing_action( 'admin_notices' ) && $this->needs_repositioned() ) {
			add_action( 'wpbr_admin_notices', array( $this, 'render_notices' ) );
			return;
		}

		foreach ( self::$notices as $notice ) {
			if ( 'disabled' !== get_option( "wpbr_admin_notice_{$notice->id}" ) ) {
				$notice->render();
			}
		}

		$this->print_nonce();
	}

	/**
	 * Renders a notice to communicate that a collection has been saved.
	 *
	 * @since 1.2.0
	 *
	 * @return Admin_Notice
	 */
	public static function collection_saved_notice() {
		$message = __( 'Collection saved successfully.', 'wp-business-reviews' );
		$notice  = new Admin_Notice( 'collection_saved', $message, 'success' );

		return $notice;
	}

	/**
	 * Renders a notice to communicate that a collection has been trashed.
	 *
	 * @since 1.2.0
	 *
	 * @return Admin_Notice
	 */
	public static function collection_trashed_notice() {
		$message = __( '1 collection moved to the Trash.', 'wp-business-reviews' );
		$notice  = new Admin_Notice( 'collection_trashed', $message, 'success' );

		return $notice;
	}

	/**
	 * Renders a notice to communicate that a database update is complete.
	 *
	 * @since 1.2.0
	 *
	 * @return Admin_Notice
	 */
	public static function db_update_complete_notice() {
		$message = sprintf(
			__( '%1$sWP Business Reviews database update complete.%2$s Thank you for updating to the latest version!', 'wp-business-reviews' ),
			'<strong>',
			'</strong>'
		);
		$notice  = new Admin_Notice( 'db_update_complete', $message, 'success', 'site' );

		return $notice;
	}

	/**
	 * Renders a notice to communicate that a database update is in progress.
	 *
	 * @since 1.2.0
	 *
	 * @return Admin_Notice
	 */
	public static function db_update_in_progress_notice() {
		$message = sprintf(
			__( '%1$sWP Business Reviews database update in progress.%2$s You may navigate away from this page while the update completes.', 'wp-business-reviews' ),
			'<strong>',
			'</strong>'
		);
		$notice  = new Admin_Notice( 'db_update_in_progress', $message, 'warning', '' );

		return $notice;
	}

	/**
	 * Renders a notice to communicate that a database update is required.
	 *
	 * @since 1.2.0
	 *
	 * @return Admin_Notice
	 */
	public static function db_update_required_notice() {
		$message = sprintf(
			__( '%1$sWP Business Reviews v%2$s requires a database update.%3$s This update ensures compatibility with new features and functionality of the plugin. Please make a complete backup before proceeding.', 'wp-business-reviews' ),
			'<strong>',
			WPBR_VERSION,
			'</strong>'
		);
		$cta = array(
			'text' => __( 'Run Database Update', 'wp-business-reviews' ),
			'url'  => wp_nonce_url(
			   add_query_arg( 'wpbr_db_update', '1', admin_url( 'admin.php?page=wpbr-settings' ) ),
			   'wpbr_db_update',
			   'wpbr_db_update_nonce'
		   ),
		);
		$notice = new Admin_Notice( 'db_update_required', $message, 'warning', '', $cta );

		return $notice;
	}

	/**
	 * Renders a notice to communicate that at least one platform is required.
	 *
	 * @since 1.2.0
	 *
	 * @return Admin_Notice
	 */
	public static function settings_platform_required_notice() {
		$message = __( 'Settings not saved. At least one platform must remain active.', 'wp-business-reviews' );
		$notice  = new Admin_Notice( 'settings_platform_required', $message, 'error' );

		return $notice;
	}

	/**
	 * Renders a notice to communicate that settings have been saved.
	 *
	 * @since 1.2.0
	 *
	 * @return Admin_Notice
	 */
	public static function settings_saved_notice() {
		$message = __( 'Settings saved successfully.', 'wp-business-reviews' );
		$notice  = new Admin_Notice( 'settings_saved', $message, 'success' );

		return $notice;
	}

	/**
	 * Renders a notice to communicate that an error occurred with Facebook.
	 *
	 * @since 1.3.1
	 *
	 * @return Admin_Notice
	 */
	public static function facebook_page_tokens_error_notice() {
		$message = sprintf(
			/* translators: 1: support link 2: closing anchor tag 3: system info link 4: closing anchor tag */
			__( 'An error occurred while requesting Facebook page tokens. Please %1$scontact support%2$s with a copy of your %3$ssystem info%4$s for assistance.', 'wp-business-reviews' ),
			'<a href="https://wpbusinessreviews.com/support/" target="_blank" rel="noopener noreferrer">',
			'</a>',
			'<a href="' . admin_url( 'admin.php?page=wpbr-system-info' ) . '">',
			'</a>'
		);
		$notice  = new Admin_Notice( 'facebook_page_tokens_error', $message, 'error' );

		return $notice;
	}

    /**
     * Renders a notice to communicate actions required by the user to resolve Facebook connect issues.
     *
     * @since 1.8.0
     *
     * @return Admin_Notice
     */
    public static function facebook_connect_notice() {
        $message = sprintf(
        /* translators: 1: platform link 2: closing anchor tag */
            __( 'Having problems with Facebook Connect? We updated our plugin to reflect new changes in Facebook API. Please %1$sDisconnect Facebook%2$s and then reconnect again.', 'wp-business-reviews' ),
            '<a href="' . admin_url( 'admin.php?page=wpbr-settings&wpbr_subtab=facebook&wpbr_tab=platforms' ) . '">',
            '</a>'
        );

        return new Admin_Notice( 'facebook_connect', $message, 'warning', 'site');
    }

	/**
	 * Disables the activation banner via AJAX.
	 *
	 * @since  0.1.0
	 */
	public function ajax_disable() {
		check_ajax_referer( 'wpbr_disable_admin_notice', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( __( 'Sorry, you are not allowed to dismiss admin notices.' ), 'wp-business-reviews' );
		}

		if (
			! isset( $_POST['notice'] )
			|| ! array_key_exists( $_POST['notice'], self::$plugin_notices )
		) {
			wp_die();
		}

		$notice_id = sanitize_text_field( wp_unslash( $_POST['notice'] ) );
		$this->disable( $notice_id );
		wp_die();
	}

	/**
	 * Disables the notice.
	 *
	 * @since  0.1.0
	 *
	 * @param string $notice_id Notice ID.
	 */
	protected function disable( $notice_id ) {
		update_option( "wpbr_admin_notice_{$notice_id}", 'disabled' );
	}

	/**
	 * Determines whether notices should render on a specific screen.
	 *
	 * @since 1.2.0
	 *
	 * @param string $screen_id Optional. The screen ID to validate.
	 * @return bool Whether notices should be rendered.
	 */
	protected function is_valid_screen( $screen_id = '' ) {
		if ( '' === $screen_id ) {
			$screen    = get_current_screen();
			$screen_id = $screen->id;
		}

		if (
			'dashboard' === $screen_id
			|| 'plugins' === $screen_id
			|| false !== strpos( $screen_id, 'wpbr' )
		) {
			return true;
		}

		return false;
	}

	/**
	 * Determines whether admin notices need repositioned on the current screen.
	 *
	 * The typical `admin_notices` hook does not work for all plugin screens
	 * due to special toolbars and layouts that depart from WordPress core. In
	 * these cases, it can be helpful to render notices on a later hook such as
	 * `wpbr_admin_notices`.
	 *
	 * @since 1.2.0
	 *
	 * @param string $screen_id Optional. The screen ID to validate.
	 * @return bool Whether the screen has a toolbar.
	 */
	protected function needs_repositioned( $screen_id = '' ) {
		if ( '' === $screen_id ) {
			$screen    = get_current_screen();
			$screen_id = $screen->id;
		}

		$screen_ids = array(
			'reviews_page_wpbr-builder',
			'reviews_page_wpbr-settings',
			'reviews_page_wpbr-system-info',
		);

		if ( in_array( $screen_id, $screen_ids ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Prints a nonce for use when disabling the notice via AJAX.
	 *
	 * @since 1.2.0
	 */
	protected function print_nonce() {
		wp_localize_script( 'wpbr-admin-main-script', 'wpbr_admin_notice_nonce', [
			'nonce' => wp_create_nonce( 'wpbr_disable_admin_notice' )
		] );
	}
}
