<?php
/**
 * Defines the Review Tags section of the Builder.
 *
 * @package WP_Business_Reviews\Config
 * @since   0.2.0
 */

namespace WP_Business_Reviews\Config;

$config = array(
	'review_source' => array(
		'name'   => __( 'Review Source', 'wp-business-reviews' ),
		'icon'   => 'fas wpbr-icon wpbr-fw wpbr-tags',
		'fields' => array(
			'platform' => array(
				'type'  => 'hidden',
				'value' => 'review_tag',
			),
			'review_tags' => array(
				'name'     => __( 'Tags', 'wp-business-reviews' ),
				'type'     => 'review_tags',
				'tooltip'  => __( 'Determines which reviews are included in the collection based on Review Tag.', 'wp-business-reviews' ),
			),
		),
	),
);

return $config;
