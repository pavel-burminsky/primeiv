<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

function fusion_get_option( $option_name, $page_option = false, $post_id = false ) {

	$value       = '';
	$value_found = false;
	$id          = Fusion::get_instance()->get_page_id();
	$is_archive  = false === $post_id ? ( false !== strpos( $id, 'archive' ) || false === $id ) : false !== strpos( $post_id, 'archive' );
	$map         = Fusion_Options_Map::get_option_map();
	$edit_post   = false;

	// Admin check for edit post screen.
	if ( is_admin() && false === $id && isset( $_GET['post'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
		$id        = (int) sanitize_text_field( wp_unslash( $_GET['post'] ) ); // phpcs:ignore WordPress.Security.NonceVerification
		$edit_post = true;
	}

	/**
	 * Tweak for the "mobile_header_bg_color" option.
	 */
	if ( 'mobile_archive_header_bg_color' === $option_name && ( ! is_archive() || fusion_is_shop( $id ) ) ) {
		$option_name = 'mobile_header_bg_color';
	}

	/**
	 * Tweak for blog "page_title_bar".
	 */
	if ( 'page_title_bar' === $option_name && 'post' === get_post_type( $id ) ) {
		$option_name = 'blog_page_title_bar';
	}

	if ( false === strpos( $option_name, '[' ) ) {
		$option_name_located = Fusion_Options_Map::get_option_name_from_theme_option( $option_name );
		if ( is_array( $option_name_located ) ) {
			$value = [];
			foreach ( $option_name_located as $key => $option_id ) {
				$value[ $key ] = fusion_get_option( $option_id );
			}
		}
	}

	/**
	 * Get term options.
	 * Overrides page-option & theme-option.
	 */
	if ( $is_archive ) {
		$tax_value = fusion_data()->term_meta( intval( $id ) )->get( $option_name );
		if ( null !== $tax_value && '' !== $tax_value ) {
			$value_found = true;
			$value       = $tax_value;
		}
	}

	$post_id = apply_filters( 'fusion_get_option_post_id', ( $post_id ) ? $post_id : $id );

	// If $post_id is not set that means there is a call for a TO and it is still to early for post ID to be set.
	if ( false === $post_id ) {
		$skip = true;
	} else {
		$post_meta = fusion_data()->post_meta( $post_id );

		// Make sure this is not an override that should not be happening.
		// See https://github.com/Theme-Fusion/Avada/issues/8122 for details.
		$skip = (
			( '' === $post_meta->get( 'header_bg_image[url]' ) && in_array( $option_name, [ 'header_bg_repeat', 'header_bg_full' ], true ) ) ||
			( '' === $post_meta->get( 'bg_image[url]' ) && ( 'bg_repeat' === $option_name || 'bg_full' === $option_name ) ) ||
			( '' === $post_meta->get( 'content_bg_image[url]', $post_id ) && in_array( $option_name, [ 'content_bg_repeat', 'content_bg_full' ], true ) )
		);
	}

	/**
	 * Get page options.
	 * Overrides theme-option.
	 */
	$get_page_option = apply_filters( 'fusion_should_get_page_option', ( is_singular() || fusion_is_shop( $post_id ) || ( is_home() && ! is_front_page() ) || $edit_post || ( false !== $post_id && ! $is_archive ) ) );

	if ( ! $value_found && ! $skip && $get_page_option ) {

		// Get the page-option.
		$page_option = $post_meta->get( $option_name );

		if ( 'default' !== $page_option && false !== $page_option && '' !== $page_option && null !== $page_option ) {
			$value_found = true;
			$value       = $page_option;
		}

		// Tweak for sidebars options.
		$sidebars_options = [
			'pages_sidebar',
			'pages_sidebar_2',
			'posts_sidebar',
			'posts_sidebar_2',
			'portfolio_sidebar',
			'portfolio_sidebar_2',
			'woo_sidebar',
			'woo_sidebar_2',
			'ec_sidebar',
			'ec_sidebar_2',
			'ppbress_sidebar',
			'ppbress_sidebar_2',
		];

		if ( '' === $page_option && in_array( $option_name, $sidebars_options, true ) ) {
			$value_found = true;
			$value       = $page_option;
		}

		// Tweak for show_first_featured_image.
		if ( 'show_first_featured_image' === $option_name && '' === $page_option && 'avada_portfolio' !== get_post_type( $post_id ) ) {
			$value_found = true;
			$value       = true;
		}
	}

	// Get the theme-option value if we couldn't find a value in page-options or taxonomy-options.
	if ( ! $value_found ) {

		/**
		 * Get the Global Options.
		 */
		$option_name = Fusion_Options_Map::get_option_name( $option_name, 'theme' );
		$value       = fusion_get_theme_option( $option_name );
	}

	// Tweak values for the "page_title_bar" option - TOs and POs have different formats.
	if ( 'page_title_bar' === $option_name || 'blog_page_title_bar' === $option_name ) {
		$value = strtolower( $value );
		$value = 'yes' === $value ? 'bar_and_content' : $value;
		$value = 'yes_without_bar' === $value ? 'content_only' : $value;
		$value = 'no' === $value ? 'hide' : $value;
	}

	// Tweak values for the "page_title_bar_bs" option - TOs and POs have different formats.
	if ( 'page_title_bar_bs' === $option_name ) {
		$value = strtolower( $value );
		$value = 'searchbar' === $value ? 'search_box' : $value;
	}

	/**
	 * Apply mods for options.
	 */
	if ( is_string( $option_name ) && isset( $map[ $option_name ] ) && isset( $map[ $option_name ]['is_bool'] ) && true === $map[ $option_name ]['is_bool'] ) {
		$value = ( '1' === $value || 1 === $value || true === $value || 'yes' === $value );
	}
	return apply_filters( 'fusion_get_option', $value, $option_name, $page_option, $post_id );
}

function primeiv_available_treatment_box( $treatment, $css_classes = '', $treatments_group = 'global_iv_treatments') {
	$treatments = get_field( $treatments_group, 'option' );
	foreach ( $treatments as $t ) {
		$title_slug = sanitize_title( $t['treatment_title'] );
		if ( $title_slug == $treatment ) {
			ob_start();
			?>
			<div class="treatment-item <?php echo $css_classes; ?>">
				<div class="icon-wrapper">
					<div class="treatment-icon">
						<?php 
						$image = $t['treatment_icon'];
						$size  = 'full';
						if( $image ) {
							echo wp_get_attachment_image( $image['id'], $size );
						}
						?>
						<div class="treatment-icon-title"><?php echo $t['treatment_title'] ?></div>
					</div>
				</div>
				<div class="treatment-info">
					<?php echo wp_kses( $t['treatment_description'], [ 'strong' => [] ] ); ?>
				</div>
			</div>
			<?php
			echo ob_get_clean();
		}
	}
}

function primeiv_get_default_available_treatments( $treatments_group = 'global_iv_treatments' ) {
	$treatments = ( get_field( $treatments_group, 'option' ) ) ? get_field( $treatments_group, 'option' ) : [];

	return array_map( function( $treatment ) {
		return sanitize_title( $treatment['treatment_title'] );
	}, $treatments );
}

function primeiv_get_packages() {
    if( ! function_exists('get_field') ) {
        return [];
    }

	$packages_ids = [
        'vci',
        'gbb',
        'ni',
        'wlp',
        'mip',
    ];

    foreach ( $packages_ids as $i => $id ) {
        $disabled_field_key = sprintf( 'location_%s_package_disabled', $id );
        if( get_field($disabled_field_key) ) {
            unset($packages_ids[$i]);
        }
    }

    $packages = [];
	foreach ($packages_ids as $package_id) {
		$package_data = [
            'iv_global_package_title'       => get_field( "location_{$package_id}_package_title" )
                ? get_field( "location_{$package_id}_package_title" )
                : get_field( "global_{$package_id}_package_title", "option" ),
            'iv_global_package_features'    => get_field( "location_{$package_id}_package_features" )
                ? get_field( "location_{$package_id}_package_features" )
                : get_field( "global_{$package_id}_package_features", "option" ),
            'iv_global_package_description' => get_field( "location_{$package_id}_package_description" )
                ? get_field( "location_{$package_id}_package_description" )
                : get_field( "global_{$package_id}_package_description", "option" ),
            'iv_global_package_notes'       => get_field( "location_{$package_id}_package_notes" )
                ? get_field( "location_{$package_id}_package_notes" )
                : get_field( "global_{$package_id}_package_notes", "option" ),
        ];

		$packages[] = $package_data;
	}

	return $packages;
}

function primeiv_price_tooltip( $name, $global ) {
	$tooltip = '';

	if ( is_array( $global ) ) {
		foreach( $global as $item ) {
			if ( $name == $item['name'] ) {
				$tooltip = $item['tooltip'];
			}
		}
	}

	return $tooltip;
}

function primeiv_footer_hours_table( $appointment_hours ) {
    if ( empty( $appointment_hours ) ) {
        return;
    }
    ?>
    <table>
        <tbody>
        <?php
        $grouped_by_time_clean_weekdays = primeiv_footer_hours_table_group_nearest_working_days_by_same_time( $appointment_hours );
        foreach ( $grouped_by_time_clean_weekdays as $chunk ) {
            foreach ( $chunk as $time => $grouped_working_days ) {
                primeiv_footer_hours_table_tr( $grouped_working_days, $time );
            }
        }
        ?>
        </tbody>
    </table>
    <?php
}

function primeiv_footer_hours_table_tr( $td_1, $td_2 ) {
    ?>
    <tr>
        <td><strong><?php echo $td_1; ?></strong></td>
        <td><?php echo $td_2; ?></td>
    </tr>
    <?php
}


function primeiv_footer_hours_table_group_nearest_working_days_by_same_time($appointment_hours) {
    $fields_postfix_to_weekdays = primeiv_get_footer_hours_table_field_postfix_to_weekday();
    $field_postfix_to_time = [];
    foreach ( $fields_postfix_to_weekdays as $field_postfix => $weekday ) {
        if( ! isset( $appointment_hours['ilp_location_hours_ap_' . $field_postfix] ) ) { continue; }
        $field_postfix_to_time[$field_postfix] = $appointment_hours['ilp_location_hours_ap_' . $field_postfix];
    }

    $grouped_by_time = [];
    $i = 0;
    $same_time = '';
    foreach ( $field_postfix_to_time as $field_postfix => $time ) {
        if( $same_time !== $time ) {
            $i++;
        }

        $grouped_by_time[$i][$time][] = $field_postfix;
        $same_time = $time;
    }
    
    $grouped_by_time_clean_weekdays = [];
    foreach ( $grouped_by_time as $i => $chunk ) {
        foreach ( $chunk as $same_time => $field_postfixes ) {
            $days_count = count( $field_postfixes );
            if( $days_count === 1 ) {
                $grouped_by_time_clean_weekdays[$i][$same_time] = $fields_postfix_to_weekdays[$field_postfixes[0]];
            } else {
                $grouped_by_time_clean_weekdays[$i][$same_time] = $fields_postfix_to_weekdays[$field_postfixes[0]] . ' - ' . $fields_postfix_to_weekdays[$field_postfixes[$days_count - 1]];
            }
        }
    }
    return $grouped_by_time_clean_weekdays;
}

function primeiv_get_footer_hours_table_field_postfix_to_weekday() {
    return [
        'mon' => 'Mon',
        'tue' => 'Tue',
        'wed' => 'Wed',
        'thu' => 'Thu',
        'fri' => 'Fri',
        'sat' => 'Sat',
        'sun' => 'Sun',
    ];
}


function primeiv_is_location_coming_soon() {
    $opening_date = primeiv_get_location_opening_date();
    
    if( empty($opening_date) ) {
        return false;
    }
    
    return time() < strtotime($opening_date);
}

function primeiv_get_location_opening_date( $only_year = false ) {
    $opening_date = get_field('cs_opening_date');
    
    if( ! $opening_date ) {
        return '';
    }

    return $only_year
        ? date('Y', strtotime($opening_date))
        : $opening_date;
    
}

function primeiv_print_location_map( $change_size_for_sidebar = false ){
    if( $map_iframe = get_field( 'ilpd_map_code' ) ) {
        if( $change_size_for_sidebar ) {
            $search = ['width="600"', 'height="450"'];
            $replace = ['width="260"', 'height="251"'];
            $map_iframe = str_replace($search, $replace, $map_iframe);
        }
        get_template_part( 'template-parts/location/map','', ['map_iframe' => $map_iframe] );
    }
}

function primeiv_print_location_phone_link( $svg_instead_of_icon = false ) {
    if ( $phone = get_field( 'ilp_phone_number' ) ) : ?>
        <div class="location-info-phone">
            <?php if ( $svg_instead_of_icon ) {
                ?>
                <svg width="14" height="15" viewBox="0 0 14 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd"
                          d="M12.8768 9.68944C12.0197 9.68944 11.1781 9.55487 10.3799 9.29193C9.98965 9.15839 9.50932 9.28158 9.27019 9.52588L7.69669 10.7143C5.8706 9.74017 4.74638 8.61698 3.78468 6.80539L4.93789 5.27226C5.2381 4.97205 5.34576 4.5352 5.21739 4.12526C4.95169 3.31858 4.81679 2.47457 4.81781 1.62526C4.81781 1.00518 4.31366 0.501038 3.69462 0.501038H1.12319C0.504141 0.501038 0 1.00414 0 1.62423C0 8.7236 5.7764 14.5 12.8768 14.5C13.4959 14.5 14 13.9959 14 13.3768V10.8126C14 10.1936 13.4959 9.68944 12.8768 9.68944Z"
                          fill="#53ACE0"/>
                </svg>
                <?php
            } else {
                ?>
                <i class="location-info-phone-icon"></i>
                <?php
            } ?>
            <a href="tel:<?php echo $phone; ?>"><?php echo $phone; ?></a>
        </div>
    <?php endif;
}

function primeiv_print_location_email_link( $email_us_title = false, $svg_instead_of_icon = false ) {
    if ( $email = get_field( 'ilp_email' ) ) : ?>
        <div class="location-info-email">
            <?php if ( $svg_instead_of_icon ) {
                ?>
                <svg width="18" height="15" viewBox="0 0 18 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd"
                          d="M17.6585 5.20783C17.6896 5.18375 17.7269 5.16882 17.766 5.16471C17.8052 5.1606 17.8447 5.16748 17.8801 5.18456C17.9156 5.20165 17.9456 5.22828 17.9667 5.26145C17.9879 5.29463 17.9994 5.33305 18 5.3724V12.5621C18 13.494 17.244 14.249 16.3131 14.249H1.68686C0.757029 14.25 0 13.495 0 12.5631V5.37651C0 5.20166 0.200571 5.10189 0.341486 5.21194C1.12834 5.82291 2.17234 6.60051 5.75897 9.20486C6.49954 9.74589 7.75234 10.8855 9 10.8783C10.2549 10.8886 11.5303 9.72532 12.2451 9.20486C15.8307 6.59949 16.8717 5.81983 17.6585 5.20783ZM9 9.75C9.81566 9.7644 10.9903 8.72349 11.5807 8.29457C16.2453 4.90851 16.6001 4.61331 17.677 3.76989C17.7779 3.691 17.8594 3.59015 17.9153 3.475C17.9713 3.35986 18.0003 3.23346 18 3.10543V2.43686C18 1.506 17.244 0.75 16.3131 0.75H1.68686C0.757029 0.75 0 1.506 0 2.43686V3.10543C0 3.36566 0.119314 3.6084 0.322971 3.76989C1.39886 4.61023 1.75474 4.90851 6.41931 8.29457C7.00972 8.72349 8.18434 9.7644 9 9.75Z"
                          fill="#37A4DD"/>
                </svg>
                <?php
            } else {
                ?>
                <i class="location-info-email-icon"></i>
                <?php
            } ?>
            <a href="mailto:<?php echo $email; ?>"><?php echo $email_us_title ? 'Email Us' : $email; ?></a>
        </div>
    <?php endif;
}

function primeiv_print_location_address( $hide_icon = false ) {
    if ( $address = get_field( 'ilp_address' ) ) : ?>
        <div class="location-info-address">
            <?php if ( !$hide_icon ) {
                ?>
                <i class="location-info-address-icon"></i>
                <?php
            } ?>
            <p><?php echo $address; ?></p>
        </div>
    <?php endif;
}






