<?php

require 'includes/styles-scripts.php';
require 'includes/primeiv-functions.php';
require 'includes/page-builder.php';
require 'includes/store-locator-state-page.php';
require 'includes/locations-acf-data.php';
require 'includes/locations-module.php';
require 'acf-data/index.php';

function avada_lang_setup() {
	$lang = get_stylesheet_directory() . '/languages';
	load_child_theme_textdomain( 'Avada', $lang );
}
add_action( 'after_setup_theme', 'avada_lang_setup' );



function aicr_add_custom_role() {
	if ( get_option( 'aicr_custom_user_role_1_prd' ) < 1 ) {
		add_role( 'franchise_owner_user_role', 'Franchise Owner', array( 'read' => true, 'edit_pages' => true, 'edit_published_pages' => true, 'level_1' => true ) );
		update_option( 'aicr_custom_user_role_1_prd', 1 );
	}
}
add_action( 'init', 'aicr_add_custom_role' );



/********** WP Store Locator Customization **********/
add_filter( 'wpsl_templates', 'wpsl_primeiv_custom_templates' );
function wpsl_primeiv_custom_templates( $templates ) {
	$templates[] = array (
		'id'   => 'wpslcustomtemplate',
		'name' => 'Custom PrimeIV template',
		'path' => get_stylesheet_directory() . '/' . 'wpsl-templates/custom.php',
	);
	return $templates;
}


add_filter( 'wpsl_meta_box_fields', 'primeiv_custom_meta_box_fields' );
function primeiv_custom_meta_box_fields( $meta_fields ) {
	
	$meta_fields[__( 'Extra', 'wpsl' )] = array(
		'book_now_url' => array(
			'label' => __( 'Book Now (URL)', 'wpsl' )
		)
	);

    if( isset( $meta_fields[__( 'Additional Information', 'wpsl' )] ) ) {
        $meta_fields[__( 'Additional Information', 'wpsl' )]['is_external_url'] = [
            'label' => __( 'External URL', 'wpsl' ),
            'type'  => 'checkbox'
        ];
        $meta_fields[__( 'Additional Information', 'wpsl' )]['piv_coming_soon_location'] = [
            'label' => __( 'Coming Soon', 'wpsl' ),
            'type'  => 'checkbox'
        ];

    }

	return $meta_fields;
}

add_filter( 'comments_open', '__return_false' );
add_filter( 'pings_open', '__return_false' );


add_filter( 'wpsl_frontend_meta_fields', 'primeiv_custom_frontend_meta_fields' );
function primeiv_custom_frontend_meta_fields( $store_fields ) {

	$store_fields['wpsl_piv_coming_soon_location'] = array( 
		'name' => 'piv_coming_soon_location'
	);

	$store_fields['wpsl_book_now_url'] = array( 
		'name' => 'book_now_url'
	);

	$store_fields['wpsl_is_external_url'] = array( 
		'name' => 'is_external_url'
	);
  
	return $store_fields;
}


add_filter( 'wpsl_store_header_template', 'custom_primeiv_store_header_template' );
function custom_primeiv_store_header_template() {
	
	$header_template = '<% if ( wpslSettings.storeUrl == 1 && url ) { %>' . "\r\n";
	$header_template .= '<strong><a href="<%= url %>" target="_self"><% if ( piv_coming_soon_location == 1 ) { %><span class="extra-cs-location-title">COMING SOON: </span><% } %><%= store %></a></strong>' . "\r\n";
	$header_template .= '<% } else { %>' . "\r\n";
	$header_template .= '<strong><% if ( piv_coming_soon_location == 1 ) { %><span class="extra-cs-location-title">COMING SOON: </span><% } %><%= store %></strong>' . "\r\n";
	$header_template .= '<% } %>'; 
	
	return $header_template;
}

