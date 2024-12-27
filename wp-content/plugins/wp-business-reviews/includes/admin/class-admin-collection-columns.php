<?php
/**
 * Defines the Admin_Collection_Columns class
 *
 * @package WP_Business_Reviews\Includes\Admin
 * @since   0.1.0
 */

namespace WP_Business_Reviews\Includes\Admin;

use WP_Business_Reviews\Includes\Deserializer\Collection_Deserializer as Deserializer;
use WP_Business_Reviews\Includes\Platform_Manager;

/**
 * Customizes the WP_List_Table columns for collections.
 *
 * @since 0.1.0
 */
class Admin_Collection_Columns {
	/**
	 * Collection deserializer.
	 *
	 * @since 0.1.0
	 * @var Deserializer $deserializer
	 */
	private $deserializer;

	/**
	 * Instantiates the Admin_Collection_Columns object.
	 *
	 * @since 0.1.0
	 *
	 * @param Deserializer $deserializer Retriever of collections.
	 */
	public function __construct( Deserializer $deserializer ) {
		$this->deserializer = $deserializer;
	}

	/**
	 * Registers functionality with WordPress hooks.
	 *
	 * @since 0.1.0
	 */
	public function register() {
		add_filter( 'get_edit_post_link', array( $this, 'modify_edit_post_link' ), 10, 3 );
		add_filter( 'manage_edit-wpbr_collection_columns', array( $this, 'get_columns' ) );
		add_action( 'manage_wpbr_collection_posts_custom_column', array( $this, 'render' ), 10, 2 );
		add_filter( 'post_row_actions', array( $this, 'modify_post_row_actions' ), 10, 2 );
	}

	/**
	 * Retrieves the custom columns in the order in which they appear.
	 *
	 * @since 0.1.0
	 *
	 * @param array $columns An array of column headers. Default empty.
	 * @return array Filtered array of columns.
	 */
	public function get_columns( $columns ) {
		$columns                  =  array(
			'cb'                     => '<input type="checkbox">',
			'title'                  => __( 'Title', 'wp-business-reviews' ),
			'taxonomy-wpbr_platform' => __( 'Platform', 'wp-business-reviews' ),
			'wpbr_style'             => __( 'Style', 'wp-business-reviews' ),
			'wpbr_format'            => __( 'Format', 'wp-business-reviews' ),
			'date'                   => __( 'Date', 'wp-business-reviews' ),
			'wpbr_shortcode'         => __( 'Shortcode', 'wp-business-reviews' ),
		);

		return $columns;
	}

	/**
	 * Renders the columns.
	 *
	 * @since 1.0
	 *
	 * @param string $column_name Column name.
	 * @param int    $post_id     Unique ID of the review post.
	 */
	public function render( $column_name, $post_id ) {
		$args = array();

		if ( isset( $_GET['post_status'] ) ) {
			$post_status = sanitize_text_field( wp_unslash( $_GET['post_status'] ) );

			if ( 'trash' === $post_status ) {
				$args['post_status'] = array( 'trash' );
			}
		}

		$collection = $this->deserializer->get_collection( $post_id, $args );

		switch ( $column_name ) {
			case 'wpbr_style':
				$settings = $collection->get_settings();
				$style   = isset( $settings['style'] ) ? $settings['style']: 'unstyled';
				$styles   = array(
					'light'       => __( 'Light', 'wp-business-reviews' ),
					'dark'        => __( 'Dark', 'wp-business-reviews' ),
					'transparent' => __( 'Transparent', 'wp-business-reviews' ),
					'unstyled'    => __( 'Unstyled', 'wp-business-reviews' ),
				);

				if ( isset( $styles[ $style ] ) ) {
					echo esc_html( $styles[ $style ] );
				}
				break;

			case 'wpbr_format':
				$settings = $collection->get_settings();
				$format = isset( $settings['format'] ) ? $settings['format'] : 'unformatted';
				$formats = array(
					'review_gallery' => array(
						'name' => __( 'Gallery', 'wp-business-reviews' ),
						'icon' => 'th-large',
					),
					'review_list' => array(
						'name' => __( 'List', 'wp-business-reviews' ),
						'icon' => 'list-ul',
					),
					'review_carousel' => array(
						'name' => __( 'Carousel', 'wp-business-reviews' ),
						'icon' => 'arrows-alt-h',
					),
				);

				if ( isset( $formats[ $format ] ) ) {
					$name = $formats[ $format ]['name'];
					$icon = $formats[ $format ]['icon'];
					echo '<i class="fas wpbr-icon wpbr-fw wpbr-' . esc_attr( $icon ). '"></i> ' . esc_html( $name );
				} else {
					echo '--';
				}
				break;

			case 'wpbr_shortcode':
				$shortcode = sprintf( '[wpbr_collection id="%s"]', $collection->get_post_id() );
				printf(
					'<button type="button" class="%1$s" aria-label="%2$s" data-wpbr-shortcode="%3$s"><i class="%4$s"></i> %5$s</button>',
					'button wpbr-tooltip wpbr-tooltip--top js-wpbr-shortcode-button',
					esc_attr( $shortcode ),
					esc_attr( $shortcode ),
					'fas wpbr-icon wpbr-fw wpbr-copy',
					esc_html__( 'Copy Shortcode', 'wp-business-reviews' )
				);
				break;
		}
	}

	/**
	 * Modifies the edit post links to direct to the builder with collection loaded.
	 *
	 * @since 0.1.0
	 *
	 * @param string      $link    The edit post link for the given post.
	 * @param int|WP_Post $id      Post ID or post object.
	 * @param string      $context How to output the '&' character. Default '&amp;'.
	 * @return string The filtered edit post link.
	 */
	public function modify_edit_post_link( $link, $id, $context ) {
		$screen = get_current_screen();

		if ( empty( $screen ) || 'wpbr_collection' !== $screen->post_type ) {
			return $link;
		}

		return admin_url( 'admin.php?page=wpbr-builder&wpbr_collection_id=' . $id );
	}

	/**
	 * Modifies the row of actions in the title column.
	 *
	 * @since 0.1.0
	 *
	 * @param array   $actions An array of row action links.
	 * @param WP_Post $post    The post object.
	 * @return array An updated array of row action links.
	 */
	public function modify_post_row_actions( $actions, $post ) {
		if ( $post->post_type === "wpbr_collection" ) {
			$modified_actions = array(
				'post-id' => '<span class="wpbr-admin-column-action">ID: ' . $post->ID . '</span>',
			);
			$actions = $modified_actions + $actions;
		}

		return $actions;
	}
}
