<?php

if (!class_exists('google_api')) {

    /**
     *
     */
    class google_api
    {

        private $reference = 'No Location Set';
        private $place_id = 'No location set';
        private $api_key;
        private $url;

        public function __construct($reference, $place_id)
        {
            require_once NJT_PLUGIN_PATH . '/includes/classes/getAvatarAPI.php';
            $this->place_id = $place_id;
            $this->reference = $reference;
            $this->api_key = get_option('njt_google_api_key_use');

            if (strlen($this->place_id) && ($this->place_id !== 'No location set')) {
                    $default = array(
                        'placeid' => $place_id,
                        'fields' => 'url,name,user_ratings_total,photo,rating,review,icon,international_phone_number,website,formatted_address,price_level',
                        'key' => $this->api_key,
                        'language' => $this->loadPluginTextdomain(),
                    );
                    $default = apply_filters('njt_ggrv_google_api_query', $default);
                    $this->url = add_query_arg($default,
                    'https://maps.googleapis.com/maps/api/place/details/json'
                );
            }

        }

        public function loadPluginTextdomain() {
            if (function_exists('determine_locale')) {
              $locale = determine_locale();
            } else {
              $locale = is_admin() ? get_user_locale() : get_locale();
            }

            return $locale;
        }

        public function get_reviews()
        {

            $url = esc_url_raw($this->url);
            $data = wp_remote_get($this->url);

            if (is_wp_error($data)) {

                $error_message = $data->get_error_message();
                echo $error_message;
                return;

            }

            if (empty($data['body'])) {

                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HEADER, 0);
                $data = curl_exec($ch); // Google response
                curl_close($ch);
                $response = json_decode($data, true);

            } else {
                $response = json_decode($data['body'], true);
            }

            $response = $this->get_reviewers_avatars($response);
            $response = $this->get_business_avatar($response);
            return $response;

        }

        public function get_business_avatar($response)
        {

            if (isset($response['result']['photos'])) {

                $request_url = add_query_arg(
                    array(
                        'photoreference' => $response['result']['photos'][0]['photo_reference'],
                        'key' => $this->api_key,
                        'maxwidth' => '300',
                        'maxheight' => '300',
                    ),
                    'https://maps.googleapis.com/maps/api/place/photo'
                );

                $response_urlAvatar = Njt_Avatar_Google_API::njt_getAvatarUrl($request_url);

                $response = array_merge($response, array('place_avatar' => esc_url($response_urlAvatar)));

            }

            return $response;

        }

        public function get_reviewers_avatars($response)
        {

            $njt_reviews = array();

            if (isset($response['result']['reviews']) && !empty($response['result']['reviews'])) {

                // Loop Google Places reviews.
                foreach ($response['result']['reviews'] as $review) {

                    if (!empty($review['profile_photo_url'])) {
                        $avatar_img = $review['profile_photo_url'];
                    } else {
                        $avatar_img = NJT_PLUGIN_URL . '/assets/images/mystery-man.png';
                    }

                    $review = array_merge($review, array('avatar' => $avatar_img));

                    array_push($njt_reviews, $review);
                }

                $response = array_merge($response, array('njt_reviews' => $njt_reviews));
            }

            return $response;
        }
        public function only_get_rating($rating)
        {
            $html = '';

            $tam = 0;
            $stars_percent = $rating / 5 * 100;
            if (is_float($rating)) {
                $arg = explode('.', $rating);
                $rating = $arg[0];
                $tam = $arg[1];
            }

            $html .= '<div class="njt-fr-starslist-wrapper njt-fr-starslist-wrapper-google-review">
						<div class="njt-fr-starslist-container">
						<div class="njt-fr-starslist-current" style="width: ' . $stars_percent . '%">';
            for ($i = 1; $i <= $rating; $i++) {
                $html .= '<div class="njt-fr-star"></div>';
            }
            if ($tam == 0) {
                $html .= '<div class="njt-fr-star"></div>';
            }
            if ($tam > 0) {
                $html .= '<div class="njt-fr-star"></div>';
            }
            $html .= '</div>';
            $html .= '<div class="njt-fr-starslist-background">
							<div class="njt-fr-star"></div>
							<div class="njt-fr-star"></div>
							<div class="njt-fr-star"></div>
							<div class="njt-fr-star"></div>
							<div class="njt-fr-star"></div>
						</div>';
            $html .= '</div>';
            $html .= '</div>';
            return $html;
        }
        public function get_star_rating($rating, $time, $hide_out_of_rating, $hide_google_image)
        {

            $html = '';
            $rating_value = '<div class="njt-fr-starsnumb" ' . (($hide_out_of_rating === '1') ? ' style="display:none;"' : '') . '><span>' . $rating . '</span>' . __('', 'njt-google-reviews') . '</div>';

            $tam = 0;
            $stars_percent = $rating / 5 * 100;
            if (is_float($rating)) {
                $arg = explode('.', $rating);
                $rating = $arg[0];
                $tam = $arg[1];
            }
            $html .= $rating_value;
            $html .= '<div class="njt-fr-starslist-wrapper njt-fr-starslist-wrapper-google-review">
						<div class="njt-fr-starslist-container">
						<div class="njt-fr-starslist-current" style="width: ' . $stars_percent . '%">';
            for ($i = 1; $i <= $rating; $i++) {
                $html .= '<div class="njt-fr-star"></div>';
            }
            if ($tam == 0) {
                $html .= '<div class="njt-fr-star"></div>';
            }
            if ($tam > 0) {
                $html .= '<div class="njt-fr-star"></div>';
            }
            $html .= '</div>';
            $html .= '<div class="njt-fr-starslist-background">
							<div class="njt-fr-star"></div>
							<div class="njt-fr-star"></div>
							<div class="njt-fr-star"></div>
							<div class="njt-fr-star"></div>
							<div class="njt-fr-star"></div>
						</div>';
            $html .= '</div>';
            $html .= '</div>';

            if ($time) {
                $html .= '<span class="gpr-rating-time">' . $time . '</span>';
            }

            return $html;

        }

        public static function get_time_since($date, $granularity = 1)
        {
            $difference = time() - $date;
            $retval = '';
            $periods = array(
                'decade' => 315360000,
                'year' => 31536000,
                'month' => 2628000,
                'week' => 604800,
                'day' => 86400,
                'hour' => 3600,
                'minute' => 60,
                'second' => 1,
            );

            foreach ($periods as $key => $value) {
                if ($difference >= $value) {
                    $time = floor($difference / $value);
                    $difference %= $value;
                    $retval .= ($retval ? ' ' : '') . $time . ' ';
                    $retval .= (($time > 1) ? $key . 's' : $key);
                    $granularity--;
                }
                if ($granularity == '0') {
                    break;
                }
            }

            return $retval . ' ago';
        }

        public function delete_transient_cache($transient_unique_id)
        {
            delete_transient('njt_grv_widget_api_' . $transient_unique_id);
            delete_transient('njt_grv_widget_options_' . $transient_unique_id);
        }

    }

}