add_filter( 'wpsl_listing_template', 'primeiv_custom_listing_template' );
function primeiv_custom_listing_template() {

	global $wpsl, $wpsl_settings;

	$listing_template = '<li data-store-id="<%= id %>">' . "\r\n";
		$listing_template .= "\t\t" . '<div class="wpsl-store-location">' . "\r\n";
		$listing_template .= "\t\t\t" . '<p class="wpsl-contact-details-header"><%= thumb %>' . "\r\n";
		$listing_template .= "\t\t\t\t" . wpsl_store_header_template( 'listing' ) . "\r\n";
		$listing_template .= "\t\t\t\t" . '<span class="wpsl-street"><%= address %></span>' . "\r\n";
		$listing_template .= "\t\t\t\t" . '<% if ( address2 ) { %>' . "\r\n";
		$listing_template .= "\t\t\t\t" . '<span class="wpsl-street"><%= address2 %></span>' . "\r\n";
		$listing_template .= "\t\t\t\t" . '<% } %>' . "\r\n";
		$listing_template .= "\t\t\t\t" . '<span>' . wpsl_address_format_placeholders() . '</span>' . "\r\n";

		if ( !$wpsl_settings['hide_country'] ) {
			$listing_template .= "\t\t\t\t" . '<span class="wpsl-country"><%= country %></span>' . "\r\n";
		}
		
		$listing_template .= "\t\t\t" . '</p>' . "\r\n";


		$listing_template .= "\t\t" . '<div class="wpsl-direction-wrap">' . "\r\n";
		if ( !$wpsl_settings['hide_distance'] ) {
			$listing_template .= "\t\t\t" . '<%= distance %> ' . esc_html( wpsl_get_distance_unit() ) . '' . "\r\n";
		}        
		$listing_template .= "\t\t\t" . '<%= createDirectionUrl() %>' . "\r\n"; 
		$listing_template .= "\t\t" . '</div>' . "\r\n";

		
		if ( $wpsl_settings['show_contact_details'] ) {
			$listing_template .= "\t\t\t" . '<p class="wpsl-contact-details">' . "\r\n";
			$listing_template .= "\t\t\t" . '<% if ( phone ) { %>' . "\r\n";
			$listing_template .= "\t\t\t" . '<span class="wpsl-contact-details-phone"><i class="wpsl-custom-phone-icon"></i> <%= formatPhoneNumber( phone ) %></span>' . "\r\n";
			$listing_template .= "\t\t\t" . '<% } %>' . "\r\n";
			$listing_template .= "\t\t\t" . '<% if ( fax ) { %>' . "\r\n";
			$listing_template .= "\t\t\t" . '<span><strong>' . esc_html( $wpsl->i18n->get_translation( 'fax_label', __( 'Fax', 'wpsl' ) ) ) . '</strong>: <%= fax %></span>' . "\r\n";
			$listing_template .= "\t\t\t" . '<% } %>' . "\r\n";
			$listing_template .= "\t\t\t" . '<% if ( email ) { %>' . "\r\n";
			$listing_template .= "\t\t\t" . '<span class="wpsl-contact-details-email"><i class="wpsl-custom-email-icon"></i> <a href="mailto:<%= formatEmail( email ) %>"><%= formatEmail( email ) %></a></span>' . "\r\n";
			$listing_template .= "\t\t\t" . '<% } %>' . "\r\n";
			$listing_template .= "\t\t\t" . '</p>' . "\r\n";
		}
		
		$listing_template .= "\t\t\t" . wpsl_more_info_template() . "\r\n";
		$listing_template .= "\t\t" . '</div>' . "\r\n";


		$listing_template .= "\t\t" . '<div class="wpsl-page-info-custom-btn-wrap">' . "\r\n";


		
		$listing_template .= "\t\t\t" . '<% if ( piv_coming_soon_location == 1 ) { %>' . "\r\n";
		$listing_template .= "\t\t\t" . '<p><a '. "\r\n";
        $listing_template .= "\t\t\t" . '<% if ( typeof is_external_url !== undefined ) { %>' . "\r\n";
            $listing_template .= "\t\t\t" . '<% if ( is_external_url == 1 ) { %>' . "\r\n";
                $listing_template .= "\t\t\t" . ' target="_blank" ' . "\r\n";
            $listing_template .= "\t\t\t" . '<% } %>' . "\r\n";
        $listing_template .= "\t\t\t" . '<% } %>' . "\r\n";
        $listing_template .= "\t\t\t" . ' class="wpsl-page-info-custom-btn book-now-btn cs" href="<%= url %>">Get Notified When We Open</a></p>' . "\r\n";
        
        
		$listing_template .= "\t\t\t" . '<% } else { %>' . "\r\n";
		


        $listing_template .= "\t\t\t" . '<p><a ' . "\r\n";
        $listing_template .= "\t\t\t" . '<% if ( typeof is_external_url !== undefined ) { %>' . "\r\n";
        $listing_template .= "\t\t\t" . '<% if ( is_external_url == 1 ) { %>' . "\r\n";
        $listing_template .= "\t\t\t" . ' target="_blank"' . "\r\n";
        $listing_template .= "\t\t\t" . '<% } %>' . "\r\n";
        $listing_template .= "\t\t\t" . '<% } %>' . "\r\n";
		$listing_template .= "\t\t\t" . ' href="<%= url %>" class="wpsl-page-info-custom-btn">Visit Location Page</a></p>' . "\r\n";
        
        
        
        
		$listing_template .= "\t\t\t" . '<% if ( book_now_url ) { %>' . "\r\n";
		$listing_template .= "\t\t\t" . '<p><a target="_blank" class="wpsl-page-info-custom-btn book-now-btn" href="<%= book_now_url %>">' . __( 'Book Now', 'wpsl' ) . '</a></p>' . "\r\n";
		$listing_template .= "\t\t\t" . '<% } %>' . "\r\n";



		$listing_template .= "\t\t\t" . '<% } %>' . "\r\n";        



		$listing_template .= "\t\t" . '</div>' . "\r\n";


		$listing_template .= "\t" . '</li>';

	return $listing_template;

}


