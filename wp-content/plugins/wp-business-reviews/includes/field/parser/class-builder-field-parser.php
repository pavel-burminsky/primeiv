<?php
/**
 * Defines the Builder_Field_Parser class
 *
 * @link https://wpbusinessreviews.com
 *
 * @package WP_Business_Reviews\Includes\Field\Parser
 * @since 0.1.0
 */

namespace WP_Business_Reviews\Includes\Field\Parser;

use WP_Business_Reviews\Includes\Field\Field;

/**
 * Recursively parses fields from a settings config.
 *
 * This parser specifically caters to hierarchical settings configs made up of
 * sections and fields.
 *
 * @since 0.1.0
 */
class Builder_Field_Parser {
	/**
	 * Recursively parses fields from a builder config.
	 *
	 * When the parser finds a `fields` key, then each item within that array
	 * is assumed to be a complete field definition. The arguments within the
	 * definition are used to create a new `Field` object.
	 *
	 * @since 0.1.0
	 *
	 * @param Config $config   Builder config.
	 * @param array  $settings Collection settings used to populate fields.
	 * @return Fields[] Array of `Field` objects.
	 */
	public function parse_fields( $config, array $settings = array() ) {
		$field_objects = array();

		// Convert config to array for processing.
		$config_array = $config->getArrayCopy();

		foreach ( $config_array as $section ) {
			foreach ( $section['fields'] as $field_id => $field_args ) {
				// Create the field object from the field definition.
				$field_object = new Field( $field_id, $field_args, 'wpbr_collection' );

				if ( $field_object ) {
					// Hydrate subfields if they are set.
					if ( ! empty( $field_args['subfields'] ) ) {
						$subfields = $field_args['subfields'];
						$subfield_objects = array();

						foreach ( $subfields as $subfield_id => $subfield_args ) {
							$subfield_object = new Field( $subfield_id, $subfield_args );
							$subfield_object->set_field_arg( 'is_subfield', true );
							$subfield_objects[ $subfield_id ] = $subfield_object;
						}

						$field_object->set_field_arg( 'subfields', $subfield_objects );
					}

					// Attempt to retrieve the field value.
					if ( isset( $settings[ $field_id ] ) ) {
						$field_value = $settings[ $field_id ];
					} else {
						$field_value = $field_object->get_field_arg( 'default' );
					}

					// Set the field value.
					$field_object->set_value( $field_value );
				}

				// Add the field object to array of parsed fields.
				$field_objects[ $field_id ] = $field_object;
			}
		}

		return $field_objects;
	}
}
