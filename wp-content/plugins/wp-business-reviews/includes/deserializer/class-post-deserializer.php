<?php
/**
 * Defines the Post_Deserializer class
 *
 * @link https://wpbusinessreviews.com
 *
 * @package WP_Business_Reviews\Includes\Deserializer
 * @since 0.1.0
 */

namespace WP_Business_Reviews\Includes\Deserializer;

use WP_Business_Reviews\Includes\Review;


/**
 * Retrieves Posts from the database.
 *
 * @since 0.1.0
 */
class Post_Deserializer {
	/**
	 * The prefix prepended to post meta keys.
	 *
	 * @since 0.1.0
	 * @var string $prefix
	 */
	protected $prefix = 'wpbr_';

	/**
	 * The post type being retrieved.
	 *
	 * @since 0.1.0
	 * @var string $post_type
	 */
	protected $post_type = 'post';

	/**
	 * The WP_Query object used to query posts.
	 *
	 * @since 0.1.0
	 * @var \WP_Query $wp_query
	 */
	protected $wp_query;

	/**
	 * Instantiates the Post_Deserializer object.
	 *
	 * @since 0.1.0
	 *
	 * @param WP_Query The WP_Query object used to query posts.
	 */
	public function __construct( \WP_Query $wp_query ) {
		$this->wp_query = $wp_query;
	}

	/**
	 * Gets a single WP post.
	 *
	 * @since 0.2.0 Added `$args` as second parameter.
	 * @since 0.1.0
	 *
	 * @see WP_Query For acceptable query arguments.
	 *
	 * @param string $post_id ID of the post to retrieve.
	 * @param array  $args    Array of query arguments.
	 * @return WP_Post|false WP_Post object or false if post not found.
	 */
	public function get_post( $post_id, $args = array() ) {
		$post     = null;
		$defaults = array(
			'post_type'      => $this->post_type,
			'p'              => $post_id,
			'posts_per_page' => 1,
			'no_found_rows'  => true,
		);

		$args = wp_parse_args( $args, $defaults );
		$this->wp_query->query( $args );

		if ( ! $this->wp_query->have_posts() ) {
			return false;
		}

		return $this->wp_query->posts[0];
	}

	/**
	 * Queries posts.
	 *
	 * @since 0.1.0
	 *
	 * @param string|array $args Optional. URL query string or array of vars.
	 * @return WP_Post[]|false Array of WP_Post objects or false if no posts found.
	 */
	public function query_posts( $args = '' ) {
		$posts = array();

		/**
		 * Filters the number of posts to query.
		 *
		 * The `$post_type` parameter can be used in a condition to alter the
		 * number of posts for a specific post type.
		 *
		 * @since 1.1.0
		 *
		 * @param int    $posts_per_page Number of posts to query.
		 * @param string $post_type      The post type being queried.
		 */
		$posts_per_page = apply_filters( 'wpbr_posts_per_page', 24, $this->post_type );

		$args = wp_parse_args( $args, array(
			'post_type'      => $this->post_type,
			'posts_per_page' => $posts_per_page,
		) );

		$this->wp_query->query( $args );

		if ( ! $this->wp_query->have_posts() ) {
			return false;
		}

		return $this->wp_query->posts;
	}

	/**
	 * Retrieves a post meta key.
	 *
	 * @param int $post_id The Post ID.
	 * @param string $key  The post meta key to retrieve.
	 * @return mixed The meta value.
	 */
	public function get_meta( $post_id, $key ) {
		return get_post_meta( $post_id, "{$this->prefix}{$key}", true );
	}

	/**
	 * Gets platform ID from post terms.
	 *
	 * @since 0.1.0
	 *
	 * @param WP_Post $post Post object.
	 * @return string Platform ID.
	 */
	protected function get_platform( $post ) {
		$term_list = wp_get_post_terms(
			$post->ID,
			'wpbr_platform',
			array(
				'fields' => 'slugs',
			)
		);

		return isset( $term_list[0] ) ? $term_list[0] : '';
	}
}
