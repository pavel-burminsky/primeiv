<?php
class NJT_GGRV_Helper
{
    public static function get_star_rating($rating, $time, $hide_out_of_rating, $hide_google_image)
    {

        $html = '';
        $rating_value = '<div class="njt-fr-starsnumb" ' . (($hide_out_of_rating === '1') ? ' style="display:none;"' : '') . '><span>' . $rating . '</span>' . __(' out of 5 stars', 'njt-google-reviews') . '</div>';

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
}
