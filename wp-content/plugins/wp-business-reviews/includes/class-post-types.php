<?php
/**
 * Defines the Post_Types class
 *
 * @package WP_Business_Reviews\Includes
 * @since   0.1.0
 */

namespace WP_Business_Reviews\Includes;

/**
 * Registers custom post types and taxonomies.
 *
 * @since 0.1.0
 */
class Post_Types {

	/**
	 * Registers functionality with WordPress hooks.
	 *
	 * @since 0.1.0
	 */
	public function register() {
		add_action( 'init', array( $this, 'register_taxonomies' ) );
		add_action( 'init', array( $this, 'register_post_types' ) );
		add_filter( 'gutenberg_can_edit_post_type', array( $this, 'disable_gutenberg' ), 10, 2 );
	}

	/**
	 * Registers all custom post types.
	 *
	 * @since 0.1.0
	 */
	public function register_post_types() {
		$this->register_collection_post_type();
		$this->register_review_post_type();
		$this->register_review_source_post_type();
	}

	/**
	 * Registers all custom taxonomies.
	 *
	 * @since 0.1.0
	 */
	public function register_taxonomies() {
		$this->register_platform_taxonomy();
		$this->register_rating_taxonomy();
		$this->register_attribute_taxonomy();
		$this->register_review_tag_taxonomy();
	}

	/**
	 * Registers the wpbr_collection post type.
	 *
	 * @since 0.1.0
	 */
	public function register_collection_post_type() {
		$labels = array(
			'name'                  => _x( 'Collections', 'Post Type General Name', 'wp-business-reviews' ),
			'singular_name'         => _x( 'Collection', 'Post Type Singular Name', 'wp-business-reviews' ),
			'menu_name'             => __( 'Collections', 'wp-business-reviews' ),
			'name_admin_bar'        => __( 'Collection', 'wp-business-reviews' ),
			'archives'              => __( 'Collection Archives', 'wp-business-reviews' ),
			'attributes'            => __( 'Collection Attributes', 'wp-business-reviews' ),
			'parent_item_colon'     => __( 'Parent Collection:', 'wp-business-reviews' ),
			'all_items'             => __( 'Collections', 'wp-business-reviews' ),
			'add_new_item'          => __( 'Add New Collection', 'wp-business-reviews' ),
			'add_new'               => __( 'Add Collection', 'wp-business-reviews' ),
			'new_item'              => __( 'New Collection', 'wp-business-reviews' ),
			'edit_item'             => __( 'Edit Collection', 'wp-business-reviews' ),
			'update_item'           => __( 'Update Collection', 'wp-business-reviews' ),
			'view_item'             => __( 'View Collection', 'wp-business-reviews' ),
			'view_items'            => __( 'View Collections', 'wp-business-reviews' ),
			'search_items'          => __( 'Search Collections', 'wp-business-reviews' ),
			'not_found'             => __( 'Not found', 'wp-business-reviews' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'wp-business-reviews' ),
			'featured_image'        => __( 'Featured Image', 'wp-business-reviews' ),
			'set_featured_image'    => __( 'Set featured image', 'wp-business-reviews' ),
			'remove_featured_image' => __( 'Remove featured image', 'wp-business-reviews' ),
			'use_featured_image'    => __( 'Use as featured image', 'wp-business-reviews' ),
			'insert_into_item'      => __( 'Insert into item', 'wp-business-reviews' ),
			'uploaded_to_this_item' => __( 'Uploaded to this item', 'wp-business-reviews' ),
			'items_list'            => __( 'Collections list', 'wp-business-reviews' ),
			'items_list_navigation' => __( 'Collections list navigation', 'wp-business-reviews' ),
			'filter_items_list'     => __( 'Filter items list', 'wp-business-reviews' ),
		);

		$args = array(
			'label'               => __( 'Collection', 'wp-business-reviews' ),
			'labels'              => $labels,
			'supports'            => array( 'title' ),
			'taxonomies'          => array(),
			'hierarchical'        => false,
			'public'              => false,
			'show_in_rest'        => current_user_can('edit_posts'),
			'show_ui'             => true,
			'show_in_menu'        => 'wpbr',
			'show_in_admin_bar'   => false,
			'show_in_nav_menus'   => false,
			'can_export'          => true,
			'has_archive'         => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => false,
			'capabilities'        => array(
				'create_posts' => 'do_not_allow', // Do not allow users to create new posts.
			),
			'map_meta_cap'        => true, // Allow users to still edit and delete posts.
		);

		register_post_type( 'wpbr_collection', $args );
	}

