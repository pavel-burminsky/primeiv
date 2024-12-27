<?php
/**
 * Defines the System_Info class
 *
 * @package WP_Business_Reviews\Includes\Admin
 * @since   1.2.0
 */

namespace WP_Business_Reviews\Includes\Admin;

use WP_Business_Reviews\Includes\Platform_Manager;
use WP_Business_Reviews\Includes\Admin\Health_Check\Health_Check_Debug_Data;
use WP_Business_Reviews\Includes\View;
use WP_Business_Reviews\Includes\Refresher\Auto_Review_Refresher;

/**
 * Updates the database to ensure compatibility with the latest plugin version.
 *
 * @since 1.2.0
 */
class System_Info {
	/**
	 * Instantiates the System_Info object.
	 *
	 * @since 1.2.0
	 *
	 * @param Platform_Manager $platform_manager Platform_Manager instance.
	 */
	public function __construct( Platform_Manager $platform_manager ) {
		$this->platform_manager = $platform_manager;
	}

	/**
	 * Registers functionality with WordPress hooks.
	 *
	 * @since 1.2.0
	 */
	public function register() {
		add_action( 'wpbr_admin_page_wpbr-system-info', array( $this, 'render' ) );
		add_action( 'wp_ajax_nopriv_wpbr_test_ajax', array( $this, 'test_ajax' ) );
	}

	/**
	 * Retrieves system info.
	 *
	 * @since 1.2.0
	 */
	protected function get_info() {
		// Get the core system info using Health Check functionality.
		$wp_info = Health_Check_Debug_Data::debug_data();

		$wpbr_info = array(
			'wp_business_reviews' => array(
				'label'  => __( 'WP Business Reviews', 'wp-business-reviews' ),
				'fields' => array(
					array(
						'label' => __( 'WPBR Plugin Version', 'wp-business-reviews' ),
						'value' => WPBR_VERSION,
					),
					array(
						'label' => __( 'WPBR Database Version', 'wp-business-reviews' ),
						'value' => get_option( 'wpbr_db_version' ),
					),
					array(
						'label' => __( 'Upgraded From', 'wp-business-reviews' ),
						'value' => get_option( 'wpbr_updated_from' ),
					),
					array(
						'label' => __( 'Active Platforms', 'wp-business-reviews' ),
						'value' => $this->get_active_platforms_string(),
					),
					array(
						'label' => __( 'Connected Platforms', 'wp-business-reviews' ),
						'value' => $this->get_connected_platforms_string(),
					),
					array(
						'label' => __( 'Platforms Failing Auto Refresh', 'wp-business-reviews' ),
						'value' => $this->get_failed_platforms_string(),
					),
					array(
						'label' => __( 'Total Reviews', 'wp-business-reviews' ),
						'value' => $this->get_review_count(),
					),
					array(
						'label' => __( 'Total Collections', 'wp-business-reviews' ),
						'value' => $this->get_collection_count(),
					),
					array(
						'label' => __( 'Total Review Sources', 'wp-business-reviews' ),
						'value' => count( Auto_Review_Refresher::get_review_source_ids() ),
					),
					array(
						'label' => __( 'Last Refreshed Review Source Count', 'wp-business-reviews' ),
						'value' => get_option( 'wpbr_last_refreshed_review_source_count', 0 ),
					),
					array(
						'label' => __( 'Last Scheduled Event', 'wp-business-reviews' ),
						'value' => $this->get_last_scheduled_event(),
					),
					array(
						'label' => __( 'Automatic Refresh', 'wp-business-reviews' ),
						'value' => $this->get_auto_refresh(),
					),
				),
			),
		);

		// Add admin AJAX field to WordPress section.
		if ( isset( $wp_info['wp-core']['fields'] ) ) {
			$wp_info['wp-core']['fields'][] = array(
				'label' => __( 'Admin AJAX', 'wp-business-reviews' ),
				'value' => $this->is_admin_ajax_accessible() ? __( 'Accessible', 'wp-business-reviews' ) : __( 'Inaccessible', 'wp-business-reviews' ),
			);
		}

		// Add host name field to Server section.
		if ( isset( $wp_info['wp-server']['fields'] ) ) {
			$wp_info['wp-server']['fields'][] = array(
				'label' => __( 'Host Name', 'wp-business-reviews' ),
				'value' => gethostname(),
			);
		}

		$info = array_merge( $wpbr_info, $wp_info );

		return $info;
	}

