<?php

if ( !defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

class TmmStoreLocatorStatePage {

    private const STORE_LOCATOR_SHORTCODE = 'wpsl';
    private const PAGE_STATE_FIELD = 'tmm_store_locator_state';
    private const STORE_LOCATOR_JS_OVERRIDE_FILE_PATH = '/js/wp-store-locator/wpsl-gmap.js';

    public function __construct() {
        $this->init();
    }

    private function init() {
        add_shortcode( 'tmm_store_locator_state', [ $this, 'shortcode_cb' ] );
        add_filter( 'wpsl_store_data', [ $this, 'filter_store_locator_results' ] );
        add_filter( 'wpsl_gmap_js', [ $this, 'rewrite_store_locator_js' ] );
        add_filter( 'tmm_store_locator_settings_in_template', [ $this, 'maybe_rewrite_wpsl_zoom_level' ] );
        add_action( 'acf/init', [ $this, 'register_page_fields' ] );
    }


    private static function prepare_acf_choices(): array {
        $choices = ['' => 'None'];
        foreach ( self::get_states_geolocation() as $abbreviation => $state_data ) {
            $choices[$abbreviation] = $state_data['name'];
        }
        return $choices;
    }

    public function maybe_rewrite_wpsl_zoom_level($wpsl_settings) {
        if ( get_field( self::PAGE_STATE_FIELD, get_the_ID() ) ) {
            $wpsl_settings['zoom_level'] = 6;
            $wpsl_settings['run_fitbounds'] = 1;
        }

        return $wpsl_settings;
    }


    public function rewrite_store_locator_js($url) {
        if ( file_exists( get_stylesheet_directory() . self::STORE_LOCATOR_JS_OVERRIDE_FILE_PATH ) ) {
            $url = get_stylesheet_directory_uri() . self::STORE_LOCATOR_JS_OVERRIDE_FILE_PATH;
        }
        return $url;
    }

    public function shortcode_cb( $atts ) {
        if ( !self::can_run_shortcode() ) {
            return '';
        }
        $state_abbreviation = get_field( self::PAGE_STATE_FIELD );
        $states = self::get_states_geolocation();
        if (
            !isset( $state_abbreviation )
            || !isset( $states[$state_abbreviation] )
        ) {
            return current_user_can( 'manage_options' )
                ? 'State is not specified or it doesnt exist in states list - ' . implode( ', ', array_keys( $states ) )
                : '';
        }
        $state = $states[$state_abbreviation];

        $category = $atts['category'] ?? '';
        ob_start();
        ?>
        <div tmm-store-locator data-page-id="<?php the_ID(); ?>" class="tmm-store-locator">
            <?php echo do_shortcode( sprintf( '[%s start_location="%s" category="%s"]', self::STORE_LOCATOR_SHORTCODE, $state['coordinates'], $category ) ); ?>
        </div>
        <?php
        do_action('tmm_below_store_locator_state', $state_abbreviation);
        return ob_get_clean();
    }

    private static function can_run_shortcode(): bool {
        return shortcode_exists( self::STORE_LOCATOR_SHORTCODE )
            && function_exists( 'get_field' );
    }

    public function filter_store_locator_results($stores_meta) {
        $page_id = $_GET['pageId'] ?? 0;

        if ( !$page_id ) {
            return $stores_meta;
        }

        $state_abbreviation = get_field( self::PAGE_STATE_FIELD, $page_id );
        $states = self::get_states_geolocation();
        if (
            !$state_abbreviation
            || !isset( $states[$state_abbreviation] )
        ) {
            return $stores_meta;
        }

        $stores_meta_filtered = [];
        foreach ( $stores_meta as $store_meta ) {
            if ( strcasecmp( $store_meta['state'], $state_abbreviation ) === 0 ) {
                $stores_meta_filtered[] = $store_meta;
            }
        }

        return $stores_meta_filtered;
    }

    public static function get_states_geolocation(): array {
        return [
            'AL' => [ 'name' => 'Alabama', 'coordinates' => '32.3182,-86.9023' ],
            'AK' => [ 'name' => 'Alaska', 'coordinates' => '64.2008,-149.4937' ],
            'AZ' => [ 'name' => 'Arizona', 'coordinates' => '34.0489,-111.0937' ],
            'AR' => [ 'name' => 'Arkansas', 'coordinates' => '34.5574,-92.2863' ],
            'CA' => [ 'name' => 'California', 'coordinates' => '36.7783,-119.4179' ],
            'CO' => [ 'name' => 'Colorado', 'coordinates' => '39.5501,-105.7821' ],
            'CT' => [ 'name' => 'Connecticut', 'coordinates' => '41.6032,-73.0877' ],
            'DE' => [ 'name' => 'Delaware', 'coordinates' => '38.9108,-75.5277' ],
            'FL' => [ 'name' => 'Florida', 'coordinates' => '27.6648,-81.5158' ],
            'GA' => [ 'name' => 'Georgia', 'coordinates' => '32.1656,-82.9001' ],
            'HI' => [ 'name' => 'Hawaii', 'coordinates' => '19.8968,-155.5828' ],
            'ID' => [ 'name' => 'Idaho', 'coordinates' => '44.0682,-114.7420' ],
            'IL' => [ 'name' => 'Illinois', 'coordinates' => '40.6331,-89.3985' ],
            'IN' => [ 'name' => 'Indiana', 'coordinates' => '40.2672,-86.1349' ],
            'IA' => [ 'name' => 'Iowa', 'coordinates' => '41.8780,-93.0977' ],
            'KS' => [ 'name' => 'Kansas', 'coordinates' => '39.0119,-98.4842' ],
            'KY' => [ 'name' => 'Kentucky', 'coordinates' => '37.8393,-84.2700' ],
            'LA' => [ 'name' => 'Louisiana', 'coordinates' => '30.9843,-91.9623' ],
            'ME' => [ 'name' => 'Maine', 'coordinates' => '45.2538,-69.4455' ],
            'MD' => [ 'name' => 'Maryland', 'coordinates' => '39.0458,-76.6413' ],
            'MA' => [ 'name' => 'Massachusetts', 'coordinates' => '42.4072,-71.3824' ],
            'MI' => [ 'name' => 'Michigan', 'coordinates' => '44.3148,-85.6024' ],
            'MN' => [ 'name' => 'Minnesota', 'coordinates' => '46.7296,-94.6859' ],
            'MS' => [ 'name' => 'Mississippi', 'coordinates' => '32.3547,-89.3985' ],
            'MO' => [ 'name' => 'Missouri', 'coordinates' => '37.9643,-91.8318' ],
            'MT' => [ 'name' => 'Montana', 'coordinates' => '46.8797,-110.3626' ],
            'NE' => [ 'name' => 'Nebraska', 'coordinates' => '41.4925,-99.9018' ],
            'NV' => [ 'name' => 'Nevada', 'coordinates' => '38.8026,-116.4194' ],
            'NH' => [ 'name' => 'New Hampshire', 'coordinates' => '43.1939,-71.5724' ],
            'NJ' => [ 'name' => 'New Jersey', 'coordinates' => '40.0583,-74.4057' ],
            'NM' => [ 'name' => 'New Mexico', 'coordinates' => '34.5199,-105.8701' ],
            'NY' => [ 'name' => 'New York', 'coordinates' => '40.7128,-74.0060' ],
            'NC' => [ 'name' => 'North Carolina', 'coordinates' => '35.7596,-79.0193' ],
            'ND' => [ 'name' => 'North Dakota', 'coordinates' => '47.5515,-101.0020' ],
            'OH' => [ 'name' => 'Ohio', 'coordinates' => '40.4173,-82.9071' ],
            'OK' => [ 'name' => 'Oklahoma', 'coordinates' => '35.0078,-97.0929' ],
            'OR' => [ 'name' => 'Oregon', 'coordinates' => '43.8041,-120.5542' ],
            'PA' => [ 'name' => 'Pennsylvania', 'coordinates' => '41.2033,-77.1945' ],
            'RI' => [ 'name' => 'Rhode Island', 'coordinates' => '41.5801,-71.4774' ],
            'SC' => [ 'name' => 'South Carolina', 'coordinates' => '33.8361,-81.1637' ],
            'SD' => [ 'name' => 'South Dakota', 'coordinates' => '43.9695,-99.9018' ],
            'TN' => [ 'name' => 'Tennessee', 'coordinates' => '35.5175,-86.5804' ],
            'TX' => [ 'name' => 'Texas', 'coordinates' => '31.968599,-99.901813' ],
            'UT' => [ 'name' => 'Utah', 'coordinates' => '40.433357,-111.880474' ],
            'VT' => [ 'name' => 'Vermont', 'coordinates' => '44.5588,-72.5778' ],
            'VA' => [ 'name' => 'Virginia', 'coordinates' => '36.712241, -76.276242' ],
            'WA' => [ 'name' => 'Washington', 'coordinates' => '47.7511,-120.7401' ],
            'WV' => [ 'name' => 'West Virginia', 'coordinates' => '38.5976,-80.4549' ],
            'WI' => [ 'name' => 'Wisconsin', 'coordinates' => '43.7844,-88.7879' ],
            'WY' => [ 'name' => 'Wyoming', 'coordinates' => '43.0760,-107.2903' ],
            'WDC' => [ 'name' => 'Washington D.C.', 'coordinates' => '38.9072,-77.0369' ]
        ];
    }

    public function register_page_fields() {
        if ( function_exists( 'acf_add_local_field_group' ) ):
            acf_add_local_field_group( array(
                'key' => 'group_63db9c386520a',
                'title' => 'Page Store Locator Settings',
                'fields' => array(
                    array(
                        'key' => 'field_63db9c3aad93a',
                        'label' => 'State',
                        'name' => 'tmm_store_locator_state',
                        'aria-label' => '',
                        'type' => 'select',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'choices' => self::prepare_acf_choices(),
                        'default_value' => false,
                        'return_format' => 'value',
                        'multiple' => 0,
                        'allow_null' => 0,
                        'ui' => 0,
                        'ajax' => 0,
                        'placeholder' => '',
                    ),
                    array(
                        'key' => 'field_63db9c3aad931',
                        'label' => 'Locations Book Button Url overrides',
                        'name' => 'primeiv_locations_book_button_overrides',
                        'aria-label' => '',
                        'type' => 'repeater',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'layout' => 'table',
                        'min' => 0,
                        'max' => 0,
                        'collapsed' => '',
                        'button_label' => 'Add Row',
                        'rows_per_page' => 20,
                        'sub_fields' => array(
                            array(
                                'key' => 'field_63db9c3aad932',
                                'label' => 'Location Store Locator',
                                'name' => 'location_store_locator',
                                'aria-label' => '',
                                'type' => 'post_object',
                                'instructions' => '',
                                'required' => 0,
                                'conditional_logic' => 0,
                                'wrapper' => array(
                                    'width' => '',
                                    'class' => '',
                                    'id' => '',
                                ),
                                'post_type' => ['wpsl_stores'],
                                'post_status' => 'publish',
                                'return_format' => 'id',
                                'multiple' => '0',
                                'allow_null' => '1',
                                'bidirectional' => '0',
                                'ui' => '1',
                                'bidirectional_target' => [],
                                'parent_repeater' => 'field_63a5b7de91ca7',
                            ),
                            array(
                                'key' => 'field_63db9c3aad933',
                                'label' => 'Book Url',
                                'name' => 'book_url',
                                'aria-label' => '',
                                'type' => 'url',
                                'instructions' => '',
                                'required' => 0,
                                'conditional_logic' => 0,
                                'wrapper' => array(
                                    'width' => '',
                                    'class' => '',
                                    'id' => '',
                                ),
                                'default_value' => '',
                                'placeholder' => '',
                                'parent_repeater' => 'field_63a5b7de91ca7',
                            ),
                        ),
                    )
                ),
                'location' => array(
                    array(
                        array(
                            'param' => 'post_type',
                            'operator' => '==',
                            'value' => 'page',
                        ),
                        array(
                            'param' => 'post_template',
                            'operator' => '!=',
                            'value' => 'location-page.php',
                        ),
                    ),
                ),
                'menu_order' => 0,
                'position' => 'normal',
                'style' => 'default',
                'label_placement' => 'top',
                'instruction_placement' => 'label',
                'hide_on_screen' => '',
                'active' => true,
                'description' => '',
                'show_in_rest' => 0,
            ) );
        endif;
    }
}

$TmmStoreLocatorStatePage = new TmmStoreLocatorStatePage();
