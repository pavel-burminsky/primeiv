<?php
/**
 * Defines the Builder_Inspector class
 *
 * @link https://wpbusinessreviews.com
 *
 * @package WP_Business_Reviews\Includes\Builder
 * @since 0.1.0
 */

namespace WP_Business_Reviews\Includes\Builder;

use WP_Business_Reviews\Includes\Config;
use WP_Business_Reviews\Includes\Field\Parser\Builder_Field_Parser as Field_Parser;
use WP_Business_Reviews\Includes\Field\Field_Repository;
use WP_Business_Reviews\Includes\View;
use WP_Business_Reviews\Includes\Collection;

/**
 * Displays settings in a compact user interface.
 *
 * @since 0.1.0
 */
class Builder_Inspector {
	/**
	 * Builder config.
	 *
	 * @since 0.1.0
	 * @var Config
	 */
	protected $config;

	/**
	 * Parser of field objects.
	 *
	 * @since 0.1.0
	 * @var Field_Parser
	 */
	protected $field_parser;

	/**
	 * Platform associated with the collection.
	 *
	 * @since 0.1.0
	 * @var string $platform
	 */
	protected $platform;

	/**
	 * Collection of reviews data and presentation settings.
	 *
	 * @since 0.1.0
	 * @var Collection
	 */
	protected $collection;

	/**
	 * Repository that holds field objects.
	 *
	 * @since 0.1.0
	 * @var Field_Repository
	 */
	protected $field_repository;

	/**
	 * Instantiates the Builder_Inspector object.
	 *
	 * @since 0.1.0
	 *
	 * @param Config       $config       Collection config.
	 * @param Field_Parser $field_Parser Parser of field objects.
	 * @param string       $platform     Optional. Platform.
	 * @param Collection   $collection   Optional. Collection.
	 */
	public function __construct(
		Config $config,
		Field_Parser $field_parser,
		$platform = '',
		Collection $collection = null
	) {
		$this->config       = $config;
		$this->field_parser = $field_parser;
		$this->platform     = $platform;
		$this->collection   = $collection;
	}

	/**
	 * Initializes the object for use.
	 *
	 * @since 0.1.0
	 */
	public function init() {
		if ( isset( $this->platform ) ) {
			$this->config = $this->prepare_config( $this->config, $this->platform );
		}

		if ( ! empty( $this->collection ) ) {
			$settings = $this->collection->get_settings();
		} else {
			$settings = array();
		}

		$this->field_repository = new Field_Repository(
			$this->field_parser->parse_fields(
				$this->config,
				$settings
			)
		);
	}

	/**
	 * Prepares config based on provided platform.
	 *
	 * @param Config $config   Path to config or `Config` object.
	 * @param string $platform The platform ID.
	 * @return Config The prepared Config object.
	 */
	protected function prepare_config( $config, $platform ) {
		$platform_slug = str_replace( '_', '-', $platform );

		// Prepend review source controls for each platform.
		$config->prepend_config(
			new Config( WPBR_PLUGIN_DIR . "config/config-builder-{$platform_slug}.php" )
		);

		// Remove controls that do not apply to certain platforms.
		if ( 'facebook' !== $platform && 'review_tag' !== $platform ) {
			unset( $config['reviews']['fields']['review_components']['options']['recommendation'] );
			unset( $config['filters']['fields']['review_type'] );
		}

		if ( 'yelp' === $platform ) {
			$config['reviews']['fields']['max_characters']['default'] = 160;
			$config['reviews']['fields']['max_characters']['description'] = __(
				'Define the length of each review up to 160 characters, which is the maximum length returned by Yelp.',
				'wp-business-reviews'
			);
		}

		if ( 'zomato' === $platform ) {
			$config['reviews']['fields']['max_characters']['default'] = 150;
			$config['reviews']['fields']['max_characters']['description'] = __(
				'Define the length of each review up to 150 characters, which is the maximum length returned by Zomato.',
				'wp-business-reviews'
			);
		}

		if ( 'review_tag' === $platform ) {
			$config['reviews']['fields']['max_characters']['description'] = __(
				'Define the length of each review. Some platforms, such as Yelp and Zomato, may be limited to a truncated review excerpt.',
				'wp-business-reviews'
			);
		}

		return $config;
	}

	/**
	 * Sets the platform.
	 *
	 * @param string $platform Collection platform.
	 */
	public function set_platform( $platform ) {
		$this->platform = $platform;
	}

	/**
	 * Sets the collection.
	 *
	 * @param array $collection Collection.
	 */
	public function set_collection( $collection ) {
		$this->collection = $collection;
	}

	/**
	 * Renders the inspector UI.
	 *
	 * @since  0.1.0
	 */
	public function render() {
		$post_id = $review_source_id = $platform = $title = '';
		$view_object = new View( WPBR_PLUGIN_DIR . 'views/builder/inspector.php' );

		if ( ! empty( $this->collection ) ) {
			$post_id          = $this->collection->get_post_id();
			$review_sources   = $this->collection->get_review_sources();

			if ( ! empty( $review_sources ) ) {
				$review_source_id = $review_sources[0]->get_review_source_id();
			}
		}

		$view_object->render(
			array(
				'post_id'               => $post_id,
				'review_source_id'      => $review_source_id,
				'platform'              => $this->platform,
				'config'                => $this->config,
				'field_repository'      => $this->field_repository,
			)
		);
	}
}