	/**
	 * Retrieves the total number of reviews.
	 *
	 * @since 1.2.0
	 *
	 * @return int The total number of reviews.
	 */
	protected function get_review_count() {
		$args = array(
			'fields'                 => 'ids',
			'posts_per_page'         => -1,
			'post_type'              => 'wpbr_review',
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
		);
		$post_ids = get_posts( $args );

		if ( empty( $post_ids ) ) {
			return 0;
		}

		return count( $post_ids );
	}

	/**
	 * Retrieves the total number of collections.
	 *
	 * @since 1.3.0
	 *
	 * @return int The total number of collections.
	 */
	protected function get_collection_count() {
		$args = array(
			'fields'                 => 'ids',
			'posts_per_page'         => -1,
			'post_type'              => 'wpbr_collection',
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
		);
		$post_ids = get_posts( $args );

		if ( empty( $post_ids ) ) {
			return 0;
		}

		return count( $post_ids );
	}

	/**
	 * Retrieves active platforms.
	 *
	 * @since 1.3.0
	 *
	 * @return string Comma-separated list of active platforms.
	 */
	protected function get_active_platforms_string() {
		$active_platforms = $this->platform_manager->get_active_platforms();

		if ( empty( $active_platforms ) ) {
			return __( 'None', 'wp-business-reviews' );
		}

		return join( ', ', $active_platforms );
	}

	/**
	 * Retrieves connected platforms.
	 *
	 * @since 1.3.0
	 *
	 * @return string Comma-separated list of connected platforms.
	 */
	protected function get_connected_platforms_string() {
		$connected_platforms = $this->platform_manager->get_connected_platforms();

		if ( empty( $connected_platforms ) ) {
			return __( 'None', 'wp-business-reviews' );
		}

		return join( ', ', $connected_platforms );
	}

	/**
	 * Retrieves platforms experiencing failed connections.
	 *
	 * @since 1.3.0
	 *
	 * @return string Comma-separated list of failed platforms.
	 */
	protected function get_failed_platforms_string() {
		$failed_platforms = $this->platform_manager->get_failed_platforms();

		if ( empty( $failed_platforms ) ) {
			return __( 'None', 'wp-business-reviews' );
		}

		return join( ', ', $failed_platforms );
	}

	/**
	 * Determines whether admin AJAX is accessible.
	 *
	 * @since 1.2.0
	 *
	 * @return bool True if accessible, false otherwise.
	 */
	protected function is_admin_ajax_accessible() {
		$ajax_url = admin_url( 'admin-ajax.php' );
		$args = array(
			'body' => array(
				'action' => 'wpbr_test_ajax',
			),
		);

		$response = wp_remote_post( $ajax_url, $args );

		if ( is_wp_error( $response) ) {
			return false;
		}

		$body = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( isset( $body['success'] ) && true === $body['success'] ) {
			return true;
		}

		return false;
	}

	/**
	 * Sends a successful response to indicate that admin AJAX is working.
	 *
	 * @since 1.2.0
	 */
	public function test_ajax() {
		wp_send_json_success();
	}

	/**
	 * Retrieves timestamp of the last scheduled plugin event to run via WP Cron.
	 *
	 * @since 1.2.1
	 *
	 * @return string The timestamp or message if no events found.
	 */
	public function get_last_scheduled_event() {
		$timestamp = get_option( 'wpbr_last_scheduled_event' );

		if ( $timestamp ) {
			return $timestamp . ' GMT' . get_option(' gmt_offset' );
		}

		return __( 'No events found', 'wp-business-reviews' );
	}

	/**
	 * Retrieves the automatic refresh setting in title case.
	 *
	 * @since 1.3.0
	 *
	 * @return string The auto refresh interval.
	 */
	public function get_auto_refresh() {
		$option = get_option( 'wpbr_auto_refresh' );
		$interval = '';

		switch ( $option ) {
			case 'weekly':
				$interval = __( 'Weekly', 'wp-business-reviews' );
				break;
			case 'daily':
				$interval = __( 'Daily', 'wp-business-reviews' );
				break;
			case 'disabled':
				$interval = __( 'Disabled', 'wp-business-reviews' );
				break;
			default:
				$interval = __( 'Undefined', 'wp-business-reviews' );
				break;
		}

		return $interval;
	}

	/**
	 * Renders the system info UI.
	 *
	 * @since 1.2.0
	 */
	public function render() {
		Health_Check_Debug_Data::check_for_updates();
		$view_object = new View( WPBR_PLUGIN_DIR . 'views/admin/system-info.php' );
		$view_object->render(
			array(
				'info'   => $this->get_info(),
			)
		);
	}
}
