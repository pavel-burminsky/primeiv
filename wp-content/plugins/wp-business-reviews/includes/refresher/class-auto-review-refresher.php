<?php
/**
 * Defines the Auto_Review_Refresher class
 *
 * @link https://wpbusinessreviews.com
 *
 * @package WP_Business_Reviews\Includes\Refresher
 * @since 1.3.0
 */

namespace WP_Business_Reviews\Includes\Refresher;

use WP_Business_Reviews\Includes\Libraries\WP_Background_Processing\WP_Background_Process;
use WP_Business_Reviews\Includes\Refresher\Review_Refresher;
use WP_Business_Reviews\Includes\Deserializer\Review_Source_Deserializer;
use WP_Query;
use WP_Business_Reviews\Includes\Cron_Scheduler;

/**
 * Refreshes reviews from multiple review sources over time.
 *
 * @since 1.3.0
 */
class Auto_Review_Refresher extends WP_Background_Process {
	/**
	 * User-defined interval that determines how often reviews are refreshed.
	 *
	 * @since 1.3.0
	 * @var string
	 */
	protected $interval;

	/**
	 * Retriever of review sources.
	 *
	 * @since 1.3.0
	 * @var Review_Source_Deserializer
	 */
	protected $review_source_deserializer;

	/**
	 * Refresher of reviews for a single review source.
	 *
	 * @since 1.3.0
	 * @var Review_Refresher
	 */
	protected $review_refresher;

	/**
	 * Platforms that have experienced a failed connection.
	 *
	 * @since 1.3.0
	 * @var array
	 */
	protected $failed_platforms;

	/**
	 * How many times each platform has failed.
	 *
	 * @since 1.3.0
	 * @var array
	 */
	protected $failed_platform_counts;

	/**
	 * Instantiates the Auto_Review_Refresher object.
	 *
	 * @since 1.3.0
	 *
	 * @param string                     $interval                   Refresh interval.
	 * @param Review_Refresher           $review_refresher           Refresher of reviews.
	 * @param Review_Source_Deserializer $review_source_deserializer Retriever of review sources.
	 * @param array                      $failed_platforms           Failed platforms.
	 */
	public function __construct(
		$interval,
		Review_Refresher $review_refresher,
		Review_Source_Deserializer $review_source_deserializer,
		array $failed_platforms = array()
	) {
		$this->interval                   = $interval;
		$this->review_refresher           = $review_refresher;
		$this->review_source_deserializer = $review_source_deserializer;
		$this->failed_platforms           = $failed_platforms;
		$this->prefix                     = 'wp_' . get_current_blog_id();
		$this->action                     = 'wpbr_refresh_reviews';

		parent::__construct();
	}

	/**
	 * Registers functionality with WordPress hooks.
	 *
	 * @since 1.3.0
	 */
	public function register() {
		if ( 'weekly' === $this->interval ) {
			add_action( 'wpbr_run_weekly_events', array( $this, 'dispatch_updates' ) );
		} elseif ( 'daily' === $this->interval ) {
			add_action( 'wpbr_run_daily_events', array( $this, 'dispatch_updates' ) );
		}
	}

	/**
	 * Retrieves all review source IDs.
	 *
	 * The query targets the wpbr_collection post type to ensure that review source
	 * post IDs are only returned if they have a child collection.
	 *
	 * @since 1.3.0
	 *
	 * @return array Array of review source post IDs.
	 */
	public static function get_review_source_ids() {
		$unique_ids = array();
		$args       = array(
			'fields'                 => 'id=>parent',
			'no_found_rows'          => true,
			'posts_per_page'         => -1,
			'post_type'              => 'wpbr_collection',
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
		);

		// Query collection posts to get their post parent IDs.
		$query            = new WP_Query( $args );
		$collection_posts = $query->posts;

		if ( ! empty( $collection_posts ) ) {
			$review_source_post_ids = array();

			// Extract post parent IDs (aka the review source post IDs).
			foreach ( $collection_posts as $k => $v ) {
				if ( 0 !== $v->post_parent ) {
					$review_source_post_ids[] = $v->post_parent;
				}
			}

			// Remove duplicate post IDs.
			$unique_ids = array_unique( $review_source_post_ids );
		}

		return $unique_ids;
	}

