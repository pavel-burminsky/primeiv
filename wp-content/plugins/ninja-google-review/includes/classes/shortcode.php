<?php

if (!defined('ABSPATH')) {
    die('-1');
}

/**
 *
 */
class google_place_shortcode
{

    public function __construct()
    {
        add_action('init', array($this, 'shortcode'));

    }

    public function shortcode()
    {

        add_shortcode('njt-gpr', function ($atts, $content = '') {

            $args = shortcode_atts(array(
                //'title'                => '',
                'location' => '',
                'reference' => '',
                'place_id' => '',
                'place_type' => '',
                'cache' => '',
                'disable_title_output' => '',
                'review_filter' => '',
                'review_limit' => '5',
                'review_characters' => '20',
                'hide_header' => '',
                'hide_out_of_rating' => '',
                'hide_google_image' => '',
                'target_blank' => '1',
                'no_follow' => '1',
                'column' => '1',
                'carousel' => '',
                'carousel_autoplay' => '',
                'carousel_speed' => '',
                'shadow' => 'disabled',
                'btn_write' => 'Write a review',
                'show_write_btn' => '',
            ), $atts);

            extract($args);

            $reviews = new google_api($reference, $place_id);

            $transient_unique_id = substr($place_id, 0, 25);
            $response = get_transient('njt_grv_shortcode_api_' . $transient_unique_id);
            $widget_options = get_transient('njt_grv_shortcode_options_' . $transient_unique_id);

            $serialized_instance = serialize($args);
            $cache = strtolower($cache);
            if ($cache !== 'none') {
                if ($response === false || $serialized_instance !== $widget_options) {
                    $expiration = $cache;
                    switch ($expiration) {
                        case '1 hour':
                            $expiration = 3600;
                            break;
                        case '3 hours':
                            $expiration = 3600 * 3;
                            break;
                        case '6 hours':
                            $expiration = 3600 * 6;
                            break;
                        case '12 hours':
                            $expiration = 60 * 60 * 12;
                            break;
                        case '1 day':
                            $expiration = 60 * 60 * 24;
                            break;
                        case '2 days':
                            $expiration = 60 * 60 * 48;
                            break;
                        case '1 week':
                            $expiration = 60 * 60 * 168;
                            break;
                    }

                    set_transient('njt_grv_shortcode_api_' . $transient_unique_id, $response, $expiration);
                    set_transient('njt_grv_shortcode_options_' . $transient_unique_id, $serialized_instance, $expiration);
                }
            }

            $options_by_locationID = get_option($place_id);

            $review_limit = (int) $review_limit;

            $args_post_reviews = array(
                'posts_per_page' => -1,
                'post_status' => array('publish', 'pending', 'future'),
                'post_type' => 'njt_google_reviews',
                'meta_query' => array(
                    'relation' => 'AND',
                    array(
                        'key' => 'location_id',
                        'value' => $place_id,
                        'compare' => '=',
                    ),
                ),
                'meta_key' => 'time', // use order by time (time is post_meta)
                'orderby' => 'meta_value_num', // use order by time (time is post_meta)
                'order' => 'DESC', // use order by time (time is post_meta)
            );
            $args_post_reviews = apply_filters('njt_ggrv_get_reviews', $args_post_reviews);
            $postReviews = get_posts($args_post_reviews);
            //
            if ((empty($reference) && empty($place_id)) || ($place_id == 'No location set' && $reference == 'No location set')) {

                _e('<strong>INVALID REQUEST</strong>: Please check that this widget has a Google Place ID set.', 'njt-google-reviews');
            }

            ob_start();
            if (count($postReviews) > 0) {
                include NJT_PLUGIN_PATH . '/includes/views/shortcode-detail-update.php';
            } else {
                $link_location = add_query_arg(array('page' => 'njt-ggreviews-locations'), admin_url('admin.php'));
                _e('<strong>INVALID REQUEST</strong>: No reviews found, please check with settings Place ID again <a href="' . $link_location . '">here</a>.', 'njt-google-reviews');
            }

            $result = ob_get_contents();
            ob_end_clean();
            $content .= $result;
            return $content;
        });
    }

}

new google_place_shortcode();
