<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}

add_action( 'wp_enqueue_scripts', 'theme_enqueue_styles' );
function theme_enqueue_styles() {
    $front_main_css_location = '/style.css';
    $front_main_css_file_time = filemtime(get_stylesheet_directory() . $front_main_css_location);
    
    wp_enqueue_style( 'child-style', get_stylesheet_directory_uri() . $front_main_css_location, [], $front_main_css_file_time );

    if (is_page_template('location-page.php')) {
        wp_enqueue_script( 'location-match-h', get_stylesheet_directory_uri() . '/js/jquery.matchHeight-min.js', array( 'jquery' ), '1.0.0', true );
    }

    wp_enqueue_style( 'montserrat-gfont', 'https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap' );

    $front_main_js_location = '/js/front-main.js';
    $front_main_js_filemtime = filemtime(get_stylesheet_directory() . $front_main_js_location);
    wp_enqueue_script( 'primeiv-front-main', get_stylesheet_directory_uri() . $front_main_js_location, ['jquery'], $front_main_js_filemtime, true );
}


add_action( 'wp_enqueue_scripts', function (){
    if (
        is_singular( 'page' )
        && is_page_template( 'page-builder.php' )
    ) {
        wp_enqueue_script( 'tooltipster', get_stylesheet_directory_uri() . '/js/tooltipster.main.min.js', ['jquery'], false, true );
    }
}, 5 );


add_action( 'wp_enqueue_scripts', function (){
    if ( is_singular( 'page' ) ) {
        $front_main_css_location = '/front-scss.css';
        $front_main_css_file_time = filemtime(get_stylesheet_directory() . $front_main_css_location);
        wp_enqueue_style( 'primeiv_page_builder', get_stylesheet_directory_uri() . $front_main_css_location, [], $front_main_css_file_time );
    }
}, 5 );