/**
 * WPSL custom markers
 */
add_filter( 'wpsl_admin_marker_dir', 'primeiv_wpsl_admin_marker_dir' );
function primeiv_wpsl_admin_marker_dir() {

	$admin_marker_dir = get_stylesheet_directory() . '/wpsl-markers/';

	return $admin_marker_dir;
}

define( 'WPSL_MARKER_URI', dirname( get_bloginfo( 'stylesheet_url') ) . '/wpsl-markers/' );


function primeiv_coming_soon_marker( $store_meta, $store_id ) {

	if ( get_post_meta( $store_id, 'wpsl_piv_coming_soon_location', true ) == 1 ) {
		$store_meta['alternateMarkerUrl'] = get_site_url() . '/wp-content/themes/Avada-Child/wpsl-markers/grey@2x.png';
	}

	return $store_meta;
}
add_filter( 'wpsl_store_meta', 'primeiv_coming_soon_marker', 10, 2 );



/* ACF default values */
add_filter('acf/prepare_field/key=field_60dd1e654da6a', 'primeiv_acf_default_image_1');
function primeiv_acf_default_image_1($field) {
	if ($field['value'] === null) {
		$field['value'] = 617;
	}
	return $field;
}

add_filter('acf/prepare_field/key=field_60dd1eb24da6b', 'primeiv_acf_default_image_2');
function primeiv_acf_default_image_2($field) {
	if ($field['value'] === null) {
		$field['value'] = 630;
	}
	return $field;
}

add_filter('acf/prepare_field/key=field_60dd1ebf4da6c', 'primeiv_acf_default_image_3');
function primeiv_acf_default_image_3($field) {
	if ($field['value'] === null) {
		$field['value'] = 616;
	}
	return $field;
}

add_filter('acf/prepare_field/key=field_60dde0f50b472', 'primeiv_acf_default_image_4');
function primeiv_acf_default_image_4($field) {
	if ($field['value'] === null) {
		$field['value'] = 628;
	}
	return $field;
}

add_filter('acf/prepare_field/key=field_60dde1240b473', 'primeiv_acf_default_image_5');
function primeiv_acf_default_image_5($field) {
	if ($field['value'] === null) {
		$field['value'] = 619;
	}
	return $field;
}

add_filter('acf/prepare_field/key=field_60dde1270b474', 'primeiv_acf_default_image_6');
function primeiv_acf_default_image_6($field) {
	if ($field['value'] === null) {
		$field['value'] = 618;
	}
	return $field;
}

add_filter('acf/prepare_field/key=field_60dde12c0b475', 'primeiv_acf_default_image_7');
function primeiv_acf_default_image_7($field) {
	if ($field['value'] === null) {
		$field['value'] = 625;
	}
	return $field;
}

add_filter('acf/prepare_field/key=field_60dde1320b476', 'primeiv_acf_default_image_8');
function primeiv_acf_default_image_8($field) {
	if ($field['value'] === null) {
		$field['value'] = 615;
	}
	return $field;
}

add_filter('acf/prepare_field/key=field_60dde1340b477', 'primeiv_acf_default_image_9');
function primeiv_acf_default_image_9($field) {
	if ($field['value'] === null) {
		$field['value'] = 614;
	}
	return $field;
}


/**
 * Custom html fields
 */
function primeiv_before_closing_head() {
	if ( get_field( 'primeiv_custom_html' ) == 'on' && get_field( 'primeiv_before_head' ) ) {
		echo get_field( 'primeiv_before_head' );
	}
}
add_action( 'wp_head', 'primeiv_before_closing_head', 100 );


