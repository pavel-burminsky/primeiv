<?php
/**
 * Defines the WooCommerce_Request class
 *
 * @link https://wpbusinessreviews.com
 *
 * @package WP_Business_Reviews\Includes\Request
 * @since 1.5.0
 */

namespace WP_Business_Reviews\Includes\Request;

/**
 * Retrieves data from WooCommerce REST API.
 *
 * @since 1.5.0
 */
class WooCommerce_Request extends Request {
	/**
	 * Platform ID.
	 *
	 * @since 1.5.0
	 * @var string $platform
	 */
	protected $platform = 'woocommerce';

	/**
	 * Retrieves the platform status based on a test request.
	 *
	 * @return string The platform status.
	 * @since 1.5.0
	 *
	 */
	public function get_platform_status() {

		if ( class_exists( 'woocommerce' ) ) {
			return 'connected';
		} else {
			return 'disconnected';
		}

	}

	/**
	 * Get's the review sources based on the selected product. WooCommerce doesn't require searching for businesses so this is largely a bypass method.
	 *
	 * @param string $product_id The product ID that we are pulling reviews from.
	 *
	 * @return array
	 *
	 * @since 1.5.0
	 *
	 */
	public function search_review_source( $product_id ) {
		return $this->get_review_source( $product_id );
	}

	/**
	 * Retrieves review source details based on the WooCommerce product ID.
	 *
	 * @param string $product_id The product ID that we are pulling reviews from.
	 *
	 * @return array|object Associative array containing response or WP_Error if response structure is invalid.
	 * @since 1.5.0
	 *
	 */
	public function get_review_source( $product_id ) {

		$product = wc_get_product( $product_id );

		return [
			'id'           => $product->get_id(),
			'name'         => $product->get_title(),
			'url'          => get_permalink( $product->get_id() ),
			'rating'       => $product->get_average_rating(),
			'review_count' => $product->get_rating_count(),
			'image_url'    => wp_get_attachment_url( $product->get_image_id() ),
		];

	}

	/**
	 * Retrieves reviews based on WooCommerce product ID.
	 *
	 * @param string $product_id The product ID that we are pulling reviews from.
	 *
	 * @return array|object Associative array containing response or WP_Error if response structure is invalid.
	 * @since 1.5.0
	 *
	 */
	public function get_reviews( $product_id ) {

		$args = [
			'post_id'     => $product_id,
			'post_type'   => 'product',
			'status'      => 'approve',
			'post_status' => 'publish',
		];

		$comments = get_comments( $args );
		$reviews  = [];

		foreach ( $comments as $comment ) {

			$review = [
				'reviewer_name'  => $comment->comment_author,
				'reviewer_image' => get_avatar_url( $comment->comment_author_email ),
				'time_created'   => $comment->comment_date_gmt,
				'review_content' => $comment->comment_content,
				'rating'         => get_comment_meta( $comment->comment_ID, 'rating', true ),
				'review_url'     => get_permalink( $product_id ) . '#comment-' . $comment->comment_ID,
			];

			$reviews[] = $review;

		}

		return $reviews;


	}

}
