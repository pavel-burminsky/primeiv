<?php
/**
 * Defines the Facebook_Page_Manager class
 *
 * @link https://wpbusinessreviews.com
 *
 * @package WP_Business_Reviews\Includes
 * @since 0.1.0
 */

namespace WP_Business_Reviews\Includes;

use WP_Business_Reviews\Includes\Serializer\Option_Serializer;
use WP_Business_Reviews\Includes\Request\Facebook_Request;
use WP_Business_Reviews\Includes\Admin\Admin_Notices;

/**
 * Manages tokens and requests related to Facebook pages.
 *
 * In order to retrieve reviews from Facebook pages, a user access token must
 * first be received from the WP Business Reviews Server plugin. Once received,
 * the user access token is used to request page access tokens, which are then
 * saved to the WordPress database so that reviews may be requested.
 *
 * @since 0.1.0
 *
 * @see WP_Business_Reviews/Includes/Request/Facebook_Request
 */
class Facebook_Page_Manager {
	/**
	 * Array of Facebook pages and tokens.
	 *
	 * @since 0.1.0
	 * @var array $pages
	 */
	private $pages;

	/**
	 * Settings serializer.
	 *
	 * @since 0.1.0
	 * @var Option_Serializer $serializer
	 */
	private $serializer;

	/**
	 * Facebook request.
	 *
	 * @since 0.1.0
	 * @var Facebook_Request $request
	 */
	private $request;

	/**
	 * Instantiates the Facebook_Page_Manager object.
	 *
	 * @since 0.1.0
	 *
	 * @param array             $pages      Facebook pages and tokens.
	 * @param Option_Serializer $serializer Settings saver.
	 * @param Facebook_Request  $request    Facebook request.
	 */
	public function __construct( array $pages, Option_Serializer $serializer, Facebook_Request $request ) {
		$this->pages      = $pages;
		$this->serializer = $serializer;
		$this->request    = $request;
	}

	/**
	 * Registers functionality with WordPress hooks.
	 *
	 * @since 0.1.0
	 */
	public function register() {
		add_action( 'load-reviews_page_wpbr-settings', array( $this, 'handle_user_token' ), 1 );
		add_filter( 'wpbr_facebook_pages', array( $this, 'get_review_sources' ) );
		add_action( 'admin_post_wpbr_settings_save', array( $this, 'disconnect' ), 9 );
	}

	/**
	 * Retrieves array of Facebook pages and tokens.
	 *
	 * @since 0.1.0
	 *
	 * @return array Facebook pages and tokens.
	 */
	public function get_review_sources() {
		return $this->pages;
	}

	/**
	 * Resets all Facebook settings.
	 *
	 * @since 0.1.0
	 */
	public function disconnect() {
		if (
			! isset( $_POST['wpbr_disconnect_facebook'] )
			|| ! current_user_can( 'manage_options' )
		) {
			return false;
		}

		check_admin_referer( 'wpbr_option_save', 'wpbr_option_nonce' );

		$facebook_settings = array(
			'facebook_user_token' => '',
			'facebook_platform_status' => '',
			'facebook_pages' => '',
		);

		$this->serializer->save_multiple( $facebook_settings );
		$this->serializer->redirect_to_tab();
	}

	/**
	 * Handles the user token returned from Facebook.
	 *
	 * First the user token is saved. Then it is used to generate and save the
	 * page tokens that are necessary to access page reviews.
	 *
	 * @since 1.3.1
	 */
	public function handle_user_token() {
		if (
			! isset( $_POST['wpbr_facebook_user_token'] )
			|| ! current_user_can( 'manage_options' )
		) {
			return false;
		}

		check_admin_referer( 'wpbr_facebook_token_save', 'wpbr_facebook_token_nonce' );

		$token = sanitize_text_field( wp_unslash( $_POST['wpbr_facebook_user_token'] ) );

		// Only save user token if page tokens are saved successfully.
		if ( $this->save_pages( $token ) ) {
			$this->save_user_token( $token );
		}
	}

	/**
	 * Saves Facebook user token.
	 *
	 * @since 1.3.1 Add $user_token parameter and set visibility to protected.
	 * @since 0.1.0
	 *
	 * @param string $user_token The Facebook user token.
	 * @return bool True if token saved, false otherwise.
	 */
	protected function save_user_token( $user_token) {
		if ( $this->serializer->save( 'facebook_user_token', $user_token ) ) {
			/**
			 * Fires after Facebook user token successfully saves.
			 *
			 * @since 0.2.0
			 */
			do_action( 'wpbr_after_facebook_user_token_save' );

			return true;
		}

		return false;
	}

	/**
	 * Saves Facebook page names and tokens.
	 *
	 * @since 1.3.1 Add $user_token parameter and set visibility to protected.
	 * @since 0.1.0
	 *
	 * @param string $user_token The Facebook user token.
	 * @return bool True if pages saved, false otherwise.
	 */
	protected function save_pages( $user_token ) {
		$pages = array();

		$this->request->set_token( $user_token );

		$response = $this->request->get_review_sources();

		if ( is_wp_error( $response ) ) {
			Admin_Notices::add_notice( 'facebook_page_tokens_error' );

			return false;
		}

		// Process the array of pages and pull out only the keys we need.
		if ( isset( $response['data'] ) ) {
			foreach ( $response['data'] as $page ) {
				if ( ! isset( $page['id'], $page['name'], $page['access_token'] ) ) {
					continue;
				}

				$pages[ $page['id'] ] = array(
					'name'  => $page['name'],
					'token' => $page['access_token'],
				);
			}

			return $this->serializer->save( 'facebook_pages', $pages );
		}

		return false;
	}
}
