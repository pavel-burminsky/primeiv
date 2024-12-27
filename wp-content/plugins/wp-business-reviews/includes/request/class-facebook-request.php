<?php

/**
 * Defines the Facebook_Request class
 *
 * @link https://wpbusinessreviews.com
 *
 * @package WP_Business_Reviews\Includes\Request
 * @since 0.1.0
 */

namespace WP_Business_Reviews\Includes\Request;

/**
 * Retrieves data from Facebook Graph API.
 *
 * @since 0.1.0
 */
class Facebook_Request extends Request {
	/**
	 * Platform ID.
	 *
	 * @since 0.1.0
	 * @var string $platform
	 */
	protected $platform = 'facebook';

	/**
	 * Facebook user token.
	 *
	 * @since 0.1.0
	 * @var string $user_token
	 */
	protected $user_token;

	/**
	 * Array of Facebook Pages and Page tokens.
	 *
	 * @since 0.1.0
	 * @var array $pages
	 */
	protected $pages;

	/**
	 * Instantiates the Facebook_Request object.
	 *
	 * @since 0.1.0
	 *
	 * @param string $user_token Facebook user token.
	 * @param array  $pages      Array of Facebook Pages and Page tokens.
	 */
	public function __construct( $user_token, array $pages = array() ) {
		$this->user_token = $user_token;
		$this->pages = $pages;
	}

	/**
	 * Retrieves the platform status based on a test request.
	 *
	 * @since 1.0.1
	 *
	 * @return string The platform status.
	 */
	public function get_platform_status() {
		$url = add_query_arg(
			array(
				'access_token' => $this->user_token,
			),
			'https://graph.facebook.com/v14.0/me/'
		);

		$response = $this->get( $url );

		if ( is_wp_error( $response ) || isset( $response['error'] ) ) {
			return 'disconnected';
		}

		return 'connected';
	}

	/**
	 * Retrieves review source details based on Facebook page ID.
	 *
	 * @since 0.1.0
	 *
	 * @param string $review_source_id The Facebook Page ID.
	 * @return array|WP_Error Associative array containing response or WP_Error
	 *                        if response structure is invalid.
	 */
	public function get_review_source( $review_source_id ) {
		if ( ! isset( $this->pages[ $review_source_id ] ) ) {
			return new \WP_Error(
				'wpbr_missing_facebook_page_token',
				sprintf(
					__( 'Facebook page token could not be found. <a href="%s">Reconnecting to Facebook</a> may fix the issue.', 'wp-business-reviews' ),
					esc_url( admin_url( 'admin.php?page=wpbr-settings&wpbr_tab=platforms&wpbr_subtab=facebook' ) )
				)
			);
		}

		$page_token = $this->pages[ $review_source_id ]['token'];

		$url = add_query_arg(
			array(
				'fields'       => 'name,link,overall_star_rating,rating_count,cover,phone,single_line_address,location',
				'access_token' => $page_token,
			),
			"https://graph.facebook.com/v14.0/{$review_source_id}"
		);

		$response = $this->get( $url );

		return $response;
	}

	/**
	 * Retrieves Facebook pages from the Facebook Grap.
	 *
	 * @since 0.1.0
	 *
	 * @return array|WP_Error Associative array containing response or WP_Error
	 *                        if response structure is invalid.
	 */
	public function get_review_sources() {
		$url = add_query_arg(
			array(
				'access_token' => $this->user_token,
			),
			'https://graph.facebook.com/v14.0/me/accounts/'
		);

		$response = $this->get( $url );

		return $response;
	}

	/**
	 * Retrieves reviews based on Facebook Page ID.
	 *
	 * @since 0.1.0
	 *
	 * @param string $review_source_id The Facebook Page ID.
	 * @return array|WP_Error Associative array containing response or WP_Error
	 *                        if response structure is invalid.
	 */
	public function get_reviews( $review_source_id ) {
		if ( ! isset( $this->pages[ $review_source_id ] ) ) {
			return new \WP_Error(
				'wpbr_missing_facebook_page_token',
				sprintf(
					__( 'Facebook page token could not be found. <a href="%s">Reconnecting to Facebook</a> may fix the issue.', 'wp-business-reviews' ),
					esc_url( admin_url( 'admin.php?page=wpbr-settings&wpbr_tab=platforms&wpbr_subtab=facebook' ) )
				)
			);
		}

		$page_token = $this->pages[ $review_source_id ]['token'];

		/**
		 * Filters the dimensions of Facebook reviewer picture.
		 *
		 * @since 0.2.0
		 *
		 * @param array $dimensions {
		 *     Width and height of Facebook reviewer picture.
		 *
		 *     @type int $width  Width of image in pixels.
		 *     @type int $height Height of image in pixels.
		 * }
		 */
		$dimensions = apply_filters( 'wpbr_facebook_picture_dimensions', array( 120, 120 ) );

		$url = add_query_arg(
			array(
				'limit'        => 24,
				'fields'       => "reviewer{id,name,picture.width({$dimensions[0]}).height({$dimensions[1]})},created_time,rating,recommendation_type,review_text,open_graph_story",
				'access_token' => $page_token,
			),
			"https://graph.facebook.com/v14.0/{$review_source_id}/ratings"
		);

		// Request data from remote API.
		$response = $this->get( $url );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		if ( isset( $response['error']['message'] ) ) {
			$error  = sanitize_text_field( $response['error']['message'] );
			$remedy = sprintf(
				__( '<a href="%s">Reconnecting to Facebook</a> may fix the issue.', 'wp-business-reviews' ),
				esc_url( admin_url( 'admin.php?page=wpbr-settings&wpbr_tab=platforms&wpbr_subtab=facebook' ) )
			);
			$message = $error . ' ' . $remedy;

			return new \WP_Error( 'wpbr_facebook_oauthexception', $message );
		}

		return $response['data'];
	}

	/**
	 * Sets the Facebook user access token.
	 *
	 * @since 0.1.0
	 *
	 * @param string $user_token User access token.
	 */
	public function set_token( $user_token ) {
		$this->user_token = $user_token;
	}

	/**
	 * Determines if a token has been set.
	 *
	 * @since 0.1.0
	 *
	 * @return boolean True if token is set, false otherwise.
	 */
	public function has_token() {
		return ! empty( $this->user_token );
	}
}
