<?php
/**
 * Defines the Blank_Slate class
 *
 * @package WP_Business_Reviews\Includes\Admin
 * @since   0.1.0
 */

namespace WP_Business_Reviews\Includes\Admin;

use \WP_Query;
use WP_Business_Reviews\Includes\View;

class Blank_Slate {
	/**
	 * The post types in which a blank slate is rendered.
	 *
	 * @since  0.1.0
	 * @var array $post_types
	 */
	public $post_types;

	/**
	 * The current post type.
	 *
	 * @since  0.1.0
	 * @var array $current_post_type
	 */
	public $current_post_type;

	/**
	 * Instantiates a Blank_Slate object.
	 *
	 * @since 0.1.0
	 *
	 * @param array $post_types Post types in which a blank slate is rendered.
	 */
	public function __construct( $post_types ) {
		$this->post_types = $post_types;
	}

	/**
	 * Registers functionality with WordPress hooks.
	 *
	 * @since 0.1.0
	 */
	public function register() {
		add_action( 'current_screen', array( $this, 'init' ) );
	}

	/**
	 * Initializes the object for use.
	 *
	 * The blank slate is only initialized on the appropriate screen when no
	 * posts exist.
	 *
	 * @since 0.1.0
	 */
	public function init() {
		$screen                  = get_current_screen();
		$screen_id               = $screen->id;
		$this->current_post_type = $screen->post_type;

		/**
		 * Return early if any of the following conditions are true:
		 *
		 *     - The current post type has not been registered for a blank slate.
		 *     - The current screen is not the edit screen.
		 *     - One or more posts of this type already exists.
		 */
		if (
			! in_array( $this->current_post_type, $this->post_types )
			|| 'edit-' . $this->current_post_type !== $screen_id
			|| $this->post_exists( $this->current_post_type )
		) {
			return;
		}

		add_action( 'admin_head', array( $this, 'hide_ui' ) );
		add_filter( 'views_edit-' . $this->current_post_type, array( $this, 'prepend_to_list_table' ) );
		add_filter( 'admin_body_class', array( $this, 'add_admin_body_class' ) );
	}

	/**
	 * Prepends the blank slate to the list table.
	 *
	 * @param array $views An array of available list table views.
	 * @return array An array of available list table views.
	 */
	public function prepend_to_list_table( $views ) {
		$this->render();
		return $views;
	}

	/**
	 * Adds admin body class to all admin pages created by the plugin.
	 *
	 * @since 0.1.0
	 *
	 * @param  string $classes Space-separated list of CSS classes.
	 * @return string Filtered body classes.
	 */
	public function add_admin_body_class( $classes ) {
		// Leave space on both sides so other plugins do not conflict.
		return $classes . ' wpbr-admin--blank-slate ';
	}

	/**
	 * Hides non-essential UI elements when blank slate content is on screen.
	 *
	 * @since 0.1.0
	 */
	public function hide_ui() {
		echo '<style type="text/css">.wpbr-admin .page-title-action, .wpbr-admin .subsubsub, .wpbr-admin .wp-list-table, .wpbr-admin .tablenav.top {display: none; }</style>';
	}

	/**
	 * Determines if at least one post of a given post type exists.
	 *
	 * @since 0.1.0
	 *
	 * @param string $post_type Post type used in the query.
	 * @return bool True if post exists, otherwise false.
	 */
	private function post_exists( $post_type ) {
		// Attempt to get a single post of the post type.
		$query = new WP_Query( array(
			'post_type'              => $post_type,
			'posts_per_page'         => 1,
			'no_found_rows'          => false,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
			'fields'                 => 'ids',
			'post_status'            => array( 'any', 'trash' ),
		) );

		return $query->have_posts();
	}

	/**
	 * Renders the blank slate.
	 *
	 * @since  0.1.0
	 */
	public function render() {
		$post_slug   = str_replace( '_', '-', $this->current_post_type );
		$view_object = new View( WPBR_PLUGIN_DIR . 'views/admin/blank-slate/' . $post_slug . '.php' );
		$view_object->render();
	}
}
