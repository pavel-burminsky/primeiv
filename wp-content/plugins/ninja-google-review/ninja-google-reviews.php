<?php
/*
 * Plugin Name: Ninja Google Place Reviews Pro
 * Plugin URI: http://ninjateam.org
 * Description: Google Places Reviews help you display your business reviews and ratings on your website. Keep your business expertise trusted.
 * Version: 2.4.1
 * Author: NinjaTeam
 * Author URI: http://ninjateam.org
 */
if (!defined('ABSPATH')) {
    die('-1');
}

if (!class_exists('njt_google_reviews')) {
    class njt_google_reviews
    {
        protected static $_instance = null;
        private $location_html = "";
        private static $post_type = 'njt_google_reviews';
        public static function instance()
        {
            if (is_null(self::$_instance)) {
                self::$_instance = new self();
            }
            return self::$_instance;
        }
        public function __construct()
        {

            $this->location_html .= '<div class="njt_gg_reviews_add_location_wrap js-add-new-location" data-location_id="%1$s">';
            $this->location_html .= '<input name="njt_gg_reviews_location[location_look][]" class="njt-location-autocomplete-snippet regular-text" id="njt-autocomplete-snippet"
            name="" type="text"/>';
            $this->location_html .= '<input type="hidden" class="location-rich regular-text"  readonly id="location-rich" name="njt_gg_reviews_location[location_name][]" type="text" value="%1$s"  placeholder="No location set"/>';
            $this->location_html .= '<input type="hidden" class="njt_google_place_id regular-text" readonly id="njt_google_place_id" name="njt_gg_reviews_location[place_id][]" type="text" placeholder="No location set " value=""/>';
            $this->location_html .= '<div class="njt_google_reviews_add_business_btns">';
            $this->location_html .= '%2$s';
            $this->location_html .= '<a href="#" class="button njt-gg-review-remove-location">' . __('Remove', 'njt-google-reviews') . '</a>';
            $this->location_html .= '</div>';
            $this->location_html .= '</div>';
            //PlaceID
            $this->placeID_html = "";
            $this->placeID_html .= '<div class="njt_gg_reviews_add_location_wrap js-add-new-placeid" data-location_id="%1$s">';
            $this->placeID_html .= '<span class="njt_ggrv_find_name_span" style="color:green;"></span>';
            $this->placeID_html .= '<input name="njt_gg_reviews_location[place_id][]" class="regular-text njt_ggrv_placeID" id="njt-autocomplete-snippet"
            name="" type="text"/>';
            $this->placeID_html .= '<input type="hidden" class="location-rich regular-text njt_ggrv_location_name_by_placeID"  readonly id="location-rich" name="njt_gg_reviews_location[location_name][]" type="text" value="%1$s"  placeholder="No location set"/>';
            $this->placeID_html .= '<input type="hidden" class="njt_google_look_name_by_place_id regular-text" readonly name="njt_gg_reviews_location[location_look][]" type="text" placeholder="No Place ID set " value=""/>';
            $this->placeID_html .= '<div class="njt_google_reviews_add_business_btns">';
            $this->placeID_html .= '%2$s';
            $this->placeID_html .= '<a href="#" class="button njt-gg-review-remove-location">' . __('Remove', 'njt-google-reviews') . '</a>';
            $this->placeID_html .= '</div>';
            $this->placeID_html .= '</div>';
            //
            add_action('plugins_loaded', array($this, 'load_plugin_textdomain'));
            add_action('admin_init', array($this, 'register_my_setting'));
            add_action('widgets_init', array($this, 'Settup'));
            add_action('admin_menu', array($this, 'MenuGroup'));
            add_action('admin_menu', array($this, 'custion_menu'), 11);
            add_action('admin_enqueue_scripts', array($this, 'admin_widget_scripts'), 600);
            add_action('wp_enqueue_scripts', array($this, 'widget_scripts'));
            add_action('wp_enqueue_scripts', array($this, 'widget_scripts'));
            $home_url = home_url();
            $plugins_url = plugins_url();
            if (!defined('NJT_PLUGIN_PATH')) {
                define('NJT_PLUGIN_PATH', untrailingslashit(plugin_dir_path(__FILE__)));
            }
            if (!defined('NJT_PLUGIN_URL')) {
                define('NJT_PLUGIN_URL', $plugins_url . '/' . basename(plugin_dir_path(__FILE__)));
            }
            if (!defined('NJT_PLUGIN_DIR')) {
                define('NJT_PLUGIN_DIR', plugin_dir_path(__FILE__));
            }


            // === UPDATE 22-02-2019 === //
            /*
             * Register AJAX
             */
            add_action('wp_ajax_njt_gg_review_delete_reviews', array($this, 'ajaxDeleteReviews'));
            add_action('wp_ajax_njt_gg_review_get_new_reviews', array($this, 'ajaxGetNewReviews'));
            add_action('wp_ajax_njt_gg_review_get_name_by_place_id', array($this, 'ajaxGetNameByPlaceID'));
            //
            add_action('init', array($this, 'registerCustomPostType'));
            /*
             * Remove subsubsub
             */
            add_filter('views_edit-' . self::$post_type, array($this, 'removeSubSubSub')); // this filte use delete text (All (0))
            add_filter('pre_get_posts', array($this, 'njtggReviews_OrderByCustomPostType')); // show custom order by with post_meta
            /*
             * Add metabox
             */
            add_action('add_meta_boxes', array($this, 'registerMetaBoxes'));
            /*
             * Create filter for custom post type
             */
            add_action('restrict_manage_posts', array($this, 'restrictManagePosts'));
            add_filter('parse_query', array($this, 'parseQuery'));
            //add colum
            add_filter('manage_' . self::$post_type . '_posts_columns', array($this, 'njtGgooleReviewsAdd_columns'));
            // Let WordPress know to use our filter
            add_filter('manage_' . self::$post_type . '_posts_custom_column', array($this, 'njtGgooleReviewsShowContent'));
            // UPDATE REVIEW USSE CRONJOB
            //and make sure it’s called whenever WordPress loads
            register_activation_hook(__FILE__, array($this, 'googlereviews__activation'));
            //schedule event disable when plugin deactivation
            register_deactivation_hook(__FILE__, array($this, 'googlereviews_cronstarter_deactivation'));
            add_action('init', array($this, 'googlereviews_cronstarter_activation'));

            // define cronjob for task
            if (get_option("google_reviews_schedule_on_off") && get_option('googlereviews_schedule_value') && get_option('googlereviews_schedule_update')) {
                add_filter('cron_schedules', array($this, 'googlereviews_cron_add_defined'), 3, 2); // 1 : priority (default 10), 2: The number of arguments the function accepts.
                add_action('googlereview_cron_action', array($this, 'ggreviews_excute_update_reminder'));
            }

            // === UPDATE 22-02-2019 === //
            //add_action('init', array($this, 'njtGGREVIEWINIT'));
        }

        public function load_plugin_textdomain()
        {
            $current_user = wp_get_current_user();

            if (!($current_user instanceof WP_User)) {
                return;
            }

            if (function_exists('get_user_locale')) {
                $language = get_user_locale($current_user);
            } else {
                $language = get_locale();
            }
            load_textdomain("njt-google-reviews", NJT_PLUGIN_PATH . '/languages/' . $language . '.mo');
        }

        public function custion_menu()
        {
            global $submenu;
            if (isset($submenu['minja-team-menu'])) {
                unset($submenu['minja-team-menu'][0]);
            }
        }

        public function widget_scripts()
        {
            wp_register_style('njt_google_views', NJT_PLUGIN_URL . '/assets/frontend/css/google-reviews.css');
            wp_enqueue_style('njt_google_views');
            wp_register_style('njt_google_slick', NJT_PLUGIN_URL . '/assets/frontend/slick/slick.css');
            wp_enqueue_style('njt_google_slick');
            wp_register_script('njt_google_rv_slick', NJT_PLUGIN_URL . '/assets/frontend/slick/slick.min.js', array('jquery'));
            wp_enqueue_script('njt_google_rv_slick');
            wp_register_script('njt_google_rv', NJT_PLUGIN_URL . '/assets/frontend/js/google-review.js', array('jquery'));
            wp_enqueue_script('njt_google_rv');
        
            wp_localize_script('njt_google_rv', 'njt_ggreviews', array(
                'read_more' => __('Read more', 'njt-google-reviews'),
                'read_less' => __('Read less', 'njt-google-reviews'),
            ));
        }
        public function admin_widget_scripts()
        {
            $api_key = get_option('njt_google_api_key_use');
            $wp_get_theme = wp_get_theme()->get( 'Name' );
            if("Enfold" == $wp_get_theme) {
                wp_dequeue_script('avia-google-maps-api');
            }
            if (isset($api_key)) {
                wp_register_script('njt_google_places_gmaps', 'https://maps.googleapis.com/maps/api/js?libraries=places&key=' . $api_key, array('jquery'));
                wp_enqueue_script('njt_google_places_gmaps');
            }
            wp_register_style('njt_google_places_gmaps_find_css', NJT_PLUGIN_URL . '/assets/backend/css/style.css');
            wp_enqueue_style('njt_google_places_gmaps_find_css');
            wp_register_script('njt_google_places_gmaps_find', NJT_PLUGIN_URL . '/assets/backend/js/find-id-google-place.js', array('jquery'));
            wp_enqueue_script('njt_google_places_gmaps_find');
            wp_enqueue_script('njt_core', NJT_PLUGIN_URL . '/assets/backend/js/core.js', array('jquery'), false, true);
            wp_enqueue_script('njt_location', NJT_PLUGIN_URL . '/assets/backend/js/location.js', array('jquery'), false, true);
            wp_localize_script('njt_core', 'njt_ajax_object', array(
                'ajax_url' => admin_url('admin-ajax.php'),
            ));

            wp_localize_script('njt_location', 'njt_ggreviews_location_settings', array(
                'nonce' => wp_create_nonce("njt_ggreviews_location_settings"),
                'nonce_mess' => __('Got errors with nonce, please refresh and try again.', 'njt-google-reviews'),
                'location_html' => sprintf($this->location_html, '', ''),
                'placeID_html' => sprintf($this->placeID_html, '', ''),
                'are_you_sure' => __('Are you sure?', 'njt-google-reviews'),
                'location_invalid' => __('The Location ID is invalid.', 'njt-google-reviews'),
                'updated' => __('Updated.', 'njt-google-reviews'),
            ));
        }
        public function register_my_setting()
        {
            //register_setting( 'njt_options_group', 'njt_google_api_key' );
            register_setting('njt_options_group', 'njt_google_api_key_use_our');
            register_setting('njt_options_group', 'njt_google_api_key_use');
            register_setting('njt_options_group', 'njt_google_place_id', array($this, 'saveRichSnippet'));
            register_setting('njt_options_group', 'njt_google_rich_name');
            register_setting('njt_options_group', 'njt_google_rich_descritions');
            // register_setting('njt_options_group', 'njt_google_rich_place_name');
            //UPADTE 25-02-2019
            register_setting('njt_options_group', 'google_reviews_schedule_on_off');
            register_setting('njt_options_group', 'googlereviews_schedule_update');
            register_setting('njt_options_group', 'googlereviews_schedule_value');
            //
            register_setting('njt_options_group_locations', 'njt_gg_reviews_location');
        }
        public function saveRichSnippet($place_id)
        {
            // $option_place_id = get_option('njt_gg_rich_by_place_id');
            // if ($place_id && empty($option_place_id) || ($option_place_id['place_id'] != $place_id)) {
            //     $reviews = new google_api('', $place_id);
            //     // $response = $reviews->get_reviews();
            //     if (empty($response->error_message)) {
            //         $listReviews = isset($response['njt_reviews']) ? $response['njt_reviews'] : array();
            //         if (count($listReviews) > 0) {
            //             //
            //             $optionsByLocationID = array(
            //                 'place_id' => $place_id,
            //                 'rating' => isset($response['result']['rating']) ? $response['result']['rating'] : '',
            //                 'reviews' => $listReviews[0]["author_name"],
            //             );
            //             update_option('njt_gg_rich_by_place_id', $optionsByLocationID);

            //         }

            //     }
            // }

            $option_place_id = get_option('njt_gg_rich_by_place_id');
            if ($place_id && empty($option_place_id) || ($option_place_id != $place_id)) {
                update_option('njt_gg_rich_by_place_id', $place_id);
            }
            return $place_id;
        }

        public function MenuGroup()
        {
            global $submenu;
            if (!array_key_exists('minja-team-menu', $submenu)) {
                add_menu_page('Google Reviews', 'Google Reviews', 'manage_options', 'minja-team-menu', null, NJT_PLUGIN_URL . '/assets/backend/img/ninjateam-icon.svg', 10);
            }
        }

        public function Settup()
        {
            require_once NJT_PLUGIN_PATH . '/includes/classes/njt-core.php';
            require_once NJT_PLUGIN_PATH . '/includes/classes/google.php';
            require_once NJT_PLUGIN_PATH . '/includes/classes/shortcode.php';
            require_once NJT_PLUGIN_PATH . '/includes/classes/shortcode-badge.php';
            require_once NJT_PLUGIN_PATH . '/includes/classes/google-rich-review-snippet.php';
            require_once NJT_PLUGIN_PATH . '/includes/classes/wpml.php';
            $njt_settup = njt_core_plugin::getInstance(__FILE__);
            // require_once NJT_PLUGIN_PATH . '/includes/options.php';
            $options = array(
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
            );
                
            $njt_settup->add_options_page(array(), $options);
            if (!class_exists('Google_Widget_Reviews')) {
                require NJT_PLUGIN_PATH . '/includes/classes/widget.php';
            }
            if (!class_exists('Google_Widget_Reviews_Badge')) {
                require NJT_PLUGIN_PATH . '/includes/classes/widget-badge.php';
            }
            register_widget('Google_Widget_Reviews_Badge');
            register_widget('Google_Widget_Reviews');
            return $njt_settup;
        }
        public function scripts()
        {
        }

        // === UPDATE 22-02-2019 === //
        public function findReviewsById($location_id)
        {
            $reviews = array();
            $args = array(
                'meta_query' => array(
                    array(
                        'key' => 'location_id',
                        'value' => $location_id,
                        'compare' => '=',
                    ),

                ),
                'post_type' => self::$post_type,
                'posts_per_page' => -1,
            );
            $args = apply_filters('njt_ggrv_get_reviews', $args);
            $posts = new WP_Query($args);

            if ($posts->have_posts()) {
                while ($posts->have_posts()) {
                    $posts->the_post();
                    $reviews[] = (object) array('id' => get_the_id());
                }
            }
            wp_reset_postdata();
            return $reviews;
        }
        public function ajaxDeleteReviews()
        {
            check_ajax_referer('njt_ggreviews_location_settings', 'nonce', true);
            $location_id = ((isset($_POST['location_id'])) ? $_POST['location_id'] : '');
            $locations = get_option('njt_gg_reviews_location', array());

            if (!empty($location_id)) {
                $reviews = $this->findReviewsById($location_id);
                foreach ($reviews as $k => $v) {
                    wp_delete_post($v->id);
                }
                //
                if (!empty($locations)) {
                    foreach ($locations["place_id"] as $key => $value) {
                        if ($value == $location_id) {
                            $position = $key;
                            break;
                        }
                    }
                    unset($locations["location_look"][$position]);
                    unset($locations["location_name"][$position]);
                    unset($locations["place_id"][$position]);
                    update_option('njt_gg_reviews_location', $locations);
                    //wp_send_json_success($locations);
                }
                wp_send_json_success();
                //
            } else {
                wp_send_json_error(array('mess' => __('Can not find Location ID, please try again.', 'njt-google-reviews')));
            }
        }
        public function njtCurlGetReviews($place_id, $api_key)
        {
            $url = "https://maps.googleapis.com/maps/api/place/details/json?placeid=" . $place_id . "&key=" . $api_key;
            //$url = "https://maps.googleapis.com/maps/api/place/details/json?placeid=ChIJeytYHL0yjoARxoN_dFPygp4&key=AIzaSyDd5z4OmoEZBTlY6bWDYrI6RqyvnQEKgdQ23";
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            $result = curl_exec($ch);
            curl_close($ch);
            $output = $result;
            $result = json_decode($output);

            return isset($result->error_message) ? $result->error_message : json_decode($output, true)["result"]["reviews"];
        }
        public function ggReviews_CheckUserReviewExit($args = false)
        {

            $default = array(
                'meta_query' => array(
                    array(
                        'key' => 'location_id',
                        'value' => $args['location_id'],
                        'compare' => '=',
                    ),
                    array(
                        'key' => 'author_name',
                        'value' => $args['author_name'],
                        'compare' => '=',
                    ),
                ),
                'post_type' => self::$post_type,
                'posts_per_page' => -1,
                'post_status' => array('publish', 'pending', 'future'),
            );
            $default = apply_filters('njt_ggrv_get_reviews', $default);
            $posts = new WP_Query($default);

            if ($posts->have_posts()) {
                return true;
            } else {
                return false;
            }
        }

        public function ajaxGetNewReviews()
        {
            //
            //wp_send_json_success($arr);
            //
            check_ajax_referer('njt_ggreviews_location_settings', 'nonce', true);
            $api_key = get_option("njt_google_api_key_use");

            $location_id = ((isset($_POST['location_id'])) ? $_POST['location_id'] : '');

            if (!empty($location_id) && !empty($api_key)) {
                $listInfo = $this->njtCurlGetReviews($location_id, $api_key);
                $reviews = new google_api('', $location_id);
                $response = $reviews->get_reviews();
                if (!empty($response->error_message)) {
                    wp_send_json_error(array('mess' => $response->error_message));
                } else {
                    $listInfo = isset($response['njt_reviews']) ? $response['njt_reviews'] : array();

                    if (count($listInfo) > 0) {
                        //
                        $optionsByLocationID = array(
                            'url' => isset($response['result']['url']) ? $response['result']['url'] : '',
                            'name' => isset($response['result']['name']) ? $response['result']['name'] : '',
                            'user_ratings_total' => isset($response['result']['user_ratings_total']) ? intval($response['result']['user_ratings_total']) : 0,
                            'place_avatar' => isset($response['place_avatar']) ? $response['place_avatar'] : '',
                            'rating' => isset($response['result']['rating']) ? $response['result']['rating'] : '',
                            'reviews' => isset($listInfo[0]["author_name"]) ? $listInfo[0]["author_name"] : '',
                            'icon' => isset($response['result']['icon']) ? $response['result']['icon'] : '',
                            'website' => isset($response['result']['website']) ? $response['result']['website'] : '',
                            'formatted_address' => isset($response['result']['formatted_address']) ? $response['result']['formatted_address'] : '',
                            'international_phone_number' => isset($response['result']['international_phone_number']) ? $response['result']['international_phone_number'] : '',
                            'price_level' => isset($response['result']['price_level']) ? $response['result']['price_level'] : 0,
                        );
                        update_option($location_id, $optionsByLocationID);
                        //
                        /*
                         * Insert to database
                         */

                        foreach ($listInfo as $k => $review) {
                            $agrs_ggreviews = array(
                                'author_name' => $review['author_name'],
                                'location_id' => $location_id,
                                'language'    => $review['language']                               
                            );
                            if (!$this->ggReviews_CheckUserReviewExit($agrs_ggreviews)) {
                                $insert = wp_insert_post(array(
                                    'post_title' => wp_trim_words($review['text'], 100),
                                    'post_content' => $review['text'],
                                    'post_status' => 'publish',
                                    'post_type' => self::$post_type,
                                    'post_date' => date('Y-m-d H:i:s', time()),
                                ));

                                foreach ($review as $k2 => $v2) {
									if($k2 == 'text') {
										update_post_meta($insert, $k2, urlencode(sanitize_text_field( $v2 )));
									} else {
										update_post_meta($insert, $k2, $v2);
									}
                                }
                                update_post_meta($insert, 'location_id', $location_id);
                                //

                                //
                            }
                        }

                        wp_send_json_success(array('mess' => __('successfully.')));
                    } else {
                        wp_send_json_error(array('mess' => __('No reviews found.')));
                    }
                }

            } else {
                wp_send_json_error(array('mess' => __('Please try again.', 'njt-google-reviews')));
            }
        }
        public function ajaxGetNameByPlaceID()
        {
            check_ajax_referer('njt_ggreviews_location_settings', 'nonce', true);
            $location_id = isset($_POST['place_id']) ? $_POST['place_id'] : false;
            if ($location_id) {
                $reviews = new google_api('', $location_id);
                $response = $reviews->get_reviews();
                if (!empty($response->error_message)) {
                    wp_send_json_error(array('mess' => $response->error_message));
                }
                wp_send_json_success(array('name' => isset($response['result']['name']) ? $response['result']['name'] : "Location Name Not Found!!"));
            }
        }
        public function njtGGREVIEWINIT()
        {
            $locations = get_option('njt_gg_reviews_location', array());
            if (empty($locations)) {
                $locations = array();
            }

            if (count($locations) > 0) {
                foreach ($locations["place_id"] as $k => $v) {
                    $location_id = $v;
                    $reviews = new google_api('', $location_id);
                    $response = $reviews->get_reviews();
                    if (empty($response->error_message)) {
                        $listInfo = isset($response['njt_reviews']) ? $response['njt_reviews'] : array();
                        if (count($listInfo) > 0) {
                            /*
                             * Insert to database
                             */

                            foreach ($listInfo as $k => $review) {
                                $agrs_ggreviews = array(
                                    'author_name' => $review['author_name'],
                                    'location_id' => $location_id,
                                    'language'    => $review['language']                               
                                );

                                if (!isset($_GET["location_id"])) {

                                    if (!$this->ggReviews_CheckUserReviewExit($agrs_ggreviews)) {
                                        $insert = wp_insert_post(array(
                                            'post_title' => wp_trim_words($review['text'], 100),
                                            'post_content' => $review['text'],
                                            'post_status' => 'publish',
                                            'post_type' => self::$post_type,
                                            'post_date' => date('Y-m-d H:i:s', time()),
                                        ));

                                        foreach ($review as $k2 => $v2) {
											if($k2 == 'text') {
												update_post_meta($insert, $k2, urlencode(sanitize_text_field( $v2 )));
											} else {
												update_post_meta($insert, $k2, $v2);
											}
                                        }
                                        update_post_meta($insert, 'location_id', $location_id);
                                    }

                                }

                            }

                        }
                    }
                }

            }

        }

        public function registerCustomPostType()
        {
            $labels = array(
                'name' => __('Njt Google Reviews', ''),
            );

            $args = array(
                'labels' => $labels,
                'public' => false,
                'publicly_queryable' => false,
                'show_ui' => true,
                'show_in_menu' => false,
                'query_var' => false,
                'rewrite' => array('slug' => self::$post_type),
                'capability_type' => 'post',
                'has_archive' => false,
                'hierarchical' => false,
                'menu_position' => null,
                'can_export' => false,
                'supports' => array('title'),
                'show_in_rest' => true,
                'capabilities' => array(
                    'create_posts' => 'do_not_allow', // false < WP 4.5
                ),
                'map_meta_cap' => true, // Set to `false`, if users are not allowed to edit/delete existing posts
            );

            register_post_type(self::$post_type, $args);
        }
        /*
         * Remove subsubsub
         */
        public function removeSubSubSub($views)
        {
            return array();
        }
        public function njtggReviews_OrderByCustomPostType($wp_query)
        {
            if (is_admin()) {
                // Get the post type from the query
                $post_type = $wp_query != null && $wp_query->query != null && $wp_query->query['post_type'] != null ? $wp_query->query['post_type'] : null;

                if (in_array($post_type, array(self::$post_type))) {
                    $wp_query->set('meta_key', 'time');
                    $wp_query->set('orderby', 'meta_value_num');
                    $wp_query->set('order', 'DESC');
                }
            }
        }
        public function registerMetaBoxes()
        {
            add_meta_box('njt-google-review-detail', __('Reviews'), array($this, 'metaboxReviewDetail'), self::$post_type, 'normal', 'low');
        }
        public function metaboxReviewDetail()
        {
            global $post;
            $meta = get_post_meta($post->ID);
            $avatar = isset($meta['avatar'][0]) ? $meta['avatar'][0] : NJT_PLUGIN_PATH . '/assets/images/mystery-man.png';
            $google_api_review = new google_api('', $meta['location_id'][0]);
            $time = get_post_meta($post->ID, 'relative_time_description', true);
            ?>
                    <div class="njt-google-reviews-col">
                        <div class="njt-google-reviews-main">
                            <div class="google-reviews-thumbnail">
                                <img title="<?php echo $meta['author_name'][0]; ?>" style="width: 100%" src="<?php echo $avatar; ?>" alt="<?php $meta['author_name'][0]; ?>">
                            </div>
                            <div class="google-reviews-info">
                                <h3><a rel="nofollow" target="_blank" href="#"><?php echo $meta['author_name'][0]; ?></a></h3>
                                <?php echo $google_api_review->only_get_rating($meta['rating'][0]); ?>
                                <div class="google-reviews-meta"><?php echo $time; ?></div>
                            </div>
                            <div class="clear"></div>
                        </div>
                        <div class="content-google-reviews"><?php echo urldecode($meta['text'][0]); ?></div>
                    </div>
            <?php }

        public function restrictManagePosts()
        {
            $type = 'post';
            if (isset($_GET['post_type'])) {
                $type = $_GET['post_type'];
            }

            //only add filter to post type you want
            if (self::$post_type == $type) {
                $locations = get_option('njt_gg_reviews_location', array());
                ?>
            <select name="location_id">
            <option value=""><?php _e('Select Location', 'njt-google-reviews');?></option>
            <?php
$current_v = isset($_GET['location_id']) ? $_GET['location_id'] : '';
                foreach ($locations["place_id"] as $k => $v) {
                    printf(
                        '<option value="%s"%s>%s</option>',
                        $v,
                        selected($v, $current_v, false),
                        $locations["location_look"][$k]
                    );
                }
                ?>
            </select>
            <select name="stars">
            <option value=""><?php _e('Select Rating', 'njt-google-reviews');?></option>
            <?php
$liststars = array("1" => 1, "2" => 2, "3" => 3, "4" => 4, "5" => 5);
                $current_v = isset($_GET['stars']) ? $_GET['stars'] : '';
                foreach ($liststars as $k => $v) {
                    printf(
                        '<option value="%s"%s>%s</option>',
                        $v,
                        selected($v, $current_v, false),
                        $v
                    );
                }
                ?>
            </select>
            <?php }
        }

        public function parseQuery($query)
        {
            global $pagenow;
            $type = 'post';
            if (isset($_GET['post_type'])) {
                $type = $_GET['post_type'];
            }
            if (self::$post_type == $type && is_admin() && $pagenow == 'edit.php' && isset($_GET['location_id']) && $_GET['location_id'] != '' && $query->query_vars['post_type'] == self::$post_type) {
                $meta = array(
                    array(
                        'key' => 'location_id',
                        'value' => $_GET['location_id'],
                        'compare' => '=',
                    ),  
                );
                $meta = apply_filters('njt_ggrv_parse_query', $meta);
                $query->query_vars['meta_query'] = $meta;
            }

            if (self::$post_type == $type && is_admin() && $pagenow == 'edit.php' && isset($_GET['stars']) && $_GET['stars'] != '' && $query->query_vars['post_type'] == self::$post_type) {
                $meta = array(
                    array(
                        'key' => 'rating',
                        'value' => $_GET['stars'],
                        'compare' => '=',
                   )
                );
                $query->query_vars['meta_query'] = $meta;
            }
        }

        public function njtGgooleReviewsAdd_columns($columns)
        {
            unset($columns['title']);
            unset($columns['date']);
            $columns['avatar'] = __('Avatar');
            $columns['title'] = __('Title');
            $columns['rating'] = __('Rating');
            //$columns['date'] = __('Date');
            $columns['time'] = __('Time');
            return $columns;
        }

        public function njtGgooleReviewsShowContent($column)
        {
            global $post;
            $google_api_review = new google_api('', get_post_meta($post->ID, 'location_id', true));
            switch ($column) {
                case 'avatar':
                    // Retrieve post meta
                    $avatar = get_post_meta($post->ID, 'avatar', true);
                    echo '<div class="google-reviews-thumbnail"><img style="width: 100%" src="' . $avatar . '" alt="' . $avatar . '"></div>';
                    break;
                case 'rating':
                    $rating = get_post_meta($post->ID, 'rating', true);
                    echo $google_api_review->only_get_rating($rating);
                    break;
                case 'time':
                    $time_before = get_post_meta($post->ID, 'time', true);
                    $time = get_post_meta($post->ID, 'relative_time_description', true);
                    echo $time;
                    break;
            }
        }
        public function googlereviews__activation()
        {
            update_option('njt_google_api_key_use_our', 'yes'); // use again code by Thanh
            if (!wp_next_scheduled('googlereview_cron_action')) {
                wp_schedule_event(time(), 'googlereviews_reminder', 'googlereview_cron_action');
                update_option('googlereview_start_autoupdate', "no");
            }
        }
        public function googlereviews_cronstarter_activation()
        {
            if (!wp_next_scheduled('googlereview_cron_action')) {
                wp_schedule_event(time(), 'googlereviews_reminder', 'googlereview_cron_action');
                update_option('googlereview_start_autoupdate', "no");
            }

        }
        public function googlereviews_cronstarter_deactivation()
        {
            if (wp_next_scheduled('googlereview_cron_action')) {
                // find out when the last event was scheduled
                $timestamp = wp_next_scheduled('googlereview_cron_action');
                // unschedule previous event if any
                wp_unschedule_event($timestamp, 'googlereview_cron_action');
                update_option('googlereview_start_autoupdate', "no");
            }
        }
        public function ggreviews_convert_option_to_second($option_value, $value_time)
        {
            switch ($option_value) {
                case "minute":
                    return (int) $value_time * 60;
                    break;
                case "hour":
                    return (int) $value_time * 60 * 60;
                    break;
                case "day":
                    return (int) $value_time * 24 * 60 * 60;
                    break;
                case "week":
                    return (int) $value_time * 7 * 24 * 60 * 60;
                    break;
                case "month":
                    return (int) $value_time * 30 * 24 * 60 * 60;
                    break;
                case "year":
                    return (int) $value_time * 12 * 30 * 24 * 60 * 60;
                    break;
                default:
                    return 0;
            }
        }
        public function googlereviews_cron_add_defined()
        {
            if (get_option("google_reviews_schedule_on_off")) {

                $value_time_ = get_option('googlereviews_schedule_value');
                if (get_option("googlereviews_schedule_update") && (int) $value_time_ > 0) {
                    $schedule_defined = get_option("googlereviews_schedule_update");

                    $ggreviews_time = $this->ggreviews_convert_option_to_second($schedule_defined, $value_time_);
                    if (!empty($value_time_)) {

                        $schedules['googlereviews_reminder'] = array(
                            'interval' => $ggreviews_time,
                            'display' => __('Google Reviews Reminder'),
                        );
                        return $schedules;
                    }

                } else {
                    if (wp_next_scheduled('googlereview_cron_action')) {
                        $timestamp = wp_next_scheduled('googlereview_cron_action');
                        wp_unschedule_event($timestamp, 'googlereview_cron_action');
                        update_option('googlereview_start_autoupdate', "no");
                    }
                }

            }
        }
        public function ggreviews_excute_update_reminder()
        {
            if (get_option('googlereview_start_autoupdate') == "yes") {
                $value_time_update = get_option('googlereviews_schedule_value');
                $schedule_update = get_option("googlereviews_schedule_update");
                $string_show_schedule = "Every " . (int) $value_time_update . " " . $schedule_update . " excute schedule update...";
                //
                $locations = get_option('njt_gg_reviews_location', array());
                if (empty($locations)) {
                    $locations = array();
                }
                if (count($locations) > 0) {
                    foreach ($locations["place_id"] as $k => $v) {
                        $location_id = $v;
                        $reviews = new google_api('', $location_id);
                        $response = $reviews->get_reviews();
                        if (empty($response->error_message)) {
                            $listInfo = isset($response['njt_reviews']) ? $response['njt_reviews'] : array();

                            if (count($listInfo) > 0) {
                                //
                                $optionsByLocationID = array(
                                    'url' => isset($response['result']['url']) ? $response['result']['url'] : '',
                                    'name' => isset($response['result']['name']) ? $response['result']['name'] : __('Sorry, this business does not have a proper Place ID set.', 'njt-google-reviews'),
                                    'user_ratings_total' => isset($response['result']['user_ratings_total']) ? intval($response['result']['user_ratings_total']) : 0,
                                    'place_avatar' => isset($response['place_avatar']) ? $response['place_avatar'] : '',
                                    'rating' => isset($response['result']['rating']) ? $response['result']['rating'] : '',
                                );
                                update_option($location_id, $optionsByLocationID);
                                //
                                /*
                                 * Insert to database
                                 */
                                foreach ($listInfo as $k => $review) {
                                    $agrs_ggreviews = array(
                                        'author_name' => $review['author_name'],
                                        'location_id' => $location_id,
                                        'language'    => $review['language']                               
                                    );

                                    if (!isset($_GET["location_id"])) {

                                        if (!$this->ggReviews_CheckUserReviewExit($agrs_ggreviews)) {
                                            $insert = wp_insert_post(array(
                                                'post_title' => wp_trim_words($review['text'], 100),
                                                'post_content' => $review['text'],
                                                'post_status' => 'publish',
                                                'post_type' => self::$post_type,
                                                'post_date' => date('Y-m-d H:i:s', time()),
                                            ));

                                            foreach ($review as $k2 => $v2) {
                                                if($k2 == 'text') {
													update_post_meta($insert, $k2, urlencode(sanitize_text_field( $v2 )));
												} else {
													update_post_meta($insert, $k2, $v2);
												}
                                            }
                                            update_post_meta($insert, 'location_id', $location_id);
                                        }

                                    }

                                }

                            }
                        }
                    }

                }
                //
                file_put_contents(NJT_PLUGIN_DIR . "/log.txt", json_encode($string_show_schedule) . "\n", FILE_APPEND);
            } else {
                file_put_contents(NJT_PLUGIN_DIR . "/log.txt", json_encode("not excute update google reviews...") . "\n", FILE_APPEND);
                update_option('googlereview_start_autoupdate', "yes");
            }

        }
        // === UPDATE 22-02-2019 === //

        // === UPDATE 03-06-2019 === //
        function isDisplayGoogleRickSnippet($place_id) {
            
            if(empty($place_id)){
                return false;
            }

            $options_locationID = get_option($place_id);
    
            if(empty($options_locationID['name'])) {
                return false;
            }
            if(empty($options_locationID['place_avatar'])) {
                return false;
            }
            if(empty($options_locationID['formatted_address'])) {
                return false;
            }
            if(empty($options_locationID['international_phone_number'])) {
                return false;
            }
            return true;
        }
        // === UPDATE 03-06-2019 === //
    }
}
function njt_google_reviews_install()
{
    return njt_google_reviews::instance();
}
njt_google_reviews_install();
