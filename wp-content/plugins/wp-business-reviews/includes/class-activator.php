<?php
/**
 * Fired during plugin activation
 *
 * @package WP_Business_Reviews\Includes
 * @since   0.1.0
 */

namespace WP_Business_Reviews\Includes;


/**
 * Defines all code necessary to run during the plugin's activation.
 *
 * @since 0.1.0
 */
class Activator {
	/**
	 * Scheduler of events.
	 *
	 * @since 1.2.1
	 * @var Cron_Scheduler $cron_scheduler
	 */
	private $cron_scheduler;

	/**
	 * Post type and taxonomy registrar.
	 *
	 * @since 0.1.0
	 * @var Post_Types $post_types
	 */
	private $post_types;

	/**
	 * Instantiates the Activator object.
	 *
	 * @since 0.1.0
	 *
	 * @param Cron_Scheduler $cron_scheduler Scheduler of events.
	 * @param Post_Types     $post_types     Post type and taxonomy registrar.
	 */
	public function __construct(
		Cron_Scheduler $cron_scheduler,
		Post_Types $post_types
	) {
		$this->cron_scheduler = $cron_scheduler;
		$this->post_types     = $post_types;
	}

	/**
	 * Registers functionality with WordPress hooks.
	 *
	 * @since 1.2.0
	 */
	public function register() {
		add_action( 'init', array( $this, 'check_version' ) );
	}

	/**
	 * Checks whether activation needs run.
	 *
	 * @since 1.2.0
	 */
	public function check_version() {
		if ( version_compare( get_option( 'wpbr_version' ), WPBR_VERSION, '<' ) ) {
			$this->activate();
		}
	}

	/**
	 * Activates the plugin on one or more sites.
	 *
	 * @since 0.1.0
	 *
	 * @param bool $network_wide Optional. Whether the plugin is being enabled on
	 *                           all network sites or just the current site. Default
	 *                           false.
	 */
	public function activate( $network_wide = false ) {
		if ( $network_wide ) {
			$this->activate_multisite();
		} else {
			$this->activate_single_site();
		}
	}

	/**
	 * Activates the plugin on a multisite network.
	 *
	 * @since 0.1.0
	 */
	private function activate_multisite() {
		$site_ids = get_sites( array(
			'fields'     => 'ids',
			'network_id' => get_current_network_id()
		) );

		foreach( $site_ids as $site_id) {
			switch_to_blog( $site_id );
			$this->activate_single_site();
			restore_current_blog();
		}
	}

	/**
	 * Activates the plugin on a single site.
	 *
	 * @since 0.2.1 Replace `get_plugin_info()` with WPBR_VERSION constant.
	 * @since 0.1.0
	 */
	private function activate_single_site() {
		$last_active_version = get_option( 'wpbr_version', WPBR_VERSION );
		$updated_from        = get_option( 'wpbr_updated_from' );

		// Register post types and taxonomies.
		$this->post_types->register_post_types();
		$this->post_types->register_taxonomies();
		$this->insert_terms();

		// Set default settings.
		$this->set_default_settings();

		// Flush rewrite rules for new post types and taxonomies.
		flush_rewrite_rules();

		if (
			empty( $updated_from )
			|| version_compare( $last_active_version, WPBR_VERSION, '<' )
		) {
			// Update option on fresh install or when version change occurs.
			update_option( 'wpbr_updated_from', $last_active_version );
		}

		update_option( 'wpbr_version', WPBR_VERSION );

		// Schedule cron events.
		$this->cron_scheduler->schedule_events();
	}

	/**
	 * Insert default terms.
	 *
	 * @since 1.2.0
	 */
	public function insert_terms() {
		$taxonomies = array(
			'wpbr_platform'  => Platform_Manager::get_platforms(),
			'wpbr_attribute' => array(
				'blank'          => 'Blank',
				'recommendation' => 'Recommendation',
				'refreshed'      => 'Refreshed',
			),
		);

		foreach ( $taxonomies as $taxonomy => $terms ) {
			foreach ( $terms as $slug => $term ) {
				if ( ! term_exists( $slug ) ) {
					$args = array(
						'slug' => $slug,
					);

					wp_insert_term( $term, $taxonomy, $args );
				}
			}
		}
	}

	/**
	 * Sets default settings if they are not already populated.
	 *
	 * @since 0.2.0
	 */
	private function set_default_settings() {
		$default_settings = array(
			'wpbr_plugin_styles'      => 'enabled',
			'wpbr_nofollow_links'     => 'enabled',
			'wpbr_auto_refresh'       => 'weekly',
			'wpbr_uninstall_behavior' => 'keep',
			'wpbr_active_platforms'   => array(
				'google_places' => 'enabled',
				'facebook'      => 'enabled',
				'yelp'          => 'enabled',
				'zomato'        => 'enabled',
			),
		);

		foreach ( $default_settings as $key => $value ) {
			if ( ! get_option( $key ) ) {
				update_option( $key, $value );
			}
		}
	}
}
