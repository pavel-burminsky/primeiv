<?php
/**
 * Defines the Location class
 *
 * @link https://wpbusinessreviews.com
 *
 * @package WP_Business_Reviews\Includes
 * @since 0.1.0
 */

namespace WP_Business_Reviews\Includes;

/**
 * Defines a physical location.
 *
 * @since 0.1.0
 */
class Location implements \JsonSerializable {
	/**
	 * Formatted address ready for display.
	 *
	 * @since 1.3.0 Changed visibility to public.
	 * @since 0.1.0
	 * @var string $formatted_address
	 */
	public $formatted_address;

	/**
	 * Address components.
	 *
	 * @since 1.3.0 Changed visibility to public.
	 * @since 0.1.0
	 * @var array $address
	 */
	public $address;

	/**
	 * Map coordinates.
	 *
	 * @since 1.3.0 Changed visibility to public.
	 * @since 0.1.0
	 * @var array $coordinates
	 */
	public $coordinates;

	/**
	 * Telephone number.
	 *
	 * @since 0.1.0
	 * @var string $phone
	 */
	public $phone;

	/**
	 * Instantiates the Location object.
	 *
	 * @since 0.1.0
	 *
	 * @param string $formatted_address Optional. Fully-assembled address.
	 * @param array  $address {
	 *     Optional. Address components.
	 *
	 *     @type string $street_address Street address.
	 *     @type string $city           City.
	 *     @type string $state_province Abbreviated state or province.
	 *     @type string $postal_code    Postal code.
	 *     @type string $country        Country.
	 * }
	 * @param array  $coordinates {
	 *     Optional. Map coordinates.
	 *
	 *     @type float $latitude  Latitude.
	 *     @type float $longitude Longitude.
	 * }
	 * @param string $phone Optional. Phone number.
	 */
	public function __construct(
		$formatted_address = '',
		array $address = array(),
		array $coordinates = array(),
		$phone = ''
	) {
		$this->formatted_address = $formatted_address;
		$this->address           = $address;
		$this->coordinates       = $coordinates;
		$this->phone             = $phone;
	}

	/**
	 * Retrieves formatted address.
	 *
	 * @since 0.1.0
	 */
	public function get_formatted_address() {
		return $this->formatted_address;
	}

	/**
	 * Retrieves all address components.
	 *
	 * @since 1.3.0 Move support for single address component to its own method.
	 * @since 0.1.0
	 *
	 * @return array Address components.
	 */
	public function get_address() {
		 return $this->address;
	}

	/**
	 * Retrieves a single address component.
	 *
	 * @since 1.3.0
	 *
	 * @param string $subcomponent Address subcomponent. Accepts 'street_address`,
	 *                             'city', 'state_province', or 'country'.
	 * @return string A single address component.
	 */
	public function get_address_component( $component ) {
		if ( ! isset( $this->address[ $component ] ) ) {
			return '';
		}

		return $this->address[ $component ];
	}

	/**
	 * Retrieves coordinates.
	 *
	 * @since 0.1.0
	 */
	public function get_coordinates() {
		return $this->coordinates;
	}

	/**
	 * Retrieves telephone number.
	 *
	 * @since 0.1.0
	 */
	public function get_phone() {
		return $this->phone;
	}

	/**
	 * Prepares object for JSON serialization.
	 *
	 * @since 0.1.0
	 *
	 * @return array Array of object properties.
	 */
    #[\ReturnTypeWillChange]
	public function jsonSerialize() {
        return get_object_vars( $this );
    }
}
