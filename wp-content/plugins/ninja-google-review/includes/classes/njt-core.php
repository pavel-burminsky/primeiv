<?php

if (!class_exists('njt_core_plugin')) {

    class njt_core_plugin
    {
        private static $_instance = null;
        public $meta;
        public $basename;
        public $slug;
        public $version;
        public $textdomain;
        public $name;
        public $url;
        public $includes;
        public $views;
        public $assets;
        public $backend;
        public $admin_url;
        public $option;
        public $options;
        public $settings;
        public $njt_general_settings;
        public function __construct($file, $args = array())
        {

            $defaults = array('includes' => 'includes', 'views' => 'includes/views', 'assets' => 'assets', 'backend' => 'assets/backend');
            $this->file = $file;
            $this->args = wp_parse_args($args, $defaults);
            if (!function_exists('get_plugin_data')) {

                require_once ABSPATH . 'wp-admin/includes/plugin.php';

            }

            $this->meta = get_plugin_data($this->file, false);

            //print_r($this->meta);

            $this->basename = plugin_basename($this->file);
            $this->slug = sanitize_key($this->meta['Name']);
            $this->version = sanitize_text_field($this->meta['Version']);
            $this->name = $this->meta['Name'];
            $this->url = plugins_url('', $this->file);
            $this->option = $this->slug . '_options';
            $this->includes = trailingslashit(path_join(plugin_dir_path($this->file), trim($this->args['includes'], '/')));
            $this->views = trailingslashit(path_join(plugin_dir_path($this->file), trim($this->args['views'], '/')));
            $this->backend = trailingslashit(path_join(plugin_dir_path($this->file), trim($this->args['backend'], '/')));
            $this->assets = trim($this->args['assets'], '/');
            define('NJT_VERSION', $this->version);

            add_action('wp_ajax_njt_clear_cache', array($this, 'clear_cache'));
            add_filter('plugin_row_meta', array($this, 'action_link'), 10, 2);

            add_filter('plugin_action_links', array($this, 'add_action_links'), 100, 25);

            add_filter('admin_footer_text', array($this, 'footerPlugin'), 1, 2);

            add_action('wp_ajax_get_shortcode_place_review', array($this, 'get_shortcode_place_review'));
           
            add_action('wp_ajax_get_options_by_locationid', array($this, 'get_options_by_locationID'));
            add_action('admin_notices', array($this, 'renderNotice'));
            //
            $this->njt_general_settings = array(
                array('name' => __('General', 'njt-google-reviews'), 'type' => 'opentab'),
                array(
                    'name' => __('Google Places API Key', 'njt-google-reviews'),
                    'desc' => sprintf(__(' <a href="%1$s" class="new-window" target="_blank">See tutorial here</a>', 'njt-google-reviews'), esc_url('https://ninjateam.org/how-to-setup-google-place-reviews-wordpress-plugin/')),
                    'std' => '',
                    'id' => 'njt_google_api_key_use',
                    'type' => 'text',
                    'label' => __('Yes', 'njt-google-reviews'),
                ),
                array('type' => 'closetab', 'actions' => true),
                array('name' => __('Google Rich Snippet', 'njt-google-reviews'), 'type' => 'opentab'),
                array(
                    'name' => __('Create badge ', 'njt-google-reviews'), 'type' => 'google-rich',
                ),
                array('type' => 'closetab', 'actions' => false),
            );
            //

        }

        public static function getInstance($arg)
        {
            if (is_null(self::$_instance)) {
                self::$_instance = new self($arg);
            }
            return self::$_instance;
        }

        public function footerPlugin($footer_text)
        {

            $current_screen = get_current_screen();

            if (isset($current_screen->id) && apply_filters('njt_google_footer_text', $current_screen->id == "ninjateam_page_" . $this->slug)) {

                //$this->meta['PluginURI']
                $footer_text = "If you like " . $this->name . " for WordPress, please leave a <a target='_blank' href='" . 'https://codecanyon.net/downloads' . "'>★★★★★</a> rating. Many thanks from NinjaTeam in advance!";

            }
            return $footer_text;
        }

        public function add_action_links($actions, $plugin_file)
        {
            static $plugin;
            if (!isset($plugin)) {
                $plugin = $this->basename;
            }
            if ($plugin == $plugin_file) {
                $setting = array('<a href="' . get_bloginfo('wpurl') . '/wp-admin/admin.php?page=' . $this->slug . '" title="' . $this->name . '">' . __('Settings', 'njt-google-reviews') . '</a>');
                $actions = array_merge($actions, $setting);
            }
            return $actions;
        }
        public function action_link($links, $file)
        {
            if (strpos($file, 'ninja-google-reiews.php') !== false) {
                $links[] = '<a href="https://ninjateam.org/support/" title="Support">' . __('Support', 'njt-google-reviews') . '</a>';
            }
            return $links;
        }
        public function clear_cache()
        {
            if (isset($_POST['transient_id_1']) && isset($_POST['transient_id_2'])) {
                delete_transient($_POST['transient_id_1']);
                delete_transient($_POST['transient_id_2']);
                echo __('Cache cleared', 'njt-google-reviews');
            } else {
                echo __('Error: Transient ID not set. Cache not cleared.', 'njt-google-reviews');
            }
            die();
        }
       
        public function register_assets()
        {
        }
        public function enqueue_assets()
        {
        }

        public function default_settings($manual = false)
        {
            // Settings page is created
            if ($manual || !get_option($this->option)) {
                $defaults = array();
                foreach ((array) $this->options as $value) {
                    if (isset($value['id'])) {
                        $defaults[$value['id']] = empty($value['std']) ? '' : $value['std'];
                    }
                }
                update_option($this->option, $defaults);
            }
        }

        public function get_option($option = false)
        {
            $options = get_option($this->option);
            $value = ($option) ? $options[$option] : $options;
            return (is_array($value)) ? array_filter($value, 'esc_attr') : esc_attr(stripslashes($value));
        }
        public function update_option($key = false, $value = false)
        {
            $settings = get_option($this->option);
            $new_settings = array();
            foreach ($settings as $id => $val) {
                $new_settings[$id] = ($id == $key) ? $value : $val;
            }
            return update_option($this->option, $new_settings);
        }
        
        public function add_options_page($args, $options = array())
        {
            $this->options = $options;

            $defaults = array(
                'parent' => 'minja-team-menu',
                'menu_title' => __('Shortcode', 'njt-google-reviews'),
                'page_title' => $this->name,
                'capability' => 'manage_options',
                'link' => true,
            );

            $this->settings = wp_parse_args($args, $defaults);
            $this->admin_url = admin_url('admin.php?page=' . $this->slug);

            // Insert default settings if it's doesn't exists
            add_action('admin_init', array(&$this, 'default_settings'));
            // Manage options
            add_action('admin_menu', array(&$this, 'options_page'));

        }

        public function options_page()
        {

            add_submenu_page(
                $this->settings['parent'], __($this->settings['page_title'], 'njt-google-reviews'), __($this->settings['menu_title'], 'njt-google-reviews'), $this->settings['capability'], $this->slug, array(
                    &$this,
                    'render_options_page',
                )
            );
            //==== UPDATE 22/02/2019 === //
            add_submenu_page(
                $this->settings['parent'], __("Locations", 'njt-google-reviews'), __("Locations", 'njt-google-reviews'), $this->settings['capability'], "njt-ggreviews-locations", array(
                    &$this,
                    'render_list_location_callback',
                )
            );
            add_submenu_page(
                $this->settings['parent'], __("Settings", 'njt-google-reviews'), __("Settings", 'njt-google-reviews'), $this->settings['capability'], "njt-ggreviews-settings", array(
                    &$this,
                    'render_general_settings_callback',
                )
            );
            //==== UPDATE 22/02/2019 === //
        }

        public function render_options_page()
        {

            $backend_file = $this->views . 'create-shortcode-badge.php';
            if (file_exists($backend_file)) {

                require_once $backend_file;
            }

        }

        // === UPDATE 22/02/2019 === //
        public function render_list_location_callback()
        {

            $backend_file = $this->views . 'list-location.php';

            if (file_exists($backend_file)) {

                require_once $backend_file;
            }
        }

        public function render_general_settings_callback()
        {

            $backend_file = $this->views . 'settings.php';

            if (file_exists($backend_file)) {

                require_once $backend_file;
            }
        }

        public function render_panes_general_settings()
        {
            // Options loop

            foreach ($this->njt_general_settings as $option) {
                // Get option file path
                $option_file = $this->views . $option['type'] . '.php';

                if (file_exists($option_file)) {

                    include $option_file;

                } else {

                    trigger_error('Option file <strong>' . $option_file . '</strong> not found!', E_USER_NOTICE);

                }
            }
        }

        public function render_tabs_general_settings()
        {

            foreach ($this->njt_general_settings as $option) {
                if ($option['type'] == 'opentab') {
                    $active = (isset($active)) ? ' njt-plugin-tab-inactive'
                    : ' nav-tab-active njt-plugin-tab-active';
                    echo '<a href="javascript:void(0)" class="nav-tab' . $active . '">' . $option['name'] . '</a>';
                }
            }

        }
        // === UPDATE 22/02/2019 === //

        public function render_panes()
        {

            // // Get current settings

            // Options loop

            foreach ($this->options as $option) {
                // Get option file path
                $option_file = $this->views . $option['type'] . '.php';

                if (file_exists($option_file)) {

                    include $option_file;

                } else {

                    trigger_error('Option file <strong>' . $option_file . '</strong> not found!', E_USER_NOTICE);

                }
            }
        }

        public function render_tabs()
        {

            foreach ($this->options as $option) {
                if ($option['type'] == 'opentab') {
                    $active = (isset($active)) ? ' njt-plugin-tab-inactive'
                    : ' nav-tab-active njt-plugin-tab-active';
                    echo '<a href="javascript:void(0)" class="nav-tab' . $active . '">' . $option['name'] . '</a>';
                }
            }

        }
        
        public function get_shortcode_place_review()
        {
            if( ! wp_verify_nonce( $_POST['nonce'] ,'njt_ggreviews_location_settings')) wp_die();
            $shortcode = str_replace("\\", "", $_POST['shortcode']);
            echo do_shortcode($shortcode);
            wp_die();
        }

        public function get_options_by_locationID()
        {   
            if( ! wp_verify_nonce( $_POST['nonce'] ,'njt_ggreviews_location_settings')) wp_die();
            $placeID =  !empty($_POST['placeId']) ? sanitize_text_field($_POST['placeId']) : '';
            $options_by_locationID = get_option($placeID);
            $overall_rating = isset($options_by_locationID['rating']) ? $options_by_locationID['rating'] : '';
            $reviews = new google_api(null, $placeID);
            $starRating =  $reviews->get_star_rating($overall_rating, null, null, null);
            $locationInfor = array(
                'startRating' => $starRating,
                'placeInfor' =>!empty($options_by_locationID['rating']) ? esc_html($options_by_locationID['rating']) : '',
                'ratings_count' => !empty($options_by_locationID['user_ratings_total']) ? number_format($options_by_locationID['user_ratings_total'],0,"",".") : '0',
                'place_avatar' => !empty($options_by_locationID['place_avatar']) ? esc_attr($options_by_locationID['place_avatar']) : '',
                'international_phone_number' => !empty($options_by_locationID['international_phone_number']) ? esc_attr($options_by_locationID['international_phone_number']) : '',
                'website' => !empty($options_by_locationID['website']) ? esc_attr($options_by_locationID['website']) : '',
                'formatted_address' => !empty($options_by_locationID['formatted_address']) ? strip_tags($options_by_locationID['formatted_address']) : ''
            );
            
            wp_send_json_success($locationInfor);
          
            wp_die();
        }

        public function renderNotice()
        {
            if (function_exists('get_current_screen')) {
                $screen = !empty($_GET['page']) ? $_GET['page'] : '';
                if ($screen != 'njt-ggreviews-settings'){
                    return;
                } 
            }
            $place_id = get_option('njt_google_place_id');
            $njt_google_reviews = new njt_google_reviews();
            $isDisplayGoogleRickSnippet = $njt_google_reviews->isDisplayGoogleRickSnippet($place_id);
            if(!$isDisplayGoogleRickSnippet && $place_id !='none_location') {
            ?>
                <div class="nta-error-ggrick nta-mgr-r20">
                    <div id="message" class="error notice is-dismissible"><p><?php _e('Update error Google Rich Snippet. Please update the location', 'njt-google-reviews');?></p><button type="button" class="notice-dismiss"></button></div>
                </div>
            <?php
            }
        }
    }

}