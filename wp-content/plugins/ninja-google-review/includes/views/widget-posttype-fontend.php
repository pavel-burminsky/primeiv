
<?php
$website = isset($options_by_locationID['url']) ? $options_by_locationID['url'] : '';

$name = isset($options_by_locationID['name']) ? $options_by_locationID['name'] : __('Sorry, this business does not have a proper Place ID set.', 'njt-google-reviews');
$ratings_count = isset($options_by_locationID['user_ratings_total']) ? intval($options_by_locationID['user_ratings_total']) : 0;
$place_avatar = $options_by_locationID['place_avatar'];
?>
<?php
if ($hide_header !== '1') {?>
	<div class="njt-header">
			<div class="njt-header-image">
				<a target="_blank" href="<?php echo $website ?>" >
					<img title="<?php echo $name ?>" src="<?php echo $place_avatar ?>" alt="<?php echo $name ?>"/>
				</a>
			</div>
			<div class="njt-header-content">
				<div class="njt-header-title">
					<a href="<?php echo $website ?>" target="_blank" >
						<?php echo $name ?>
					</a>

				</div>
			<?php
$overall_rating = isset($options_by_locationID['rating']) ? $options_by_locationID['rating'] : '';
    if ($overall_rating) {
        echo $reviews->get_star_rating($overall_rating, null, $hide_out_of_rating, $hide_google_image);
    } else {

        ?>
					<span class="no-reviews-header"><?php
$googleplus_page = isset($options_by_locationID['url']) ? $options_by_locationID['url'] : '';
        echo sprintf(__('<a href="%1$s" class="leave-review" target="_blank" class="new-window">Write a review</a>', 'njt-google-reviews'), esc_url($googleplus_page));?></span>
				<?php }?>
			</div>
	</div>
     <?php }?>

<?php if (count($postReviews) > 0) {?>
<div data-column="<?php echo $column ?>" class="njt-reviews-wrap njt-reviews-column-<?php echo $column ?> <?php echo !empty($carousel) ? 'njt-reviews-carousel-wrap njt-reviews-carousel-column' : '' ?> ">
<?php
$counter = 1;
    if ($review_limit <= 0) {
        $review_limit = count($postReviews);
    }
    foreach ($postReviews as $previews) {
        $author_name = get_post_meta($previews->ID, 'author_name', true);
        $author_url = get_post_meta($previews->ID, 'author_url', true) ? get_post_meta($previews->ID, 'author_url', true) : '';
        $overall_rating = get_post_meta($previews->ID, 'rating', true);

        $review_text = urldecode(get_post_meta($previews->ID, 'text', true));
        // $time = get_post_meta($previews->ID, 'time', true);
        $time = get_post_meta($previews->ID, 'relative_time_description', true);
        $avatar = get_post_meta($previews->ID, 'avatar', true) ? get_post_meta($previews->ID, 'avatar', true) : NJT_PLUGIN_PATH . '/assets/images/mystery-man.png';
        $review_filter = ($review_filter != "none") ? $review_filter : '0';
        if ($overall_rating >= $review_filter && $counter <= $review_limit) {?>
    <div style="width:<?php echo ((1 / $column) * 100) ?>%" class="njt-review njt-review-<?php echo $counter; ?>">
        <div class="njt-review-header">
            <div class="njt-review-avatar">
                <img src="<?php echo $avatar; ?>" alt="<?php echo $author_name; ?>" title="<?php echo $author_name; ?>"/>
            </div>
            <div class="njt-review-info">
                <span class="grp-reviewer-name">
                    <?php if (!empty($author_url)) {?>
                        <a target="_blank" href="<?php echo $author_url; ?>"
                                           title="<?php _e('View this profile.', 'njt-google-reviews');?>" <?php echo $target_blank . $no_follow; ?>><span><?php echo $author_name; ?></span></a>
                    <?php } else {?>
                            <?php echo $author_name; ?>
                    <?php }?>
                </span>
                <?php echo $reviews->get_star_rating($overall_rating, $time, $hide_out_of_rating, false); ?>
            </div>
        </div>

        <div class="njt-review-content">
            <?php $id = rand(0, 99999999);
            if (str_word_count(trim($review_text)) > $review_characters) {?>
                    <div id="review-<?php echo $id ?>">
                        <span  class="review-item review-item-short ">
                            <?php echo wp_trim_words($review_text, $review_characters, '...'); ?>
                        </span>
                        <span  style="display: none;" class="review-item review-item-long ">
                            <?php echo $review_text ?>
                        </span>
                    </div>
                    <a href="javascript:void(0)" data-id="review-<?php echo $id ?>"  class="btn-reivew"> <span>
                        <?php _e('Read more', 'njt-google-reviews');?>
                        </span>
                    </a>
            <?php } else {?>
                    <span class="review-item review-item-short ">
                    <?php echo $review_text; ?>
                    </span>
            <?php }?>
        </div>
    </div>
<?php $counter++;}?>
<?php }?>
</div>
<?php $googleplus_page = isset($options_by_locationID['url']) ? $options_by_locationID['url'] : '';?>
	<?php if ($ratings_count > 5 && !empty($googleplus_page)) {?>
		<div class="njt-read-all-reviews">
			<a style="color:#96588a" href="<?php echo esc_url($googleplus_page) ?>" target="_blank"><?php printf(esc_html__('Read All %d Reviews', 'njt-google-reviews'), $ratings_count);?></a>
		</div>
    <?php }?>
<?php } else {?>
    <div class="njt-no-reviews-wrap">

				<p class="no-reviews"><?php

    $googleplus_page = isset($options_by_locationID['url']) ? $options_by_locationID['url'] : '';

    echo sprintf(__('There are no reviews yet for this business. <a href="%1$s" class="leave-review" target="_blank">Be the first to review</a>', 'njt-google-reviews'), esc_url($googleplus_page));?></p>



			</div>
<?php }?>