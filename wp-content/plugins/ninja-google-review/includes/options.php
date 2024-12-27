<?php

$njt_google_reviews = array(
/*
array( 'name' => __( 'General', 'njt-google-reviews' ), 'type' => 'opentab' ),
array(
'name'  => __( 'Google Places API Key', 'njt-google-reviews' ),
'desc'  => sprintf( __( 'API keys are managed through the <a href="%1$s" class="new-window" target="_blank">Google API Console</a>. For more information please <a href="%2$s" class="new-window" target="_blank">review these steps</a>. Or view <a href="%3$s" class="new-window" target="_blank">full video tutorial</a>.', 'njt-google-reviews' ), esc_url( 'https://code.google.com/apis/console/?noredirect' ), esc_url( 'https://developers.google.com/places/web-service/get-api-key' ), esc_url( 'https://ninjateam.org/how-to-setup-google-place-reviews-wordpress-plugin/' ) ),
'std'   => '',
'id'    => 'njt_google_api_key_use',
'type'  => 'text',
'label' => __( 'Yes', 'njt-google-reviews' )
),

array( 'type' => 'closetab', 'actions' => true ),
 */

    array('name' => __('Shortcode', 'njt-google-reviews'), 'type' => 'opentab'),

    array(

        'name' => __('Create shortcode ', 'njt-google-reviews'), 'type' => 'shortcode-form',

    ),

    array('type' => 'closetab', 'actions' => false),

    array('name' => __('Badge', 'njt-google-reviews'), 'type' => 'opentab'),

    array(

        'name' => __('Create badge ', 'njt-google-reviews'), 'type' => 'badge-form',

    ),

    array('type' => 'closetab', 'actions' => false),

    array('type' => 'closetab', 'actions' => false),

/*
array( 'name' => __( 'Google Rich Snippet', 'njt-google-reviews' ), 'type' => 'opentab' ),

array(

'name' => __( 'Create badge ', 'njt-google-reviews' ), 'type' => 'google-rich'

),

array( 'type' => 'closetab', 'actions' => true ),
 */

);
