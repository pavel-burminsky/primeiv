<?php
/**
 * Defines the Collection_Serializer class
 *
 * @link https://wpbusinessreviews.com
 *
 * @package WP_Business_Reviews\Includes\Serializer
 * @since 0.1.0
 */

namespace WP_Business_Reviews\Includes\Serializer;

/**
 * Saves Collections to the database.
 *
 * @since 0.1.0
 */
class Collection_Serializer extends Post_Serializer {
	/**
	 * The post type being saved.
	 *
	 * @since 0.1.0
	 * @var string $post_type
	 */
	protected $post_type = 'wpbr_collection';

	/**
	 * Registers functionality with WordPress hooks.
	 *
	 * @since 0.1.0
	 */
	public function register() {
		add_action( 'wpbr_review_source_determine_post_id', array( $this, 'set_post_parent' ) );
		add_action( 'admin_post_wpbr_collection_save', array( $this, 'save_from_post_array' ), 30 );
		add_action( 'admin_post_wpbr_collection_trash', array( $this, 'trash' ) );
	}

	/**
	 * Prepares the post data in a ready-to-save format.
	 *
	 * @since 0.1.0
	 *
	 * @param array $raw_data Raw, unstructured post data.
	 *
	 * @return array Array of elements that make up a post.
	 */
	public function prepare_post_array( array $raw_data ) {
		$post_array = array(
			'post_type'   => $this->post_type,
			'post_status' => 'publish',
			'post_parent' => $this->post_parent,
		);

		foreach ( $raw_data as $key => $value ) {
			switch ( $key ) {
				case 'post_id':
					$post_array['ID'] = $this->clean( $value );
					break;
				case 'title':
					if ( empty( $value ) ) {
						$post_array['post_title'] = __( 'Untitled Collection', 'wp-business-reviews' );
					} else {
						$post_array['post_title'] = $this->clean( $value );
					}
					break;
				case 'platform':
					$post_array['tax_input']['wpbr_platform'] = $this->clean( $value );
					break;
				case 'post_parent':
					$post_array['post_parent'] = $this->clean( $value );
					break;
				default:
					$post_array['meta_input'][ $this->prefix . $key ] = $this->clean( $value );
					break;
			}
		}

		/*
		 * If `post_parent` is not available in the submitted `$raw_data`, then it is
		 * likely the first time this post is being saved. Check to see if `post_parent`
		 * has been set via a save hook from another serializer.
		 */
		if ( 0 === (int) $post_array['post_parent'] && 0 < $this->post_parent ) {
			$post_array['post_parent'] = $this->post_parent;
		}

		return $post_array;
	}

	/**
	 * Trashes a post.
	 *
	 * @param integer $post_id
	 * @return void
	 */
	function trash() {
		check_admin_referer( 'wpbr_collection_trash', 'wpbr_collection_nonce' );

		$post_id = 0;

		if ( ! empty( $_GET['post'] ) ) {
			$post_id = absint( $_GET['post'] );
		}

		if ( ! current_user_can( 'delete_post', $post_id ) )
			wp_die( __( 'Sorry, you are not allowed to move this item to the Trash.', 'wp-business-reviews' ) );

		if ( ! wp_trash_post( $post_id ) )
			wp_die( __( 'There was a problem moving the collection to the Trash.', 'wp-business-reviews' ) );

		$redirect = add_query_arg( array(
			'post_type'   => 'wpbr_collection',
			'wpbr_notice' => 'collection_trashed',
		), admin_url( 'edit.php' ) );

		wp_safe_redirect( $redirect );
		exit;
	}
}