function primeiv_after_opening_body() {
	if ( get_field( 'primeiv_custom_html' ) == 'on' && get_field( 'primeiv_after_opening_body' ) ) {
		echo get_field( 'primeiv_after_opening_body' );
	}
}
add_action( 'avada_before_body_content', 'primeiv_after_opening_body', 100 );


function primeiv_before_closing_body() {
	if ( get_field( 'primeiv_custom_html' ) == 'on' && get_field( 'primeiv_before_closing_body' ) ) {
		echo get_field( 'primeiv_before_closing_body' );
	}
}
add_action( 'wp_footer', 'primeiv_before_closing_body', 100 );

/**
 * Prints scripts or data in the head tag on the front end.
 *
 */
function primeiv_action_wp_head() : void {
	?>
	<script>
		jQuery(document).ready(function($) {
          // If click outside of the menu, close it
          $(document).click(function (e) {
            if (
              $(e.target).closest('.fusion-mobile-nav-holder').length === 0
              && $('.fusion-mobile-selector').attr('aria-expanded') === 'true'
            ) {
              $('.fusion-mobile-selector').trigger('click');
            }
          });
        });
	</script>
	<?php
}

add_action( 'wp_head', 'primeiv_action_wp_head' );

if( function_exists('acf_add_options_page') ) {
	
	acf_add_options_page(array(
		'page_title'    => 'PrimeIV Settings',
		'menu_title'    => 'PrimeIV Settings',
		'menu_slug'     => 'primeiv-settings',
		'capability'    => 'edit_posts',
		'redirect'      => false
	));
	
	acf_add_options_sub_page(array(
		'page_title'    => 'Locations Settings',
		'menu_title'    => 'Locations',
		'parent_slug'   => 'primeiv-settings',
	));
	
}

function primeiv_disable_sticky_header( $value, $option_name, $page_option, $post_id ) {

	if ( $option_name === 'header_sticky' && is_page_template( 'location-page.php' ) ) {
		return false;
	}
	return $value;
}

add_filter( 'fusion_get_option', 'primeiv_disable_sticky_header', 10, 4 );

function primeiv_location_back_link() {

	$location_cookie = ( isset( $_COOKIE['location'] ) ) ? $_COOKIE['location'] : false;
    $show = ! is_page_template( 'location-page.php' ) && ! is_page_template( 'page-builder.php' ) && $location_cookie;

	if ( $show ) :
		$location_url  = get_permalink( $location_cookie );
		$location_name = get_field( 'ilpd_location_name', $location_cookie );
	?>
	<script>
		jQuery(document).ready(function($){
			let backLink = (function () {
				const API = {
					init,
				}

				return API;

				function init() {
					buildLink();
					handleClose();
				}

				function buildLink() {
					let wrapper = $("<div></div>",
					{
						class: "location-back-link-wrapper",
						html: `<div class="fusion-builder-row fusion-row">
									<div class="location-back-link-container">
										<a href='<?php echo esc_url( $location_url ); ?>' class='location-back-link'>
											Back to <?php echo wp_kses_post( $location_name ) ?> location page
										</a>
										<button class='location-back-link-close'>
											<i class="fas fa-times"></i>
										</button>
									</div>
							   </div>`
					});
					$(".fusion-header").append(wrapper);
				}

				function handleClose() {
					$('.location-back-link-close').on('click', function() {
						$('.location-back-link-wrapper').remove();
						document.cookie = "location=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
					});
				}
			})();

			backLink.init();
		});
	</script>
	<style>
		.post-content {
			margin-top: -10px;
		}
	</style>
	<?php
	endif;
}

add_action( 'wp_head', 'primeiv_location_back_link' );

function primeiv_set_location_cookie() {
	if ( is_page_template( 'location-page.php' ) ) {
		// Set cookie for 1 day
		setcookie( 'location', get_the_ID(), time() + ( 86400 * 1 ), '/' );
	}
}

add_action( 'wp', 'primeiv_set_location_cookie' );

$treatments_arr = array(
	'ilp_treatments'       => 'global_iv_treatments',
	'ilp_boost_treatments' => 'global_iv_treatment_boosts',
	'ilp_other_treatments' => 'global_other_treatments',
);

