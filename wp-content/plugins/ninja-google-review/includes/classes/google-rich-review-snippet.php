<?php
class NJT_GG_RICH_REVIEW_GG_RV
{
    private $type = 'Review';
    private $name = 'Ninja Team Google Rich Review Snippet.';
    private $description = 'Ninja Team Google Rich Review Snippet.';
    //
    private $aggregateRating;
    private $ratingValue;
    private $ratingCount;
    //
    private $fb_page;
    private $fb_reviews;
    private $njt_gg_rich_snippet_opts;
    private $fb_page_info;
    private $place_id;
    private $gg_api;
    public function __construct()
    {
        //$this->fb_reviews = new FB_Review();
        $name = get_option('njt_google_rich_name');
        $desctions = get_option('njt_google_rich_descritions');
        $place_id = get_option('njt_gg_rich_by_place_id');
        $this->name = !empty($name) ? $name : get_bloginfo('sitename');
        $this->description = !empty($desctions) ? $desctions : get_bloginfo('description');
        $this->place_id = !empty($place_id) ? $place_id : false;
    }
    public function init()
    {
        add_action('wp_head', array($this, 'Render'), 0);
    }
    public function Render()
    {
        if (empty($this->place_id)) {
            return;
        }

        $options_by_locationID = get_option($this->place_id);
        if (empty($options_by_locationID)) {
            return;
        }
        $njt_google_reviews = new njt_google_reviews();
        $isDisplayGoogleRickSnippet = $njt_google_reviews->isDisplayGoogleRickSnippet($this->place_id);
        if (!$isDisplayGoogleRickSnippet) {
            return;
        }

        $ratings = isset($options_by_locationID['rating']) ? $options_by_locationID['rating'] : '';
        $author_name = isset($options_by_locationID['reviews']) ? $options_by_locationID['reviews'] : '';
        $user_ratings_total = isset($options_by_locationID['user_ratings_total']) ? $options_by_locationID['user_ratings_total'] : '';
        echo '<!-- njt-google-rich-snippet(google review) -->';
        echo '<script type="application/ld+json">{';
        echo '"@context": "http://schema.org/",';
        echo '"@type": "' . $this->type . '",';
        echo '"name": "' . wp_kses_post(wp_unslash($this->name)) . '",';
        echo '"description": "' . wp_kses_post(wp_unslash($this->description)) . '",';
        echo '"itemReviewed": {';
        echo '	 "@type": "LocalBusiness",';
        echo '	"address": "' . $options_by_locationID['formatted_address'] . '",';
        echo '	"telephone": "' . $options_by_locationID['international_phone_number'] . '",';
        echo '	"url": "' . $options_by_locationID['website'] . '",';
        echo '	"name": "' . $options_by_locationID['name'] . '",';
        echo '	"image": "' . $options_by_locationID['place_avatar'] . '",';
        echo '	"priceRange": "' . $options_by_locationID['price_level'] . '"';
        echo '},';
        echo '"author": {';
        echo '	"@type": "Person",';
        echo '	"name": "' . $author_name . '"';
        echo '},';
        echo '"reviewRating": {';
        echo '"@type": "AggregateRating",';
        echo '"ratingValue": "' . $ratings . '",';
        echo '"bestRating": "5",';
        echo '"ratingCount": "' . $user_ratings_total . '"';
        echo '}';
        echo '}</script>';
        echo '<!-- end njt-google-rich-snippet (google review) -->';
    }
}
$NJT_GG_RICH_REVIEW = new NJT_GG_RICH_REVIEW_GG_RV();
$NJT_GG_RICH_REVIEW->init();