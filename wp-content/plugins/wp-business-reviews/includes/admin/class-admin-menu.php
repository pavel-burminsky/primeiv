<?php
/**
 * Defines the Admin_Menu class
 *
 * @package WP_Business_Reviews\Includes\Admin
 * @since   0.1.0
 */

namespace WP_Business_Reviews\Includes\Admin;

use WP_Business_Reviews\Includes\Config;

/**
 * Creates the menu of admin pages for the plugin.
 *
 * @since 0.1.0
 */
class Admin_Menu {
	/**
	 * Array of admin page objects.
	 *
	 * @since 0.1.0
	 * @var array $pages
	 */
	private $pages;

	/**
	 * Instantiates an Admin_Menu object.
	 *
	 * @since 0.1.0
	 *
	 * @param string|Config $config Path to config or Config object.
	 */
	public function __construct( $config ) {
		$this->config = is_string( $config ) ? new Config( $config ) : $config;
		$this->pages  = $this->process_config( $this->config );
	}

	/**
	 * Registers functionality with WordPress.
	 *
	 * @since 0.2.0 Registered taxonomy subpages.
	 * @since 0.1.0
	 */
	public function register() {
		add_action( 'admin_menu', array( $this, 'add_page' ) );
		add_action( 'admin_menu', array( $this, 'add_taxonomy_subpages' ) );
		add_action( 'admin_menu', array( $this, 'add_subpages' ) );
		add_filter( 'submenu_file', array( $this, 'correct_submenu_highlight' ) );
		add_filter( 'submenu_file', array( $this, 'remove_submenu_pages' ) );
		add_filter( 'admin_body_class', array( $this, 'add_admin_body_class' ) );
	}

	/**
	 * Adds taxonomy subpages.
	 *
	 * Since all plugin menu pages exist in one menu, the typical approach to
	 * displaying the taxonomy in the admin menu does not work when registering
	 * the taxonomy. Instead, the global `$submenu` is directly manipulated.
	 *
	 * @since 0.2.0
	 */
	public function add_taxonomy_subpages() {
		global $submenu;

		$parent_slug = 'wpbr';
		$menu_slug   = 'edit-tags.php?taxonomy=wpbr_review_tag';

		// Add taxonomy subpages to the slug used when adding the top-level page.
        if ( isset($submenu[ $parent_slug ]) ) {
            array_push(
                $submenu[ $parent_slug ],
                array(
                    __( 'Review Tags', 'wp-business-reviews' ),
                    'manage_options',
                    $menu_slug,
                )
            );
        }
	}

	/**
	 * Ensures the correct submenu page is highlighted.
	 *
	 * Due to the unconventional approach necessary to keep all submenu pages
	 * under a single top-level parent, some pages need help in order to be
	 * highlighted correctly.
	 *
	 * @since 0.2.0
	 * @see Admin_Menu::add_taxonomy_subpages() To understand how taxonomy
	 *                                          subpages are added.
	 *
	 * @param string $submenu_file The submenu file.
	 * @return string The unaltered submenu file.
	 */
	public function correct_submenu_highlight( $submenu_file ) {
		global $parent_file;

		// Set the parent file to ensure the correct submenu is expanded.
		if ( $submenu_file === 'edit-tags.php?taxonomy=wpbr_review_tag' ) {
			$parent_file = 'wpbr';
		}

		return $submenu_file;
	}

	/**
	 * Removes submenu pages that should not be visible in the sidebar.
	 *
	 * @since 0.1.0
	 *
	 * @param string $submenu_file The submenu file.
	 * @return string The unaltered submenu file.
	 */
	public function remove_submenu_pages( $submenu_file ) {
		global $plugin_page;

		$hidden_pages = array(
			'wpbr-builder',
		);

		// Select the submenu item to highlight instead.
		if ( $plugin_page && in_array( $plugin_page, $hidden_pages ) ) {
			$submenu_file = 'edit.php?post_type=wpbr_collection';
		}

		// Hide the submenu.
		foreach ( $hidden_pages as $page ) {
			remove_submenu_page( 'wpbr', $page );
		}

		return $submenu_file;
	}

	/**
	 * Converts config to array of page objects.
	 *
	 * @since  0.1.0
	 *
	 * @param Config $config Admin pages config.
	 * @return array Array of admin page objects.
	 */
	private function process_config( Config $config ) {
		if ( empty( $config ) ) {
			return array();
		}

		$pages = array();

		foreach ( $config as $page ) {
			// Create new admin page based on the config item.
			$page_object = new Admin_Page(
				$page['page_parent'],
				$page['page_title'],
				$page['menu_title'],
				$page['capability'],
				$page['menu_slug']
			);

			// Add admin page object to pages array.
			$pages[ $page['menu_slug'] ] = $page_object;
		}

		return $pages;
	}

	/**
	 * Add the top-level admin page.
	 *
	 * @since 0.1.0
	 */
	public function add_page() {
		add_menu_page(
			__( 'WP Business Reviews', 'wp-business-reviews' ),
			__( 'Reviews', 'wp-business-reviews' ),
			'edit_posts',
			'wpbr',
			'',
			WPBR_ASSETS_URL . 'images/wpbr-menu-icon-white.png',
			25
		);
	}

	/**
	 * Add one or more admin subpages.
	 *
	 * @since 0.1.0
	 */
	public function add_subpages() {
		foreach ( $this->pages as $page ) {
			$page->add_page();
		}
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
		$current_screen = get_current_screen();

		if ( empty ( $current_screen ) ) {
			return;
		}

		if ( false !== strpos( $current_screen->id, 'wpbr' ) ) {
			// Leave space on both sides so other plugins do not conflict.
			$classes .= ' wpbr-admin ';
		}

		return $classes;
	}
}
