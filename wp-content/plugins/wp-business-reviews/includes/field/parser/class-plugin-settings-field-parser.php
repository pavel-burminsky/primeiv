<?php
/**
 * Defines the Plugin_Settings_Field_Parser class
 *
 * @link https://wpbusinessreviews.com
 *
 * @package WP_Business_Reviews\Includes\Field\Parser
 * @since 0.1.0
 */

namespace WP_Business_Reviews\Includes\Field\Parser;

use WP_Business_Reviews\Includes\Config;
use WP_Business_Reviews\Includes\Deserializer\Option_Deserializer;
use WP_Business_Reviews\Includes\Field\Field;

/**
 * Recursively parses fields from a settings config.
 *
 * This parser specifically caters to hierarchical settings configs made up of
 * sections and fields.
 *
 * @since 0.1.0
 */
class Plugin_Settings_Field_Parser {
	/**
	* Settings retriever.
	*
	* @since 0.1.0
	* @var string $deserializer
	*/
	private $deserializer;

	/**
	 * Instantiates a Plugin_Settings_Field_Parser object.
	 *
	 * @since 0.1.0
	 *
	 * @param Option_Deserializer  $deserializer  Settings retriever.
	 */
	public function __construct(
		Option_Deserializer $deserializer
	) {
		$this->deserializer  = $deserializer;
	}

	/**
	 * Recursively parses fields from a config.
	 *
	 * When the parser finds a `fields` key, then each item within that array
	 * is assumed to be a complete field definition. The arguments within the
	 * definition are used to create a new `Field` object.
	 *
	 * @since 0.1.0
	 *
	 * @param  Config $config Path to config or `Config` object.
	 * @return Fields[] Array of `Field` objects.
	 */
	public function parse_fields( $config ) {
		$field_objects = array();

		// Convert config to array for processing.
		$config_array = $config->getArrayCopy();

		foreach ( $config_array as $key => $value ) {
			foreach ( $value['sections'] as $section ) {
				foreach ( $section['fields'] as $field_id => $field_args ) {
					// Create the field object from the field definition.
					$field_object = new Field( $field_id, $field_args, 'wpbr_option' );

					if ( $field_object ) {
						// Attempt to retrieve the field value.
						$field_value = $this->deserializer->get( $field_id );

						if ( null === $field_value ) {
							// Get the default value.
							$field_value = $field_object->get_arg( 'default' );
						}

						// Set the field value.
						$field_object->set_value( $field_value );

						// Add the field object to array of parsed fields.
						$field_objects[ $field_id ] = $field_object;
					}

				}
			}
		}

		return $field_objects;
	}
}
