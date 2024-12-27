<?php
/**
 * Defines the Trustpilot section of the Builder.
 *
 * @package WP_Business_Reviews\Config
 * @since   1.3.0
 */

namespace WP_Business_Reviews\Config;

$config = array(
	'review_source' => array(
		'name'   => __( 'Trustpilot Review Source', 'wp-business-reviews' ),
		'icon'   => 'fas wpbr-icon wpbr-fw wpbr-star',
		'fields' => array(
			'platform' => array(
				'type'  => 'hidden',
				'value' => 'trust_pilot',
			),
			'review_source' => array(
				'type'             => 'review_source_search',
				'powered_by_image' => WPBR_ASSETS_URL . 'images/powered-by-trust-pilot.png',
				'powered_by_text'  => __( 'Powered by Trustpilot', 'wp-business-reviews' ),
				'subfields'        => array(
					'platform' => array(
						'type'        => 'hidden',
						'value'       => 'trust_pilot',
					),
					'review_source_search_terms'  => array(
						'name'        => __( 'Business Domain (URL)', 'wp-business-reviews' ),
						'type'        => 'text',
						'tooltip'     => __( 'Look up your business by the primary business domain URL.', 'wp-business-reviews' ),
						'placeholder' => __( 'example.com', 'wp-business-reviews' ),
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
					__( 'Trustpilot', 'wp-business-reviews'),
					'<strong>20</strong>',
					'<a href="https://wpbusinessreviews.com/documentation/collections/adding-single-reviews-to-existing-collections/" target="_blank" rel="noopener noreferrer">',
					'</a>'
				),
				'wrapper_class' => 'wpbr-field--border-top',
			),
		),
	),
);

return $config;
