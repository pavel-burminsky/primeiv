<?php

class Google_Widget_Reviews extends WP_Widget
{

    private $options;
    private $api_key;

    public $widget_fields = array(
        'title' => '',
        'location' => '',
        'reference' => '',
        'place_id' => '',
        'place_type' => '',
        'cache' => '',
        'disable_title_output' => '',
        'widget_style' => 'Minimal Light',
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
    );

    public function __construct()
    {

        parent::__construct(
            'njt_google_review_widget', // Base ID
            'Google Places Reviews', // Name
            array(
                'classname' => 'njt-google-places-reviews',
                'description' => __('Display user reviews for any location found on Google Places.', 'njt-google-reviews'),
            )
        );

        $this->api_key = get_option('njt_google_api_key_use', false);

    }

    public function output_error_message($message, $style)
    {

        switch ($style) {
            case 'error':
                $style = 'njt-error';
                break;
            case 'warning':
                $style = 'njt-warning';
                break;
            default:
                $style = 'njt-warning';
        }

        $output = '<div class="gpr-alert ' . $style . '">';
        $output .= $message;
        $output .= '</div>';

        echo $output;

    }

    public function widget($args, $instance)
    {

        extract($args);

        foreach ($instance as $variable => $value) {

            ${$variable} = !isset($instance[$variable]) ? $this->widget_fields[$variable] : esc_attr($instance[$variable]);
        }

        $transient_unique_id = substr($place_id, 0, 25);
        $response = get_transient('njt_grv_widget_api_' . $transient_unique_id);
        $widget_options = get_transient('njt_grv_widget_options_' . $transient_unique_id);
        $serialized_instance = serialize($instance);
        $cache = strtolower($cache);

        $reviews = new google_api($reference, $place_id);
        $options_by_locationID = get_option($place_id);
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

                set_transient('njt_grv_widget_api_' . $transient_unique_id, $response, $expiration);
                set_transient('njt_grv_widget_options_' . $transient_unique_id, $serialized_instance, $expiration);
            }
        }

        if ((empty($reference) && empty($place_id)) || ($place_id == 'No location set' && $reference == 'No location set')) {

            _e('<strong>INVALID REQUEST</strong>: Please check that this widget has a Google Place ID set.', 'njt-google-reviews');

            return false;
        }

        echo $before_widget;
        echo $before_title . $title . $after_title;
        $disshahow = $shadow == 'yes' ? '' : 'njt-disabled-shadow';
        //UPDATE 25-02-2019
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
        // echo '<div class="njt-google-places-reviews-wap ' . $disshahow . '">';
        $carousel_autoplay = $carousel_autoplay == 'yes' ? true : false; 
        $carousel_speed = $carousel_speed ? $carousel_speed : '3000';  
        echo "<div class='njt-google-places-reviews-wap $disshahow' data-id='$place_id' data-carousel-autoplay='$carousel_autoplay' data-carousel-speed='$carousel_speed'>";
        if (count($postReviews) > 0) {
            include NJT_PLUGIN_PATH . '/includes/views/widget-posttype-fontend.php';
        } else {
            $link_location = add_query_arg(array('page' => 'njt-ggreviews-locations'), admin_url('admin.php'));
            _e('<strong>INVALID REQUEST</strong>: No reviews found, please check with settings Place ID again <a href="' . $link_location . '">here</a>.', 'njt-google-reviews');
            // $reviews->delete_transient_cache($transient_unique_id);

        }
        echo '</div>';
        echo !empty($after_widget) ? $after_widget : '</div>';
    }

    public function update($new_instance, $old_instance)
    {
        $instance = $old_instance;
        //loop through options array and save to new instance
        foreach ($this->widget_fields as $field => $value) {
            if (isset($new_instance[$field])){
                $instance[$field] = strip_tags(stripslashes($new_instance[$field]));
            } else{
                $instance[$field] = "";
            }
        }

        return $instance;
    }

    public function form($instance)
    {

        if (!isset($this->api_key) || empty($this->api_key)) {

            $api_key_error = sprintf(__('<p><strong>Notice: </strong>No Google Places API key detected. You will need to create an API key to use Google Places Reviews. API keys are managed through the <a href="%1$s" class="new-window" target="_blank">Google API Console</a>. For more information please see <a href="%2$s"  target="_blank"  class="new-window" title="Google Places API Introduction">this article</a>.</p> <p>Once you have obtained your API key enter it in the <a href="%3$s" title="Google Places Reviews Plugin Settings">plugin settings page</a>.</p>', 'njt-google-reviews'), esc_url('https://code.google.com/apis/console/?noredirect'), esc_url('https://developers.google.com/places/documentation/#Authentication'), admin_url('/admin.php?page=googlereivews'));
            echo $api_key_error;
            return;
        } else {

            foreach ($this->widget_fields as $field => $value) {

                ${$field} = !isset($instance[$field]) ? $value : esc_attr($instance[$field]);

            }

            include NJT_PLUGIN_PATH . '/includes/views/widget-form.php';
        }

    }

}
