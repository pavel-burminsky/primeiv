<?php
$website = isset($options_by_locationID['url']) ? $options_by_locationID['url'] : '';

$name = isset($options_by_locationID['name']) ? $options_by_locationID['name'] : __('Sorry, this business does not have a proper Place ID set.', 'njt-google-reviews');
$ratings_count = isset($options_by_locationID['user_ratings_total']) ? intval($options_by_locationID['user_ratings_total']) : 0;
$place_avatar = $options_by_locationID['place_avatar'];
?>
	<div class="njt-header njt-header-badge">
			<div class="njt-header-image">
					<a href="<?php echo $website ?>" target="_blank"><img title="<?php echo $name?>" src="<?php echo $place_avatar ?>" alt="<?php echo $name?>"></a>
			</div>
			<div class="njt-header-content">
				<div class="njt-header-title">
					<a href="<?php echo $website ?>" target="_blank">
				<?php echo $name ?>
				</a>
				</div>
			<?php
$overall_rating = isset($options_by_locationID['rating']) ? $options_by_locationID['rating'] : '';
if ($overall_rating) {
    echo $reviews->get_star_rating($overall_rating, null, $hide_out_of_rating, $hide_google_image);
}
?>
			</div>
	</div>