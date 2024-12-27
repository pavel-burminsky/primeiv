<?php

if (!class_exists('NJT_GGRV_WPML')) {
    class NJT_GGRV_WPML
    {   
        private $activePolylang;
        public function __construct()
        {
            add_action('init', function(){
               if ($this->isPluginTranslateActive()) {
                    add_filter('njt_ggrv_get_reviews', array($this, 'getReviews'), 10, 1);
                    add_filter('njt_ggrv_parse_query', array($this, 'parseQuery'), 10, 1);
                    add_filter('njt_ggrv_google_api_query', array($this, 'google_query'), 10, 1);
                }
            });
        }

        public function isPluginTranslateActive()
        {
            global $sitepress, $polylang;

            if(!$sitepress && !$polylang) {
                return false;
            }

            $this->activePolylang = function_exists("pll_get_post_translations");

            if ($sitepress !== null && get_class($sitepress) === 'SitePress') {
                $settings = $sitepress->get_setting('custom_posts_sync_option', array());
                $post_type = 'njt_google_reviews';
                if (isset($settings[$post_type]) && ($settings[$post_type] === '0' )) {
                 return true;
                }
            } elseif ($this->activePolylang && $polylang->options['media_support'] == 1) {
                return true;
            } 
            return false;
        }

        public function getReviews($args)
        {
            array_push($args['meta_query'], array(
                'key' => 'language',
                'value' => ICL_LANGUAGE_CODE,
                'compare' => '=',
            ));
            return $args;
        }

        public function parseQuery($args)
        {
            array_push($args, array(
                'key' => 'language',
                'value' => ICL_LANGUAGE_CODE,
                'compare' => '=',
            ));
            return $args;
        }

        public function google_query($args){
            $args['language'] = ICL_LANGUAGE_CODE;
            return $args;
        }
    }

    new NJT_GGRV_WPML();
}
