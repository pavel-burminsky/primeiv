<?php
/**
 * Defines the WooCommerce section of the Builder.
 *
 * @package WP_Business_Reviews\Config
 * @since   1.5.0
 */

namespace WP_Business_Reviews\Config;

// Get Products with Reviews.
$args = [
	'post_type'     => 'product',
	'comment_count' => [
		'value'   => 1,
		'compare' => '>='
	]
];

$query         = new \WP_Query( $args );
$option_values = [];
// Set up array for option select.
foreach ( $query->posts as $product ) {
	$option_values[ $product->ID ] = $product->post_title;
}
if ( empty( $option_values ) ) {
	$option_values[0] = __( 'No Products with Reviews Found', 'wp-business-reviews' );
}

$config = array(
	'review_source' => array(
		'name'   => __( 'WooCommerce Review Source', 'wp-business-reviews' ),
		'icon'   => 'fas wpbr-icon wpbr-fw wpbr-star',
		'fields' => array(
			'platform'      => array(
				'type'  => 'hidden',
				'value' => 'woocommerce',
			),
			'review_source' => array(
				'type'             => 'review_source_search',
				'powered_by_image' => WPBR_ASSETS_URL . 'images/powered-by-woocommerce.png',
				'powered_by_text'  => __( 'Powered by WooCommerce', 'wp-business-reviews' ),
				'subfields'        => array(
					'platform'                    => array(
						'type'  => 'hidden',
						'value' => 'woocommerce',
					),
					'review_source_search_terms'  => array(
						'name'     => __( 'Products with Reviews', 'wp-business-reviews' ),
						'type'     => 'select',
						'options'  => $option_values,
						'tooltip'  => __( 'Select a product with reviews to build this collection.', 'wp-business-reviews' ),
						'required' => 'required',
					),
					'review_source_search_button' => array(
						'type'        => 'button',
						'button_text' => __( 'Select', 'wp-business-reviews' ),
						'value'       => 'search',
						'icon'        => 'search',
					),
				),
			),
		),
	),
);

return $config;