	/**
	 * Refreshes a single review source and its reviews.
	 *
	 * @since 1.3.0
	 *
	 * @param int $post_id The review source post ID.
	 * @return bool True if reviews were refreshed, false otherwise.
	 */
	public function refresh_review_source( $post_id ) {
		$review_source = $this->review_source_deserializer->get_review_source(
			$post_id
		);

		$platform = $review_source->get_platform();

		// Bail early if platform has already failed 5 times.
		if ( 5 < $this->get_failed_count( $platform ) ) {
			return false;
		}

		// TODO: Refresh review source meta data.

		// Refresh reviews belonging to the review source.
		$refreshed_reviews = $this->review_refresher->refresh_reviews(
			$platform,
			$review_source->get_review_source_id(),
			$review_source->get_post_id()
		);

		if ( is_wp_error( $refreshed_reviews ) ) {
			$this->increment_failed_count( $platform );
			$this->add_failed_platform( $platform );

			return false;
		}

		$this->remove_failed_platform( $platform );

		return true;
	}

	/**
	 * Begins the background update process.
	 *
	 * @since 1.3.0
	 */
	public function dispatch_updates() {
		Cron_Scheduler::update_last_scheduled_event();

		// Reset options since this is the start of a new process.
		delete_option( 'wpbr_last_refreshed_review_source_count' );

		$review_source_post_ids = $this->get_review_source_ids();

		foreach ( $review_source_post_ids as $post_id ) {
			$this->push_to_queue( $post_id );
		}

		$this->save()->dispatch();
	}

	/**
	 * Runs an individual task in the queue.
	 *
	 * If the task returns a truthy value, it will be added to the end
	 * of the queue for re-processing. If the task returns a falsy value,
	 * it is considered complete and removed from the queue.
	 *
	 * @since 1.3.0
	 *
	 * @param int $review_source_post_id Review source post ID.
	 * @return bool False to indicate the task is complete.
	 */
	protected function task( $review_source_post_id ) {
		if ( $this->refresh_review_source( $review_source_post_id ) ) {
			$count = absint( get_option( 'wpbr_last_refreshed_review_source_count', 0 ) );
			update_option( 'wpbr_last_refreshed_review_source_count', ++$count );
		}

		return false;
	}

	/**
	 * Adds a failed platform.
	 *
	 * @since 1.3.0
	 *
	 * @param string $platform Platform ID.
	 * @return bool Whether the platform updated completed.
	 */
	protected function add_failed_platform( $platform ) {
		if ( ! in_array( $platform, $this->failed_platforms ) ) {
			$this->failed_platforms[] = $platform;

			return update_option( 'wpbr_failed_platforms', $this->failed_platforms );
		}

		return false;
	}

	/**
	 * Removes a failed platform.
	 *
	 * @since 1.3.0
	 *
	 * @param string $platform Platform ID.
	 * @return bool Whether the platform updated completed.
	 */
	protected function remove_failed_platform( $platform ) {
		$key = array_search( $platform, $this->failed_platforms );

		if ( $key !== false ) {
			unset( $this->failed_platforms[ $key ] );

			/** This action is documented in includes/refresher/class-review-refresher.php */
			do_action( 'wpbr_platform_status_update', $platform, 'connected' );

			return update_option( 'wpbr_failed_platforms', $this->failed_platforms );
		}

		return false;
	}

	/**
	 * Increments a the number of failures for a specific platform.
	 *
	 * @since 1.3.0
	 *
	 * @param string $platform Platform ID.
	 * @return int The number of failures for the platform after incrementing.
	 */
	protected function increment_failed_count( $platform ) {
		$failed_count = $this->get_failed_count( $platform );
		$this->failed_platform_counts[ $platform ] = ++$failed_count;

		return $failed_count;
	}

	/**
	 * Retreives the number of failures for a specific platform in a session.
	 *
	 * @since 1.3.0
	 *
	 * @param string $platform Platform ID.
	 * @return int The current number of failures for the platform.
	 */
	protected function get_failed_count( $platform ) {
		if ( isset( $this->failed_platform_counts[ $platform ] ) ) {
			return $this->failed_platform_counts[ $platform ];
		}

		return 0;
	}
}
