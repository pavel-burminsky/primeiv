<?php
/**
 * Defines the Cron_Scheduler class
 *
 * @link https://wpbusinessreviews.com
 *
 * @package WP_Business_Reviews\Includes
 * @since 1.2.1
 */

namespace WP_Business_Reviews\Includes;

 /**
 * Schedules cron events for the plugin.
 *
 * @since 1.2.1
 */
class Cron_Scheduler {
	/**
	 * Registers functionality with WordPress hooks.
	 *
	 * @since 1.2.1
	 */
	public function register() {
		add_filter( 'cron_schedules', array( $this, 'add_schedules' ) );
		add_action( 'init', array( $this, 'schedule_events' ) );
	}

	/**
	 * Updates the time of the last scheduled event in database.
	 *
	 * @since 1.2.1
	 */
	public static function update_last_scheduled_event() {
		$timestamp = current_time( 'mysql' );
		update_option( 'wpbr_last_scheduled_event', $timestamp, false );
	}

	/**
	 * Adds custom cron schedules.
	 *
	 * @since 1.3.0
	 *
	 * @param array $schedules Cron schedules.
	 */
	public function add_schedules( $schedules ) {
		$schedules['wpbr_weekly'] = array(
			'interval' => 604800,
			'display'  => __('Once Weekly', 'wp-business-reviews')
		);

		return $schedules;
	}

	/**
	 * Schedules all cron events.
	 *
	 * @since 1.2.1
	 */
	public function schedule_events() {
		$this->schedule_daily_events();
		$this->schedule_weekly_events();
	}

	/**
	 * Unschedules cron events.
	 *
	 * @since 1.2.1
	 */
	public function unschedule_events() {
		$daily_timestamp  = wp_next_scheduled( 'wpbr_run_daily_events' );
		$weekly_timestamp = wp_next_scheduled( 'wpbr_run_weekly_events' );

		if ( $daily_timestamp ) {
			wp_unschedule_event( $daily_timestamp, 'wpbr_run_daily_events' );
		}

		if ( $weekly_timestamp ) {
			wp_unschedule_event( $weekly_timestamp, 'wpbr_run_weekly_events' );
		}
	}

	/**
	 * Schedules daily events.
	 *
	 * @since 1.2.1
	 */
	private function schedule_daily_events() {
		if ( ! wp_next_scheduled( 'wpbr_run_daily_events' ) ) {
			wp_schedule_event( time(), 'daily', 'wpbr_run_daily_events' );
		}
	}

	/**
	 * Schedules weekly events.
	 *
	 * @since 1.3.0
	 */
	private function schedule_weekly_events() {
		if ( ! wp_next_scheduled( 'wpbr_run_weekly_events' ) ) {
			wp_schedule_event( time(), 'wpbr_weekly', 'wpbr_run_weekly_events' );
		}
	}
}
