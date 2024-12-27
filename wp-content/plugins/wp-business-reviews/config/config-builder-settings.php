<?php
/**
 * Defines the reviews builder config.
 *
 * @package WP_Business_Reviews\Config
 * @since   0.1.0
 */

namespace WP_Business_Reviews\Config;

$config = array(
	'reviews' => array(
		'name'   => __( 'Reviews', 'wp-business-reviews' ),
		'icon'   => 'fas wpbr-icon wpbr-fw wpbr-user-circle',
		'fields' => array(
			'review_components' => array(
				'name'    => __( 'Review Components', 'wp-business-reviews' ),
				'type'    => 'checkboxes',
				'tooltip' => __( 'Defines the visible components of a review.', 'wp-business-reviews' ),
				'default' => array(
					'reviewer_image' => 'enabled',
					'reviewer_name'  => 'enabled',
					'rating'         => 'enabled',
					'recommendation' => 'enabled',
					'timestamp'      => 'enabled',
					'platform_icon'  => 'enabled',
					'content'        => 'enabled',
				),
				'options' => array(
					'reviewer_image' => __( 'Reviewer Image', 'wp-business-reviews' ),
					'reviewer_name'  => __( 'Reviewer Name', 'wp-business-reviews' ),
					'rating'         => __( 'Rating', 'wp-business-reviews' ),
					'recommendation' => __( 'Recommendation', 'wp-business-reviews' ),
					'timestamp'      => __( 'Timestamp', 'wp-business-reviews' ),
					'platform_icon'  => __( 'Platform Icon', 'wp-business-reviews' ),
					'content'        => __( 'Review Content', 'wp-business-reviews' ),
				),
			),
			'max_characters' => array(
				'name'    => __( 'Maximum Characters', 'wp-business-reviews' ),
				'type'    => 'number',
				'tooltip' => __( 'Defines the maximum character limit before the review is truncated. An empty or 0 value will display the full contents of the review provided by the platform API.', 'wp-business-reviews' ),
				'default' => 280,
				'placeholder' => __( 'Unlimited', 'wp-business-reviews' ),
			),
			'line_breaks' => array(
				'name'    => __( 'Line Breaks', 'wp-business-reviews' ),
				'type'    => 'radio',
				'tooltip' => __( 'Determines whether line breaks within the review content are displayed. Not all reviews have line breaks.', 'wp-business-reviews' ),
				'default' => 'disabled',
				'options'  => array(
					'enabled'  => __( 'Enabled', 'wp-business-reviews' ),
					'disabled' => __( 'Disabled', 'wp-business-reviews' ),
				),
			),
		),
	),
	'presentation' => array(
		'name'   => __( 'Presentation', 'wp-business-reviews' ),
		'icon'   => 'fas wpbr-icon wpbr-fw wpbr-paint-brush',
		'status' => 'locked',
		'fields' => array(
			'post_parent' => array(
				'type'  => 'hidden',
				'value' => 0,
			),
			'style' => array(
				'name'    => __( 'Style', 'wp-business-reviews' ),
				'type'    => 'select',
				'tooltip' => __( 'Styles the appearance of reviews.', 'wp-business-reviews' ),
				'default' => 'light',
				'options' => array(
					'light'       => __( 'Light', 'wp-business-reviews' ),
					'dark'        => __( 'Dark', 'wp-business-reviews' ),
					'transparent' => __( 'Transparent', 'wp-business-reviews' ),
				),
			),
			'format' => array(
				'name'    => __( 'Format', 'wp-business-reviews' ),
				'type'    => 'select',
				'tooltip' => __( 'Defines the format in which reviews are displayed.', 'wp-business-reviews' ),
				'default' => 'review_gallery',
				'options' => array(
					'review_gallery'  => __( 'Gallery', 'wp-business-reviews' ),
					'review_carousel' => __( 'Carousel', 'wp-business-reviews' ),
					'review_list'     => __( 'List', 'wp-business-reviews' ),
				),
			),
			'max_columns' => array(
				'name'     => __( 'Maximum Columns', 'wp-business-reviews' ),
				'type'     => 'select',
				'tooltip'  => __( 'Defines the maximum number of columns in the responsive gallery. Fewer columns may be shown based on available width.', 'wp-business-reviews' ),
				'default'  => 0,
				'options'  => array(
					'0' => __( 'Auto Fit', 'wp-business-reviews' ),
					'1' => __( '1 Column', 'wp-business-reviews' ),
					'2' => __( '2 Columns', 'wp-business-reviews' ),
					'3' => __( '3 Columns', 'wp-business-reviews' ),
					'4' => __( '4 Columns', 'wp-business-reviews' ),
				),
			),
			'slides_per_view' => array(
				'name'     => __( 'Maximum Slides Per View', 'wp-business-reviews' ),
				'type'     => 'select',
				'tooltip'  => __( 'Defines the maximum number of slides visible in the carousel at one time. Fewer slides may be shown based on available width.', 'wp-business-reviews' ),
				'default'  => 3,
				'options'  => array(
					'1' => __( '1 Slide', 'wp-business-reviews' ),
					'2' => __( '2 Slides', 'wp-business-reviews' ),
					'3' => __( '3 Slides', 'wp-business-reviews' ),
					'4' => __( '4 Slides', 'wp-business-reviews' ),
					'5' => __( '5 Slides', 'wp-business-reviews' ),
					'6' => __( '6 Slides', 'wp-business-reviews' ),
				),
			),
			'max_reviews' => array(
				'name'     => __( 'Maximum Reviews', 'wp-business-reviews' ),
				'type'     => 'number',
				'tooltip'  => __( 'Defines the maximum number of reviews in the collection, up to 24. Fewer reviews may be shown based on availability.', 'wp-business-reviews' ),
				'default'  => 24,
				'min'      => 1,
			),
		),
	),
	'order' => array(
		'name'   => __( 'Order', 'wp-business-reviews' ),
		'icon'   => 'fas wpbr-icon wpbr-fw wpbr-sort-amount-down',
		'status' => 'locked',
		'fields' => array(
			'orderby' => array(
				'name'    => __( 'Order By', 'wp-business-reviews' ),
				'type'    => 'select',
				'tooltip' => __( 'Defines the parameter by which reviews are ordered.', 'wp-business-reviews' ),
				'default' => 'review_date',
				'options' => array(
					'review_date' => __( 'Review Date', 'wp-business-reviews' ),
					'rating'      => __( 'Rating', 'wp-business-reviews' ),
					'ID'          => __( 'Post ID', 'wp-business-reviews' ),
					'menu_order'  => __( 'Menu Order', 'wp-business-reviews' ),
				),
			),
			'order' => array(
				'name'    => __( 'Order', 'wp-business-reviews' ),
				'type'    => 'select',
				'tooltip' => __( 'Defines the ascending or descending order of reviews.', 'wp-business-reviews' ),
				'default' => 'desc',
				'options' => array(
					'desc' => __( 'Descending', 'wp-business-reviews' ),
					'asc'  => __( 'Ascending', 'wp-business-reviews' ),
				),
			),
		),
	),
	'filters' => array(
		'name'   => __( 'Filters', 'wp-business-reviews' ),
		'icon'   => 'fas wpbr-icon wpbr-fw wpbr-filter',
		'status' => 'locked',
		'fields' => array(
			'min_rating' => array(
				'name'        => __( 'Minimum Rating', 'wp-business-reviews' ),
				'type'        => 'select',
				'tooltip' => __( 'Determines the visibility of reviews based on rating.', 'wp-business-reviews' ),
				'default' => '0',
				'options' => array(
					'0'   => __( 'No minimum rating', 'wp-business-reviews' ),
					'100' => __( '5.0 or positive recommendation', 'wp-business-reviews' ),
					'80'  => __( '4.0', 'wp-business-reviews' ),
					'60'  => __( '3.0', 'wp-business-reviews' ),
					'40'  => __( '2.0', 'wp-business-reviews' ),
					'1'   => __( '1.0 or negative recommendation', 'wp-business-reviews' ),
				),
			),
			'review_type' => array(
				'name'    => __( 'Review Type', 'wp-business-reviews' ),
				'type'    => 'select',
				'tooltip' => __( 'Determines the visibility of reviews based on type.', 'wp-business-reviews' ),
				'default' => 'all',
				'options' => array(
					'all'            => __( 'Ratings and recommendations', 'wp-business-reviews' ),
					'recommendation' => __( 'Recommendations only', 'wp-business-reviews' ),
					'rating'         => __( 'Ratings only', 'wp-business-reviews' ),
				),
			),
			'blank_reviews' => array(
				'name'    => __( 'Blank Reviews', 'wp-business-reviews' ),
				'type'    => 'select',
				'tooltip' => __( 'Determines the visibility of reviews without content.', 'wp-business-reviews' ),
				'default' => 'enabled',
				'options' => array(
					'enabled'  => __( 'Include reviews without text', 'wp-business-reviews' ),
					'disabled' => __( 'Exclude reviews without text', 'wp-business-reviews' ),
				),
			),
		),
	),
);

/**
 * Filters the Reviews Builder config.
 *
 * @since 0.1.0
 *
 * @param array $config Reviews Builder config containing sections and fields.
 */
return apply_filters( 'wpbr_config_reviews_builder', $config );