	/**
	 * Registers the wpbr_review post type.
	 *
	 * @since 0.1.0
	 */
	public function register_review_post_type() {
		$labels = array(
			'name'                  => _x( 'Single Reviews', 'Post Type General Name', 'wp-business-reviews' ),
			'singular_name'         => _x( 'Review', 'Post Type Singular Name', 'wp-business-reviews' ),
			'menu_name'             => __( 'Reviews', 'wp-business-reviews' ),
			'name_admin_bar'        => __( 'Review', 'wp-business-reviews' ),
			'archives'              => __( 'Review Archives', 'wp-business-reviews' ),
			'attributes'            => __( 'Review Attributes', 'wp-business-reviews' ),
			'parent_item_colon'     => __( 'Parent Review:', 'wp-business-reviews' ),
			'all_items'             => __( 'Single Reviews', 'wp-business-reviews' ),
			'add_new_item'          => __( 'Add New Review', 'wp-business-reviews' ),
			'add_new'               => __( 'Add Review', 'wp-business-reviews' ),
			'new_item'              => __( 'New Review', 'wp-business-reviews' ),
			'edit_item'             => __( 'Edit Review', 'wp-business-reviews' ),
			'update_item'           => __( 'Update Review', 'wp-business-reviews' ),
			'view_item'             => __( 'View Review', 'wp-business-reviews' ),
			'view_items'            => __( 'View Reviews', 'wp-business-reviews' ),
			'search_items'          => __( 'Search Reviews', 'wp-business-reviews' ),
			'not_found'             => __( 'Not found', 'wp-business-reviews' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'wp-business-reviews' ),
			'featured_image'        => __( 'Reviewer Image', 'wp-business-reviews' ),
			'set_featured_image'    => __( 'Set Reviewer Image', 'wp-business-reviews' ),
			'remove_featured_image' => __( 'Remove reviewer\'s image', 'wp-business-reviews' ),
			'use_featured_image'    => __( 'Use as reviewer\'s image', 'wp-business-reviews' ),
			'insert_into_item'      => __( 'Insert into item', 'wp-business-reviews' ),
			'uploaded_to_this_item' => __( 'Uploaded to this item', 'wp-business-reviews' ),
			'items_list'            => __( 'Reviews list', 'wp-business-reviews' ),
			'items_list_navigation' => __( 'Reviews list navigation', 'wp-business-reviews' ),
			'filter_items_list'     => __( 'Filter items list', 'wp-business-reviews' ),
		);

		$rewrite = array(
			'slug' => 'reviews',
		);

		$args = array(
			'label'               => __( 'Review', 'wp-business-reviews' ),
			'labels'              => $labels,
			'supports'            => array( 'title' ),
			'taxonomies'          => array(),
			'public'              => false,
			'show_in_rest'        => current_user_can('edit_posts'),
			'show_ui'             => true,
			'show_in_menu'        => 'wpbr',
			'show_in_admin_bar'   => true,
			'show_in_nav_menus'   => false,
			'can_export'          => true,
			'rewrite'             => $rewrite,
			'capability_type'     => 'post',
			'map_meta_cap'        => true, // Allow users to still edit and delete posts.
		);

		register_post_type( 'wpbr_review', $args );
	}

	/**
	 * Registers the wpbr_review_source post type.
	 *
	 * @since 0.1.0
	 */
	public function register_review_source_post_type() {
		$labels = array(
			'name'                  => _x( 'Review Sources', 'Post Type General Name', 'wp-business-reviews' ),
			'singular_name'         => _x( 'Review Source', 'Post Type Singular Name', 'wp-business-reviews' ),
			'menu_name'             => __( 'Review Sources', 'wp-business-reviews' ),
			'name_admin_bar'        => __( 'Review Source', 'wp-business-reviews' ),
			'archives'              => __( 'Review Source Archives', 'wp-business-reviews' ),
			'attributes'            => __( 'Review Source Attributes', 'wp-business-reviews' ),
			'parent_item_colon'     => __( 'Parent Review Source:', 'wp-business-reviews' ),
			'all_items'             => __( 'Review Sources', 'wp-business-reviews' ),
			'add_new_item'          => __( 'Add New Review Source', 'wp-business-reviews' ),
			'add_new'               => __( 'Add Review Source', 'wp-business-reviews' ),
			'new_item'              => __( 'New Review Source', 'wp-business-reviews' ),
			'edit_item'             => __( 'Edit Review Source', 'wp-business-reviews' ),
			'update_item'           => __( 'Update Review Source', 'wp-business-reviews' ),
			'view_item'             => __( 'View Review Source', 'wp-business-reviews' ),
			'view_items'            => __( 'View Review Sources', 'wp-business-reviews' ),
			'search_items'          => __( 'Search Review Sources', 'wp-business-reviews' ),
			'not_found'             => __( 'Not found', 'wp-business-reviews' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'wp-business-reviews' ),
			'featured_image'        => __( 'Featured Image', 'wp-business-reviews' ),
			'set_featured_image'    => __( 'Set featured image', 'wp-business-reviews' ),
			'remove_featured_image' => __( 'Remove featured image', 'wp-business-reviews' ),
			'use_featured_image'    => __( 'Use as featured image', 'wp-business-reviews' ),
			'insert_into_item'      => __( 'Insert into item', 'wp-business-reviews' ),
			'uploaded_to_this_item' => __( 'Uploaded to this item', 'wp-business-reviews' ),
			'items_list'            => __( 'Review Sources list', 'wp-business-reviews' ),
			'items_list_navigation' => __( 'Review Sources list navigation', 'wp-business-reviews' ),
			'filter_items_list'     => __( 'Filter items list', 'wp-business-reviews' ),
		);

		$args = array(
			'label'               => __( 'Review Source', 'wp-business-reviews' ),
			'labels'              => $labels,
			'supports'            => array( 'title' ),
			'taxonomies'          => array(),
			'hierarchical'        => false,
			'public'              => false,
			'show_in_rest'        => false,
			'show_ui'             => false,
			'show_in_admin_bar'   => false,
			'show_in_nav_menus'   => false,
			'can_export'          => true,
			'capabilities'        => array(
				'create_posts' => 'do_not_allow', // Do not allow users to create new posts.
			),
			'map_meta_cap'        => true, // Allow users to still edit and delete posts.
		);

		register_post_type( 'wpbr_review_source', $args );
	}

