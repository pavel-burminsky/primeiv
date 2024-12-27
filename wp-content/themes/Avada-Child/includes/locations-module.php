<?php

if ( !defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

class PrimeIVLocationsModule {
    
    private const TRANSIENT_KEY = 'primeiv_locations_per_region';
    private const TRANSIENT_LIFETIME = 2 * HOUR_IN_SECONDS;

    public function __construct() {
        $this->init();
    }

    private function init() {
        add_shortcode( 'primeiv_location_region_columns', [$this, 'primeiv_location_region_columns'] );
        add_shortcode( 'primeiv_location_states', [$this, 'primeiv_location_states'] );
        add_shortcode( 'primeiv_locations_for_state', [$this, 'primeiv_locations_for_state'] );
        add_action('primeiv_location_services_and_prices_top', [$this, 'maybe_print_serving_locations']);
        add_action('tmm_below_store_locator_state', [$this, 'print_locations_as_list']);
    }
    public function primeiv_location_states() {
        $location_states_pages = self::get_location_states_pages();
        if( empty( $location_states_pages ) ) {
            return '';
        }
        ob_start();
        self::print_list($location_states_pages);
        return ob_get_clean();
    }

    private static function print_list($items) {
        ?>
        <div class="primeiv-locations-per-region as-seen-list">
            <ul class="primeiv-locations-per-region__list" style="display: block">
                <?php foreach ( $items as $item ) {
                    ?>
                    <li>
                        <a href="<?php echo esc_url($item['url'] ) ?>"><?php echo $item['post_title'] ?></a>
                    </li>
                    <?php
                } ?>
            </ul>
        </div>
        <?php
    }

    public function print_locations_as_list( $state ) {
        $items = self::get_wpsl_stores_for_state($state);
        if( empty( $items ) ) {
            return;
        }
        self::print_list($items);
    }
    

    public function primeiv_location_region_columns() {
        $regions = self::get_regions_from_acf_fields();
        if ( !$regions ) {
            return '';
        }

        $locations_per_regions = get_transient( self::TRANSIENT_KEY );
        if ( !is_array( $locations_per_regions ) ) {
            $locations_per_regions = self::get_locations_with_regions();
            set_transient( self::TRANSIENT_KEY, $locations_per_regions, self::TRANSIENT_LIFETIME );
        }

        if ( empty( $locations_per_regions ) ) {
            return '';
        }


        ob_start();
        ?>
        <div class="primeiv-locations-per-region">
            <?php
            foreach ( $regions as $region_key => $region_pretty ) {
                if (
                    isset( $locations_per_regions[$region_key] )
                    && !empty( $locations_per_regions[$region_key] )
                ) {
                    ?>
                    <?php self::print_region_ul_with_locations( $region_pretty, $locations_per_regions[$region_key] ); ?>
                    <?php
                }
            }
            ?>
        </div>
        <?php
        return ob_get_clean();
    }

    private static function print_region_ul_with_locations( $region_pretty, $locations ) {
        ?>
        <div class="primeiv-locations-per-region__item">
            <h3 class="primeiv-locations-per-region__title" primeiv-footer-regions-title><?php echo self::prepare_region_title_with_chevron($region_pretty); ?></h3>
            <ul class="primeiv-locations-per-region__list">
                <?php foreach ( $locations as $location ) {
                    ?>
                    <li>
                        <a href="<?php echo esc_url( $location['url'] ) ?>" class="primeiv-locations-per-region__anchor"><?php echo esc_html( self::prepare_location_title( $location['title'] ) ) ?></a>
                    </li>
                    <?php
                } ?>
            </ul>
        </div>
        <?php
    }

    private static function prepare_location_title( $title ) {
        return 'IV Therapy In ' . $title;
    }

    private static function prepare_region_title_with_chevron( $region_pretty ) {
        return esc_html($region_pretty)
        .'<svg width="15" height="9" viewBox="0 0 15 9" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd" clip-rule="evenodd" d="M0.513437 0.638422C0.920239 0.23163 1.57978 0.23163 1.98658 0.638422L7.50001 6.15187L13.0134 0.638422C13.4202 0.23163 14.0798 0.23163 14.4866 0.638422C14.8933 1.04522 14.8933 1.70477 14.4866 2.11157L8.23657 8.36156C7.8298 8.76833 7.17022 8.76833 6.76345 8.36156L0.513437 2.11157C0.106646 1.70477 0.106646 1.04522 0.513437 0.638422Z" fill="#3EA5DB"/>
        </svg>';
    }

    private static function get_regions_from_acf_fields(): ? array {
        if( ! function_exists( 'get_field_object' ) ) {
            return null;
        }
        
        $field = get_field_object(PrimeIVLocationsAcfData::REGION_FIELD_ID);
        
        if( 
            ! isset( $field['choices'] )
            || ! is_array( $field['choices'] )
        ) {
            return null;
        }
        
        return $field['choices'];
    }
    
    private static function get_locations_with_regions(){
        $out = [];
        // todo - add - page template? 
        $posts = get_posts(array(
            'numberposts'   => -1,
            'post_type'     => 'page',
            'meta_key'      => PrimeIVLocationsAcfData::REGION,
            'meta_value'    => '',
            'meta_compare'  => '!=',
            'fields'        => 'ids'
        ));
        
        if( empty( $posts ) ) {
            return $out;
        }
        
        foreach ( $posts as $id ) {
            $title = get_the_title($id);
            $region = get_field( PrimeIVLocationsAcfData::REGION, $id );
            $url = get_permalink($id);
            
            $out[$region][] = [
                'title' => $title,
                'url' => $url,
                'id' => $id
            ];
        }

        return $out;
    }

    public function maybe_print_serving_locations( $location_page_id ) {
        if( ! function_exists( 'get_field_object' ) ) {
            return;
        }
        $serving_areas = get_field( PrimeIVLocationsAcfData::SEVERING_AREAS, $location_page_id );
        if( empty( $serving_areas ) ) {
            return;
        }
        ?>
        <h2 class="primeiv-serving"><?php printf('Serving: %s', trim( $serving_areas )); ?></h2>
        <?php
    }

    private static function get_location_states_pages() {
        $location_pages = get_transient('primeiv_location_pages');
        if( $location_pages ) {
            return $location_pages;
        }
        
        $locations_page_id = self::get_locations_page_id();
        
        if( ! $locations_page_id ) {
            return [];
        }

        global $wpdb;

        $sql_results = $wpdb->get_results(
            $wpdb->prepare(
                "
            SELECT p.ID, p.post_title, p.post_name, pm.meta_value AS state_code
            FROM $wpdb->posts p
            LEFT JOIN $wpdb->postmeta pm
            ON p.ID = pm.post_id
            WHERE p.post_parent = %d
            AND p.post_type = 'page'
            AND p.post_status = 'publish'
            AND pm.meta_key = 'tmm_store_locator_state'
            ORDER BY p.post_title
            ",
             $locations_page_id
            )
        );
        $states_geolocation_keys = array_keys(TmmStoreLocatorStatePage::get_states_geolocation());
        


        $sql_results_clean = array_filter($sql_results, function ( $item ) use ($states_geolocation_keys) {
            return in_array($item->state_code, $states_geolocation_keys);
        });
        
       
        if( empty( $sql_results_clean ) ) {
            return [];
        }

        $location_pages = [];
        foreach ( $sql_results_clean as $item ) {
            if( ! $item->ID ) {
                continue;
            }
            
            $location_page = (array) $item;
            $location_page['url'] = get_permalink($location_page['ID']);
            $location_pages[] = $location_page;
        }
        
        if( empty($location_pages) ) {
            return [];
        }
        
        set_transient('primeiv_location_pages', $location_pages, HOUR_IN_SECONDS);
        return $location_pages;
    }

    private static function get_locations_page_id() {
        $page_id = get_transient('primeiv_locations_page_id');
        if( $page_id ) {
            return $page_id;
        }
        $page = get_page_by_path('locations');
        $page_id = $page ? $page->ID : null;
        
        if( $page_id ) {
            set_transient('primeiv_locations_page_id', $page_id, DAY_IN_SECONDS );
        }
        return $page_id;
    }

    private static function get_wpsl_stores_for_state( $state_code ) {
        $transient_name = 'primeiv_wpsl_stores_for_state__' . $state_code;
        
        $wpsl_stores_for_stat = get_transient($transient_name);
        if( $wpsl_stores_for_stat ) {
            return $wpsl_stores_for_stat;
        }
        

        global $wpdb;

        $results = $wpdb->get_results(
            $wpdb->prepare( "
            SELECT p.ID, p.post_title, pm.meta_value AS state, pm2.meta_value as url
            FROM $wpdb->posts p
            LEFT JOIN $wpdb->postmeta pm
            ON p.ID = pm.post_id
            LEFT JOIN $wpdb->postmeta pm2
            ON p.ID = pm2.post_id
            WHERE p.post_type = 'wpsl_stores'
            AND p.post_status = 'publish'
            AND pm.meta_key = 'wpsl_state'
            AND pm.meta_value = %s
            AND pm2.meta_key = 'wpsl_url'
            ORDER BY p.post_title
            ",
            $state_code
            ), ARRAY_A
        );

        if( ! $results ) {
            return [];
        }
        set_transient($transient_name, $results, HOUR_IN_SECONDS);
        
        return $results;
    }
}

$PrimeIVLocationsModule = new PrimeIVLocationsModule();

// found in interned
//AL 32.6010112 -86.6807365 Alabama
//AK 61.3025006 -158.7750198 Alaska
//AZ 34.1682185 -111.930907 Arizona
//AR 34.7519275 -92.1313784 Arkansas
//CA 37.2718745 -119.2704153 California
//CO 38.9979339 -105.550567 Colorado
//CT 41.5187835 -72.757507 Connecticut
//DE 39.145251 -75.4189206 Delaware
//DC 38.8993487 -77.0145666 District of Columbia
//FL 27.9757279 -83.8330166 Florida 
//GA 32.6781248 -83.2229757 Georgia 
//HI 20.46 -157.505 Hawaii 
//ID 45.4945756 -114.1424303 Idaho 
//IL 39.739318 -89.504139 Illinois 
//IN 39.7662195 -86.441277 Indiana
//IA 41.9383166 -93.389798 Iowa 
//KS 38.4987789 -98.3200779 Kansas 
//KY 37.8222935 -85.7682399 Kentucky 
//LA 30.9733766 -91.4299097 Louisiana 
//ME 45.2185133 -69.0148656 Maine 
//MD 38.8063524 -77.2684162 Maryland 
//MA 42.0629398 -71.718067 Massachusetts 
//MI 44.9435598 -86.4158049 Michigan 
//MN 46.4418595 -93.3655146 Minnesota 
//MS 32.5851062 -89.8772196 Mississippi 
//MO 38.3046615 -92.437099 Missouri 
//MT 46.6797995 -110.044783 Montana 
//NE 41.5008195 -99.680902 Nebraska 
//NV 38.502032 -117.0230604 Nevada 
//NH 44.0012306 -71.5799231 New Hampshire 
//NJ 40.1430058 -74.7311156 New Jersey 
//NM 34.1662325 -106.0260685 New Mexico 
//NY 40.7056258 -73.97968 New York 
//NC 35.2145629 -79.8912675 North Carolina 
//ND 47.4678819 -100.3022655 North Dakota 
//OH 40.1903624 -82.6692525 Ohio 
//OK 35.3097654 -98.7165585 Oklahoma
//OR 44.1419049 -120.5380993 Oregon 
//PA 40.9945928 -77.6046984 Pennsylvania 
//RI 41.5827282 -71.5064508 Rhode Island 
//SC 33.62505 -80.9470381 South Carolina 
//SD 44.2126995 -100.2471641 South Dakota 
//TN 35.830521 -85.9785989 Tennessee 
//TX 31.1693363 -100.0768425 Texas 
//UT 39.4997605 -111.547028 Utah 
//VT 43.8717545 -72.4477828 Vermont 
//VA 38.0033855 -79.4587861 Virginia 
//WA 38.8993487 -77.0145665 Washington 
//WV 38.9201705 -80.1816905 West Virginia 
//WI 44.7862968 -89.8267049 Wisconsin 
//WY 43.000325 -107.5545669 Wyoming