foreach ( $treatments_arr as $key => $treatment ) {
	
	add_filter( "acf/load_field/name={$key}", function ( $field ) use ( $treatment ) {
    
		// reset choices
		$field['choices'] = array();
		
		if (have_rows($treatment, 'option')) {
			// while has rows
			while (have_rows($treatment, 'option')) {
				// instantiate row
				the_row();
	
				// vars
				$value = sanitize_title( get_sub_field('treatment_title') );
				$label = wp_strip_all_tags( get_sub_field('treatment_title') );
	
				// append to choices
				$field['choices'][ $value ] = $label;
			}
		}
		
		// return the field
		return $field;
		
	} );
}

/* Exclude Click Funnels From Yoast SEO Sitemap */
function primeiv_sitemap_exclude_post_type( $value, $post_type ) {
	return $post_type === 'clickfunnels';
}
add_filter( 'wpseo_sitemap_exclude_post_type', 'primeiv_sitemap_exclude_post_type', 10, 2 );


/**
 * add post id to Ajax url to be able to override Location Book Now url
 */
add_filter('wpsl_js_settings', function ( $store_meta ){
    if( isset( $store_meta['ajaxurl'] ) ) {
        $store_meta['ajaxurl'] = add_query_arg('primeIvPostId', get_the_ID(), $store_meta['ajaxurl']);
    }
    
    return $store_meta;
});
    


/**
 * Try to override Location Book Now url
 */
add_filter('wpsl_store_data', function ($stores_meta) {
    if ( empty( $stores_meta ) ) {
        return $stores_meta;
    }

    if ( !isset( $_REQUEST['primeIvPostId'] ) ) {
        return $stores_meta;

    }

    $locations_book_button_overrides_raw = primeiv_generate_locations_book_button_override_clean( $_REQUEST['primeIvPostId'] );
    
    if ( !$locations_book_button_overrides_raw ) {
        return $stores_meta;
    }
    foreach ( $stores_meta as $key => $item ) {

        $book_now_new_url = $locations_book_button_overrides_raw[$item['id']] ?? '';
        
        if( $book_now_new_url ) {
            $stores_meta[$key]['book_now_url'] = $book_now_new_url;
        }
    }

    return $stores_meta;
});


function primeiv_generate_locations_book_button_override_clean( $post_id ) {

    if ( !function_exists( 'get_field' ) ) {
        return [];
    }

    $out = [];

    if ( have_rows( 'primeiv_locations_book_button_overrides', $post_id ) ) {
        while ( have_rows( 'primeiv_locations_book_button_overrides', $post_id ) ) {
            the_row();
            $store_locator_post_id = get_sub_field( 'location_store_locator' );
            $book_url = get_sub_field( 'book_url' );
            if (
                $store_locator_post_id
                && $book_url
            ) {
                $out[(int)$store_locator_post_id] = $book_url;
            }
        }

    }

    return $out;
}

/**
 * Disable Auto Load mode to force get not cached data if Location Book Now Button override is used 
 */
add_action('wp_ajax_store_search', 'prime_iv_location_state_page_update_global_setting', 1);
add_action('wp_ajax_nopriv_store_search', 'prime_iv_location_state_page_update_global_setting', 1);
function prime_iv_location_state_page_update_global_setting(){

    if ( !isset( $_REQUEST['primeIvPostId'] ) ) {
        return;

    }
    if ( !function_exists( 'get_field' ) ) {
        return;
    }

    if ( !have_rows( 'primeiv_locations_book_button_overrides', $_REQUEST['primeIvPostId'] ) ) {
        return;
    }

    global $wpsl_settings;
    
    if( isset( $wpsl_settings['autoload']) ) {
        $wpsl_settings['autoload'] = 0;
    }
}


function avada_render_rollover( $post_id, $post_permalink = '', $display_woo_price = false, $display_woo_buttons = false, $display_post_categories = 'default', $display_post_title = 'default', $gallery_id = '', $display_woo_rating = false ) {
    $file_path =  get_stylesheet_directory() . '/templates/custom-function-templates/rollover.php';
    if(  file_exists( $file_path ) ) {
        include $file_path;
    }
}
add_filter('primeiv_archive_title_rollover_title', function ( $title ){
    $max_characters = 32;
    $trimmed_title = (strlen($title) > $max_characters) ? substr($title, 0, $max_characters) : $title; // Trim the title to specified number of characters
    $trimmed_title = rtrim( $trimmed_title );
    $trimmed_title = rtrim($trimmed_title, '&#8211;'); // remove '-' at the end
    $trimmed_title = rtrim($trimmed_title);
    return $trimmed_title . '...';
});


