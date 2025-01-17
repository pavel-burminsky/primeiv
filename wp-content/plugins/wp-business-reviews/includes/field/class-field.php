<?php
/**
 * Defines the Field class
 *
 * @package WP_Business_Reviews\Includes\Field
 * @since   0.1.0
 */

namespace WP_Business_Reviews\Includes\Field;

use WP_Business_Reviews\Includes\View;

/**
 * Implements a basic field based on provided arguments.
 *
 * @since 0.1.0
 */
class Field {
	/**
	 * The prefix prepended to the field control name.
	 *
	 * @since 0.1.0
	 * @var string $prefix
	 */
	protected $prefix;

	/**
	 * Unique identifier of the field.
	 *
	 * @since 0.1.0
	 * @var string $field_id
	 */
	protected $field_id;

	/**
	 * Field arguments used to define field elements and their attributes.
	 *
	 * @since 0.1.0
	 * @var array $field_args
	 */
	protected $field_args;

	/**
	 * Field value.
	 *
	 * @since 0.1.0
	 * @var string $value
	 */
	protected $value;

	/**
	 * Instantiates a Field object.
	 *
	 * @since 0.1.0
	 *
	 * @param string $field_id Unique identifier of the field.
	 * @param array  $field_args {
	 *     Field arguments.
	 *
	 *     @type string $name           Optional. Field name also used as label.
	 *     @type string $type           Optional. Field type that determines which control is used.
	 *     @type string $default        Optional. Default value used if field value is not set.
	 *     @type string $tooltip        Optional. Tooltip that clarifies field purpose.
	 *     @type string $description    Optional. Description that clarifies field usage.
	 *     @type string $wrapper_class  Optional. CSS class assigned to the field wrapper.
	 *     @type string $name_element   Optional. Field name element. Accepts 'span' or 'label'.
	 *     @type string $placeholder    Optional. Placeholder text for input controls.
	 *     @type array  $options        Optional. Field options for select/radios/checkboxes.
	 * }
	 * @param string $prefix   The prefix prepended to the field control name.
	 */
	public function __construct( $field_id, array $field_args = array(), $prefix = 'wpbr' ) {
		$this->field_id   = $field_id;
		$this->field_args = wp_parse_args(
			$field_args,
			$this->get_default_field_args()
		);
		$this->set_value();
		$this->prefix = $prefix;
	}

	/**
	 * Gets default field arguments.
	 *
	 * @since 0.1.0
	 *
	 * @return array Default field arguments.
	 */
	public function get_default_field_args() {
		return array(
			'name'          => null,
			'type'          => 'text',
			'value'         => null,
			'default'       => null,
			'tooltip'       => null,
			'description'   => null,
			'wrapper_class' => null,
			'name_element'  => 'span',
			'placeholder'   => null,
			'options'       => array(),
			'is_subfield'   => false,
			'subfields'     => array(),
			'required'      => '',
		);
	}

	/**
	 * Gets field arguments.
	 *
	 * @since 0.1.0
	 *
	 * @return array Field arguments.
	 */
	public function get_field_args() {
		return $this->field_args;
	}

	/**
	 * Gets a field argument.
	 *
	 * @since 0.1.0
	 *
	 * @param string $key Key of the requested arg.
	 * @return mixed|null Value of the requested arg.
	 */
	public function get_field_arg( $key ) {
		if ( isset( $this->field_args[ $key ] ) ) {
			return $this->field_args[ $key ];
		}

		// Invalid key was provided.
		return null;
	}

	/**
	 * Gets field ID.
	 *
	 * @since 0.1.0
	 *
	 * @return string Field ID.
	 */
	public function get_field_id() {
		return $this->field_id;
	}

	/**
	 * Gets field value.
	 *
	 * @since 0.1.0
	 *
	 * @return mixed $value Field value.
	 */
	public function get_value() {
		/**
		 * Filters the field value being retrieved.
		 *
		 * @since 0.1.0
		 */
		return apply_filters( "wpbr_get_field_value_{$this->field_id}", $this->value );
	}

	/**
	 * Gets the CSS class for the field.
	 *
	 * @since 0.1.0
	 *
	 * @return string The CSS class.
	 */
	protected function get_field_class() {
		$field_classes = array(
			'wpbr-field',
			"wpbr-field--{$this->field_args['type']}",
		);

		// Add wrapper class if one is set.
		if ( ! empty( $this->field_args['wrapper_class'] ) ) {
			$field_classes[] = $this->field_args['wrapper_class'];
		}

		// Add field or subfield JS handle.
		if ( $this->field_args['is_subfield'] ) {
			$field_classes[] = 'js-wpbr-subfield';
		} else {
			$field_classes[] = 'js-wpbr-field';
		}

		// Convert classes from array to string.
		return implode( ' ', $field_classes );
	}

	/**
	 * Sets a field argument.
	 *
	 * @since 0.1.0
	 *
	 * @param string $key Key of the arg being set.
	 * @param mixed  $value Value of the arg being set.
	 * @return bool True if successful, false otherwise.
	 */
	public function set_field_arg( $key, $value ) {
		if ( isset( $this->field_args[ $key ] ) ) {
			$this->field_args[ $key ] = $value;
			return true;
		}

		// Invalid key was provided.
		return false;
	}

	/**
	 * Sets field value.
	 *
	 * Field value is set to the passed value. If a value is not passed, then
	 * the field will attempt to set a default value.
	 *
	 * @since 0.1.0
	 *
	 * @param mixed $value Field value.
	 */
	public function set_value( $value = null ) {
		// Determine value if one is not directly passed.
		if ( null === $value ) {
			if ( isset( $this->field_args['value'] ) ) {
				// Set value as provided via constructor.
				$value = $this->field_args['value'];
			} elseif ( isset( $this->field_args['default'] ) ) {
				// Otherwise fall back to default value.
				$value = $this->field_args['default'];
			}
		}

		/**
		 * Filters the field value being set.
		 *
		 * @since 0.1.0
		 */
		$this->value = apply_filters(
			"wpbr_set_field_value_{$this->field_id}",
			$value
		);
	}

	/**
	 * Renders a given view.
	 *
	 * @since 0.1.0
	 */
	public function render() {
		if ( 'internal' === $this->field_args['type'] ) {
			return;
		}

		$view_object = new View( WPBR_PLUGIN_DIR . 'views/field/field.php' );
		$view_object->render(
			array(
				'field_id'    => $this->get_field_id(),
				'field_args'  => $this->get_field_args(),
				'value'       => $this->get_value(),
				'field_class' => $this->get_field_class(),
				'prefix'      => $this->prefix,
			)
		);
	}
}
