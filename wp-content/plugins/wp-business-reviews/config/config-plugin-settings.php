<?php
/**
 * Defines the settings config.
 *
 * @package WP_Business_Reviews\Config
 * @since   0.1.0
 */

namespace WP_Business_Reviews\Config;

/**
 * Filters the active platform options provided by the settings config.
 *
 * @since 0.1.0
 *
 * @param array $platforms Array of platform slugs.
 */
$platforms = apply_filters( 'wpbr_settings_platforms', array() );

/**
 * Filters the default platforms provided by the settings config.
 *
 * @since 0.1.0
 *
 * @param array $platforms Array of default platform slugs.
 */
$default_platforms = apply_filters( 'wpbr_settings_default_platforms', array() );

$config = array(
	'platforms' => array(
		'name'     => __( 'Platforms', 'wp-business-reviews' ),
		'sections' => array(
			'platforms' => array(
				'name'        => __( 'Platforms', 'wp-business-reviews' ),
				'description' => sprintf(
					/* translators: link to documentation */
					__( 'Need help? Get started with our %1$svideo tutorials%2$s.', 'wp-business-reviews' ),
					'<a href="' . admin_url( 'admin.php?page=wpbr-settings&wpbr_tab=help' ) . '">',
					'</a>'
				),
				'heading'     => __( 'Platform Settings', 'wp-business-reviews' ),
				'icon'        => 'cogs',
				'fields'      => array(
					'active_platforms' => array(
						'name'          => __( 'Active Review Platforms', 'wp-business-reviews' ),
						'type'          => 'checkboxes',
						'description'   => __( 'Define which review platforms are visible throughout the plugin. Only the selected platforms appear in Settings and Collections.', 'wp-business-reviews' ),
						'default'       => $default_platforms,
						'options'       => $platforms,
						'wrapper_class' => 'wpbr-field--spacious',
					),
					'save_platforms' => array(
						'type'          => 'save',
						'wrapper_class' => 'wpbr-field--spacious',
					),
				),
			),
			'google_places' => array(
				'name'        => __( 'Google', 'wp-business-reviews' ),
				'heading'     => __( 'Google Review Settings', 'wp-business-reviews' ),
				'description' => sprintf(
					/* translators: link to documentation */
					__( 'Need help? View a tutorial on %1$sConnecting to Google%2$s.', 'wp-business-reviews' ),
					'<a href="' . admin_url( 'admin.php?page=wpbr-settings&wpbr_tab=help&wpbr_subtab=video-google-places' ) . '">',
					'</a>'
				),
				'icon'        => 'status',
				'fields'      => array(
					'google_places_platform_status' => array(
						'name'     => __( 'Platform Status', 'wp-business-reviews' ),
						'type'     => 'platform_status',
						'default'  => 'disconnected',
						'platform' => 'google_places',
						'wrapper_class' => 'wpbr-field--spacious',
					),
					'google_places_api_key' => array(
						'name'         => __( 'Google Places API Key', 'wp-business-reviews' ),
						'type'         => 'text',
						'description'  => sprintf(
							/* translators: link to documentation */
							__( 'Define the API key required to retrieve Google reviews. To get an API key, %1$svisit Google%2$s and create a new key with Places and billing enabled.', 'wp-business-reviews' ),
							'<a href="https://cloud.google.com/maps-platform/?apis=places" target="_blank" rel="noopener noreferrer">',
							'</a>'
						),
						'wrapper_class' => 'wpbr-field--spacious',
					),
					'save_google_places' => array(
						'type'    => 'save',
						'wrapper_class' => 'wpbr-field--spacious',
					),
				),
			),
			'facebook' => array(
				'name'        => __( 'Facebook', 'wp-business-reviews' ),
				'heading'     => __( 'Facebook Review Settings', 'wp-business-reviews' ),
				'description' => sprintf(
					/* translators: link to documentation */
					__( 'Need help? View a tutorial on %1$sConnecting to Facebook%2$s.', 'wp-business-reviews' ),
					'<a href="' . admin_url( 'admin.php?page=wpbr-settings&wpbr_tab=help&wpbr_subtab=video-facebook' ) . '">',
					'</a>'
				),
				'icon'        => 'status',
				'fields'      => array(
					'facebook_platform_status' => array(
						'name'     => __( 'Platform Status', 'wp-business-reviews' ),
						'type'     => 'platform_status',
						'default'  => 'disconnected',
						'platform' => 'facebook',
						'wrapper_class' => 'wpbr-field--spacious',
					),
					'facebook_user_token' => array(
						'name' => __( 'Facebook User Token', 'wp-business-reviews' ),
						'type' => 'internal',
					),
					'facebook_pages' => array(
						'name'        => __( 'Facebook Pages', 'wp-business-reviews' ),
						'type'        => 'facebook_pages',
						'description' => __( 'Connect to Facebook with a role of Admin, Editor, Moderator, Advertiser, or Analyst and grant the "Manage Pages" permission in order to display reviews from that Page.', 'wp-business-reviews' ),
						'wrapper_class' => 'wpbr-field--spacious',
					),
				),
			),
			'trust_pilot' => array(
				'name'        => __( 'Trustpilot', 'wp-business-reviews' ),
				'heading'     => __( 'Trustpilot Review Settings', 'wp-business-reviews' ),
				'icon'        => 'status',
				'fields'      => array(
					'trust_pilot_platform_status' => array(
						'name'     => __( 'Platform Status', 'wp-business-reviews' ),
						'type'     => 'platform_status',
						'default'  => 'disconnected',
						'platform' => 'trust_pilot',
						'wrapper_class' => 'wpbr-field--spacious',
					),
					'trust_pilot_api_key' => array(
						'name'         => __( 'Trustpilot API Key', 'wp-business-reviews' ),
						'type'         => 'text',
						'description'  => __( 'Define the API Key required to retrieve Trustpilot reviews.', 'wp-business-reviews' ),
						'wrapper_class' => 'wpbr-field--spacious',
					),
					'save_trust_pilot' => array(
						'type'    => 'save',
						'wrapper_class' => 'wpbr-field--spacious',
					),
				),
			),
			'woocommerce' => array(
				'name'        => __( 'WooCommerce', 'wp-business-reviews' ),
				'heading'     => __( 'WooCommerce Review Settings', 'wp-business-reviews' ),
				'icon'        => 'status',
				'fields'      => array(
					'woocommerce_platform_status' => array(
						'name'     => __( 'Platform Status', 'wp-business-reviews' ),
						'type'     => 'platform_status',
						'default'  => 'disconnected',
						'platform' => 'woocommerce',
						'wrapper_class' => 'wpbr-field--spacious',
					),
					'woocommerce_internal_field' => array(
						'name' => __( '', 'wp-business-reviews' ),
						'type'         => 'hidden',
						'description'  => __( 'WooCommerce needs to be installed and activated in order to connect to WP Business Reviews. Once it is activated please click "Save Changes" below to recheck the connection status.', 'wp-business-reviews' ),
						'wrapper_class' => 'wpbr-field--spacious',
					),
					'save_woocommerce' => array(
						'type'    => 'save',
						'wrapper_class' => 'wpbr-field--spacious',
					),
				),
			),
			'yelp' => array(
				'name'        => __( 'Yelp', 'wp-business-reviews' ),
				'heading'     => __( 'Yelp Review Settings', 'wp-business-reviews' ),
				'description' => sprintf(
					/* translators: link to documentation */
					__( 'Need help? View a tutorial on %1$sConnecting to Yelp%2$s.', 'wp-business-reviews' ),
					'<a href="' . admin_url( 'admin.php?page=wpbr-settings&wpbr_tab=help&wpbr_subtab=video-yelp' ) . '">',
					'</a>'
				),
				'icon'        => 'status',
				'fields'      => array(
					'yelp_platform_status' => array(
						'name'     => __( 'Platform Status', 'wp-business-reviews' ),
						'type'     => 'platform_status',

						'default'  => 'disconnected',
						'platform' => 'yelp',
						'wrapper_class' => 'wpbr-field--spacious',
					),
					'yelp_api_key' => array(
						'name'         => __( 'Yelp API Key', 'wp-business-reviews' ),
						'type'         => 'text',
						'description'  => sprintf(
							/* translators: link to documentation */
							__( 'Define the API Key required to retrieve Yelp reviews. To get an API Key, %1$screate a Yelp App%2$s and then copy the API key provided on the \'Manage App\' page.', 'wp-business-reviews' ),
							'<a href="https://www.yelp.com/developers/v3/manage_app" target="_blank" rel="noopener noreferrer">',
							'</a>'
						),
						'wrapper_class' => 'wpbr-field--spacious',
					),
					'save_yelp' => array(
						'type'    => 'save',
						'wrapper_class' => 'wpbr-field--spacious',
					),
				)
			),
			'yp' => array(
				'name'        => __( 'YP', 'wp-business-reviews' ),
				'heading'     => __( 'YP Review Settings', 'wp-business-reviews' ),
				'icon'        => 'status',
				'fields'      => array(
					'yp_platform_status' => array(
						'name'     => __( 'Platform Status', 'wp-business-reviews' ),
						'type'     => 'platform_status',
						'default'  => 'disconnected',
						'platform' => 'yp',
						'wrapper_class' => 'wpbr-field--spacious',
					),
					'yp_api_key'         => array(
						'name'         => __( 'YP API Key', 'wp-business-reviews' ),
						'type'         => 'text',
						'description' => __( 'Define the API Key required to retrieve YP reviews. While the Yellow Pages API has discontinued registration of new API keys, existing API keys may still be used to access reviews.', 'wp-business-reviews' ),
						'wrapper_class' => 'wpbr-field--spacious',
					),
					'save_yp'            => array(
						'id'      => 'save_yp',
						'type'    => 'save',
						'wrapper_class' => 'wpbr-field--spacious',
					),
				),
			),
			'zomato' => array(
				'name'        => __( 'Zomato', 'wp-business-reviews' ),
				'heading'     => __( 'Zomato Review Settings', 'wp-business-reviews' ),
				'description' => sprintf(
					/* translators: link to documentation */
					__( 'Need help? View a tutorial on %1$sConnecting to Zomato%2$s.', 'wp-business-reviews' ),
					'<a href="' . admin_url( 'admin.php?page=wpbr-settings&wpbr_tab=help&wpbr_subtab=video-zomato' ) . '">',
					'</a>'
				),
				'icon'        => 'status',
				'fields'      => array(
					'zomato_platform_status' => array(
						'name'     => __( 'Platform Status', 'wp-business-reviews' ),
						'type'     => 'platform_status',
						'default'  => 'disconnected',
						'platform' => 'zomato',
						'wrapper_class' => 'wpbr-field--spacious',
					),
					'zomato_api_key' => array(
						'name'         => __( 'Zomato API Key', 'wp-business-reviews' ),
						'type'         => 'text',
						'description'  => sprintf(
							/* translators: link to documentation */
							__( 'Define the API Key required to retrieve Zomato reviews. To get an API key, %1$svisit Zomato for Developers%2$s and generate an API key.', 'wp-business-reviews' ),
							'<a href="https://developers.zomato.com/api" target="_blank" rel="noopener noreferrer">',
							'</a>'
						),
						'wrapper_class' => 'wpbr-field--spacious',
					),
					'save_zomato' => array(
						'type'    => 'save',
						'wrapper_class' => 'wpbr-field--spacious',
					),
				)
			),
		),
	),
	'advanced' => array(
		'name'     => __( 'Advanced', 'wp-business-reviews' ),
		'sections' => array(
			'advanced' => array(
				'name'        => __( 'Advanced', 'wp-business-reviews' ),
				'heading'     => __( 'Advanced Settings', 'wp-business-reviews' ),
				'fields'      => array(
					'auto_refresh' => array(
						'name'          => __( 'Automatic Refresh', 'wp-business-reviews' ),
						'type'          => 'select',
						'description'   => __( 'Choose how often to run the background process that checks for new reviews. If disabled, new reviews only get added to collections when manually refreshed.', 'wp-business-reviews' ),
						'default'       => 'weekly',
						'options'       => array(
							'disabled' => __( 'Disabled', 'wp-business-reviews' ),
							'daily'    => __( 'Daily', 'wp-business-reviews' ),
							'weekly'   => __( 'Weekly', 'wp-business-reviews' ),
						),
						'wrapper_class' => 'wpbr-field--spacious',
					),
					'uninstall_behavior' => array(
						'name'    => __( 'Uninstall Behavior', 'wp-business-reviews' ),
						'type'    => 'radio',
						'default' => 'keep',
						'options' => array(
							'keep'   => __( 'Keep all plugin settings, collections, and reviews.', 'wp-business-reviews' ),
							'remove' => __( 'Remove all plugin settings, collections, and reviews.', 'wp-business-reviews' ),
						),
						'wrapper_class' => 'wpbr-field--spacious',
					),
					'save_advanced' => array(
						'type'    => 'save',
						'wrapper_class' => 'wpbr-field--spacious',
					),
				),
			),
		),
	),
);

/**
 * Filters the entire plugin settings config.
 *
 * @since 0.1.0
 *
 * @param array $config Array of tabs, sections, and fields.
 */
return apply_filters( 'wpbr_config_settings', $config );
