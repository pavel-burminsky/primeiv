<?php
/**
 * Defines the Google Places section of the Builder.
 *
 * @package WP_Business_Reviews\Config
 * @since   0.1.0
 */

namespace WP_Business_Reviews\Config;

$config = array(
	'review_source' => array(
		'name'   => __( 'Review Source', 'wp-business-reviews' ),
		'icon'   => 'fab wpbr-icon wpbr-fw wpbr-google',
		'fields' => array(
			'platform' => array(
				'type'  => 'hidden',
				'value' => 'google_places',
			),
			'review_source' => array(
				'type'             => 'review_source_search',
				'powered_by_image' => WPBR_ASSETS_URL . 'images/powered-by-google.png',
				'powered_by_text'  => __( 'Powered by Google', 'wp-business-reviews' ),
				'subfields'        => array(
					'platform' => array(
						'type'        => 'hidden',
						'value'       => 'google_places',
					),
					'review_source_search_terms' => array(
						'name'        => __( 'Search Terms', 'wp-business-reviews' ),
						'type'        => 'text',
						'tooltip'     => __( 'Defines the terms used when searching the Google Places API.', 'wp-business-reviews' ),
						'placeholder' => __( 'Business Name or Place ID', 'wp-business-reviews' ),
						'required'    => 'required',
					),
					'review_source_search_location' => array(
						'name'        => __( 'Location', 'wp-business-reviews' ),
						'type'        => 'text',
						'tooltip'     => __( 'Defines the location used when searching the Google Places API.', 'wp-business-reviews' ),
						'placeholder' => __( 'City, State', 'wp-business-reviews' ),
						'required'    => 'required',
					),
					'review_source_search_button' => array(
						'type'        => 'button',
						'button_text' => __( 'Search', 'wp-business-reviews' ),
						'value'       => 'search',
						'icon'        => 'search',
					),
				),
			),
			'review_source_footnote' => array(
				'type'          => 'footnote',
				'description'   => sprintf(
					/* translators: platform name, number of reviews, link to documentation */
					__( '%1$s returns up to %2$s reviews at a time. Have more reviews? Learn %3$show to add other reviews%4$s.', 'wp-business-reviews' ),
					__( 'Google', 'wp-business-reviews'),
					'<strong>5</strong>',
					'<a href="https://wpbusinessreviews.com/documentation/collections/adding-single-reviews-to-existing-collections/" target="_blank" rel="noopener noreferrer">',
					'</a>'
				),
				'wrapper_class' => 'wpbr-field--border-top',
			),
		),
	),
);

return $config;
