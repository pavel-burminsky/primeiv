<?php
/**
 * Defines the Admin_Footer class
 *
 * @package WP_Business_Reviews\Includes\Admin
 * @since   0.1.0
 */

namespace WP_Business_Reviews\Includes\Admin;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Creates the admin footer for the plugin.
 *
 * @since 0.1.0
 */
class Admin_Footer {
	/**
	 * Registers functionality with WordPress hooks.
	 *
	 * @since 0.1.0
	 */
	public function register() {
		add_filter( 'admin_footer_text', array( $this, 'render' ) );
	}

	/**
	 * Renders the admin header.
	 *
	 * @since 0.1.0
	 */
	public function render() {
		$current_screen = get_current_screen();

		if ( empty ( $current_screen ) ) {
			return;
		}

		if ( false !== strpos( $current_screen->id, 'wpbr' ) ) {
			$icon = '<i class="fab wpbr-icon wpbr-fw wpbr-twitter"></i>';
			$footer = sprintf(
				esc_html__( 'Thanks for using WP Business Reviews. Follow the conversation on %sTwitter%s.', 'wp-business-reviews' ),
				'<a href="https://twitter.com/WPBizReviews" target="_blank" rel="noopener noreferrer">',
				'</a>'
			);

			return $icon . ' ' . $footer;
		}
	}
}
