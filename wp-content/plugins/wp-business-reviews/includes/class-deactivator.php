<?php
/**
 * Fired during plugin deactivation
 *
 * @package WP_Business_Reviews\Includes
 * @since   0.1.0
 */

namespace WP_Business_Reviews\Includes;

/**
 * Defines all code necessary to run during the plugin's deactivation.
 *
 * This functionality is specific to deactivating (not uninstalling) the plugin.
 * The uninstall procedure is defined in uninstall.php.
 *
 * @since 0.1.0
 */
class Deactivator {
	/**
	 * Scheduler of events.
	 *
	 * @since 1.2.1
	 * @var Cron_Scheduler $cron_scheduler
	 */
	private $cron_scheduler;

	/**
	 * Instantiates a Deactivator object.
	 *
	 * @since 1.2.1
	 *
	 * @param Cron_Scheduler $cron_scheduler Scheduler of events.
	 */
	public function __construct( Cron_Scheduler $cron_scheduler ) {
		$this->cron_scheduler = $cron_scheduler;
	}

	/**
	 * Deactivates the plugin.
	 *
	 * @since 0.1.0
	 */
	public function deactivate() {
		$this->cron_scheduler->unschedule_events();
	}
}
