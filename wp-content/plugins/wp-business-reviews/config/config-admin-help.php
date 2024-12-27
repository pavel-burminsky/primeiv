<?php
/**
 * Defines the admin help config.
 *
 * @package WP_Business_Reviews\Config
 * @since   0.1.0
 */

namespace WP_Business_Reviews\Config;

/**
 * The structure of the config is as follows:
 *
 * $config = array(
 *     'context_id' => array(
 *         'message_id' => array(
 *             'icon' => 'icon-name',
 *             'text' => 'Help text.',
 *         ),
 *     ),
 * );
 */
$config = array(
	'api' => array(
		'search' => array(
			'icon' => 'search',
			'text' => 'Start by searching for your business in the Review Source panel.',
		),
		'build' => array(
			'icon' => 'cloud-download-alt',
			'text' => 'Build your collection by clicking "Get Reviews" under your business.',
		),
		'presentation' => array(
			'icon' => 'save',
			'text' => 'Save your collection to unlock settings for Presentation, Order, and Filters.',
		),
	),
	'review_tag' => array(
		'search' => array(
			'icon' => 'tags',
			'text' => 'Start by selecting the tags from which to build your collection.',
		),
		'presentation' => array(
			'icon' => 'paint-brush',
			'text' => 'Fine-tune the Presentation panel and click "Save" when finished.',
		),
	),
);

/**
 * Filters the admin help messages within the Reviews Builder.
 *
 * @since 0.1.0
 *
 * @param array $config Admin help config containing messages.
 */
return apply_filters ( 'wpbr_config_admin_help_builder', $config );