	/**
	 * Registers the wpbr_platform taxonomy.
	 *
	 * @since 0.1.0
	 */
	public function register_platform_taxonomy() {
		$labels = array(
			'name'          => _x( 'Platforms', 'Taxonomy General Name', 'wp-business-reviews' ),
			'singular_name' => _x( 'Platform', 'Taxonomy Singular Name', 'wp-business-reviews' ),
			'all_items'     => __( 'All Platforms', 'wp-business-reviews' ),
		);

		$args = array(
			'labels'      => $labels,
			'meta_box_cb' => false,
			'public'      => false,
			'show_ui'     => true,
		);

		register_taxonomy(
			'wpbr_platform',
			array(
				'wpbr_review',
				'wpbr_review_source',
				'wpbr_collection',
			),
			$args
		);
	}

	/**
	 * Registers the wpbr_rating taxonomy.
	 *
	 * @since 0.1.0
	 */
	public function register_rating_taxonomy() {
		$args = array(
			'public' => false,
		);

		register_taxonomy( 'wpbr_rating', 'wpbr_review', $args );
	}

	/**
	 * Registers the wpbr_platform taxonomy.
	 *
	 * @since 0.1.0
	 */
	public function register_attribute_taxonomy() {
		$args = array(
			'public' => false,
		);

		register_taxonomy( 'wpbr_attribute', 'wpbr_review', $args);
	}

	/**
	 * Registers the wpbr_review_tag taxonomy.
	 *
	 * @since 0.2.0
	 */
	public function register_review_tag_taxonomy() {
		$labels = array(
			'name'                       => _x( 'Review Tags', 'Taxonomy General Name', 'wp-business-reviews' ),
			'singular_name'              => _x( 'Review Tag', 'Taxonomy Singular Name', 'wp-business-reviews' ),
			'menu_name'                  => __( 'Review Tags', 'wp-business-reviews' ),
			'all_items'                  => __( 'All Review Tags', 'wp-business-reviews' ),
			'parent_item'                => __( 'Parent Review Tag', 'wp-business-reviews' ),
			'parent_item_colon'          => __( 'Parent Review Tag:', 'wp-business-reviews' ),
			'new_item_name'              => __( 'New Tag Review Name', 'wp-business-reviews' ),
			'add_new_item'               => __( 'Add New Review Tag', 'wp-business-reviews' ),
			'edit_item'                  => __( 'Edit Review Tag', 'wp-business-reviews' ),
			'update_item'                => __( 'Update Review Tag', 'wp-business-reviews' ),
			'view_item'                  => __( 'View Review Tag', 'wp-business-reviews' ),
			'separate_items_with_commas' => __( 'Separate items with commas', 'wp-business-reviews' ),
			'add_or_remove_items'        => __( 'Add or remove items', 'wp-business-reviews' ),
			'choose_from_most_used'      => __( 'Choose from the most used', 'wp-business-reviews' ),
			'popular_items'              => __( 'Popular Review Tags', 'wp-business-reviews' ),
			'search_items'               => __( 'Search Review Tags', 'wp-business-reviews' ),
			'not_found'                  => __( 'Not Found', 'wp-business-reviews' ),
			'no_terms'                   => __( 'No items', 'wp-business-reviews' ),
			'items_list'                 => __( 'Review Tags list', 'wp-business-reviews' ),
			'items_list_navigation'      => __( 'Review Tags list navigation', 'wp-business-reviews' ),
		);

		$args = array(
			'labels' => $labels,
			'public' => false,
			'show_ui' => true,
		);

		register_taxonomy( 'wpbr_review_tag', 'wpbr_review', $args);
	}

	/**
	 * Disables Gutenberg for reviews.
	 *
	 * @param bool $is_enabled  Whether Gutenberg is enabled for post type.
	 * @param string $post_type Post type.
	 * @return bool Whether Gutenberg is enabled for post type.
	 */
	public function disable_gutenberg( $is_enabled, $post_type ) {
		if ( 'wpbr_review' === $post_type ) {
			return false;
		}

		return $is_enabled;
	}
}
