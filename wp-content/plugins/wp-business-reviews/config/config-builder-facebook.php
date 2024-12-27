<?php
/**
 * Defines the Facebook section of the Builder.
 *
 * @package WP_Business_Reviews\Config
 * @since   0.1.0
 */

namespace WP_Business_Reviews\Config;

/**
 * Filters the Facebook pages that are available to select.
 *
 * @since 0.2.0
 *
 * @param array $pages Multi-dimensional array of Facebook pages and tokens.
 */
$pages = apply_filters( 'wpbr_facebook_pages', array() );

$config = array(
	'review_source' => array(
		'name'   => __( 'Review Source', 'wp-business-reviews' ),
		'icon'   => 'fab wpbr-icon wpbr-fw wpbr-facebook',
		'fields' => array(
			'platform' => array(
				'type'  => 'hidden',
				'value' => 'facebook',
			),
			'review_source' => array(
				'type'             => 'review_source_facebook',
				'subfields'        => array(
					'facebook_pages_select' => array(
						'name'    => __( 'Facebook Page', 'wp-business-reviews' ),
						'type'    => 'facebook_pages_select',
						'tooltip' => 'Defines the Facebook page from which reviews are sourced.',
						'value'   => $pages,
					),
				),
			),
			'review_source_footnote' => array(
				'type'        => 'footnote',
				'description' => sprintf(
					/* translators: platform name, number of reviews, link to documentation */
					__( '%1$s returns up to %2$s reviews at a time. Have more reviews? Learn %3$show to add other reviews%4$s.', 'wp-business-reviews' ),
					__( 'Facebook', 'wp-business-reviews'),
					'<strong>24</strong>',
					'<a href="https://wpbusinessreviews.com/documentation/collections/adding-single-reviews-to-existing-collections/" target="_blank" rel="noopener noreferrer">',
					'</a>'
				),
				'wrapper_class' => 'wpbr-field--border-top',
			),
		),
	),
);

return $config;
