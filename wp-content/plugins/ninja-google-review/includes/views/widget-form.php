<p>
	<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Widget Title', 'njt-google-reviews');?></label>
	<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>"
	name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>"/>
</p>
<?php
$locations = get_option('njt_gg_reviews_location', array());
if (empty($locations)) {
    $locations = array();
}

?>

<div class="set-business">
	<!-- -->
	<p class="">
		<label for="<?php echo $this->get_field_id('place_id'); ?>"><?php _e('Location', 'njt-google-reviews');?><?php ?></label>

			   <select class="widget_gg_place_id" style="width:100%">
				<option value=""><?php _e( 'Select a location', 'njt-google-reviews' ); ?></option>
				<?php
if (count($locations) > 0) {
    foreach ($locations["place_id"] as $k => $v) {

        ?>
		<option  data-location="<?php echo $locations["location_name"][$k] ?>" value="<?php echo $v ?>" <?php if ($place_id == $v) {
            echo 'selected="selected"';
        }
        ?> ><?php echo $locations["location_look"][$k]; ?></option>
		<?php
}
}
?>
		</select>
		</p>
		<!-- -->
	<p>

		<input type="hidden" class="widefat location njt-gg-widget-place-id"
			   id="<?php echo $this->get_field_id('place_id'); ?>"
		       name="<?php echo $this->get_field_name('place_id'); ?>" type="text"
		       placeholder="<?php echo (empty($place_id) ? 'No place_id set' : ''); ?>"
		       value="<?php echo $place_id; ?>"/>
	</p>
	<p>

		<input type="hidden" class="widefat location njt-gg-widget-location-name"
			   id="<?php echo $this->get_field_id('location'); ?>"
		       name="<?php echo $this->get_field_name('location'); ?>" type="text"
		       placeholder="<?php echo (empty($location) ? 'No location set' : ''); ?>"
		       value="<?php echo $location; ?>"/>
	</p>


</div>
<h4 class="njt-widget-toggler"><?php _e('Review Options', 'njt-google-reviews');?>:<span></span></h4>
<div class="review-options toggle-item">


	<!-- Filter Reviews -->
	<p class="pro-field">
		<label for="<?php echo $this->get_field_id('review_filter'); ?>"><?php _e('Minimum Review Rating', 'njt-google-reviews');?></label>

		<select id="<?php echo $this->get_field_id('review_filter'); ?>" class="widefat"
		        name="<?php echo $this->get_field_name('review_filter'); ?>" >

			<option value="none" <?php if (empty($review_filter) || $review_filter == 'No filter') {
    echo "selected='selected'";
}?>><?php _e('No filter', 'njt-google-reviews');?>
			</option>
			<option value="5" <?php if ($review_filter == '5') {
    echo "selected='selected'";
}?>><?php _e('5 Stars', 'njt-google-reviews');?>
			</option>
			<option value="4" <?php if ($review_filter == '4') {
    echo "selected='selected'";
}?>><?php _e('4 Stars', 'njt-google-reviews');?>
			</option>
			<option value="3" <?php if ($review_filter == '3') {
    echo "selected='selected'";
}?>><?php _e('3 Stars', 'njt-google-reviews');?>
			</option>
			<option value="2" <?php if ($review_filter == '2') {
    echo "selected='selected'";
}?>><?php _e('2 Stars', 'njt-google-reviews');?>
			</option>
			<option value="1" <?php if ($review_filter == '1') {
    echo "selected='selected'";
}?>><?php _e('1 Star', 'njt-google-reviews');?>
			</option>

		</select>

	</p>

	<!-- Review Limit -->
	<p>
		<label for="<?php echo $this->get_field_id('review_limit'); ?>"><?php _e('Limit Number of Reviews', 'njt-google-reviews');?></label>

		<input class="widefat review_limits"   id="<?php echo $this->get_field_id('review_limit'); ?>" name="<?php echo $this->get_field_name('review_limit'); ?>" type="text" placeholder="<?php echo (empty($review_limit) ? '5' : ''); ?>" value="<?php echo !empty($review_limit) ? $review_limit : 5; ?>"/>
	</p>

	<p>
		<label for="<?php echo $this->get_field_id('review_characters'); ?>"><?php _e('Characters reivew limit', 'njt-google-reviews');?></label>
		<input class="widefat review_characters"   id="<?php echo $this->get_field_id('review_characters'); ?>" name="<?php echo $this->get_field_name('review_characters'); ?>" type="text" placeholder="<?php echo (empty($review_characters) ? '20' : ''); ?>" value="<?php echo !empty($review_characters) ? $review_characters : 20; ?>"/>
	</p>

</div>

<h4 class="njt-widget-toggler"><?php _e('Display Options', 'njt-google-reviews');?>:<span></span></h4>

<div class="display-options toggle-item">

	<!-- Widget Theme -->


	<!-- Hide Places Header -->
	<p>
		<input id="<?php echo $this->get_field_id('hide_header'); ?>"
		       name="<?php echo $this->get_field_name('hide_header'); ?>" type="checkbox"
		       value="1" <?php checked('1', $hide_header);?> />
		<label for="<?php echo $this->get_field_id('hide_header'); ?>"><?php _e('Hide Business Information', 'njt-google-reviews');?></label>
	</p>


	<!-- Hide x Rating -->
	<!-- <p>
		<input id="<?php echo $this->get_field_id('hide_out_of_rating'); ?>" name="<?php echo $this->get_field_name('hide_out_of_rating'); ?>" type="checkbox" value="1" <?php checked('1', $hide_out_of_rating);?> />
		<label for="<?php echo $this->get_field_id('hide_out_of_rating'); ?>"><?php _e('Hide "x out of 5 stars" text', 'njt-google-reviews');?></label>
	</p> -->

	<!-- Show Google Image -->
	<!-- <p>
		<input id="<?php echo $this->get_field_id('hide_google_image'); ?>"
		       name="<?php echo $this->get_field_name('hide_google_image'); ?>" type="checkbox"
		       value="1" <?php checked('1', $hide_google_image);?> />
		<label for="<?php echo $this->get_field_id('hide_google_image'); ?>"><?php _e('Hide Google logo', 'njt-google-reviews');?></label>
	</p> -->


</div>


<h4 class="njt-widget-toggler"><?php _e('Advanced Options:', 'njt-google-reviews');?><span></span></h4>


<div class="advanced-options toggle-item">

	<p>
		<label for="<?php echo $this->get_field_id('show_write_btn'); ?>"><?php _e('Enable button write', 'njt-google-reviews');?></label>
		<input class="widefat review_carousel"   id="<?php echo $this->get_field_id('show_write_btn'); ?>" name="<?php echo $this->get_field_name('show_write_btn'); ?>" type="checkbox"  " <?php checked('yes', $show_write_btn, true);?> value="yes"/>
	</p>

	<p>
		<label for="<?php echo $this->get_field_id('btn_write'); ?>"><?php _e('Button write name', 'njt-google-reviews');?></label>
		<input class="widefat review_carousel"   id="<?php echo $this->get_field_id('btn_write'); ?>" name="<?php echo $this->get_field_name('btn_write'); ?>" type="text" placeholder="<?php echo (empty($btn_write) ? '20' : ''); ?>"  value="<?php echo $btn_write ?>"/>

	</p>



	<p>
		<label for="<?php echo $this->get_field_id('shadow'); ?>"><?php _e('Box Shadow', 'njt-google-reviews');?></label>
		<input class="widefat review_carousel"   id="<?php echo $this->get_field_id('shadow'); ?>" name="<?php echo $this->get_field_name('shadow'); ?>" type="checkbox" placeholder="<?php echo (empty($shadow) ? '20' : ''); ?>" <?php checked('yes', $shadow, true);?> value="yes"/>enable shadow

	</p>


	<p>
		<label for="<?php echo $this->get_field_id('carousel'); ?>"><?php _e('Carousel', 'njt-google-reviews');?></label>
		<input class="widefat review_carousel"   id="<?php echo $this->get_field_id('carousel'); ?>" name="<?php echo $this->get_field_name('carousel'); ?>" type="checkbox" <?php checked('yes', $carousel, true);?> value="yes"/>
	</p>

	<p>
        <label for="<?php echo $this->get_field_id('carousel_autoplay'); ?>"><?php _e('Carousel autoplay', 'njt-google-reviews');?></label>
        <input class="widefat review_carousel"   id="<?php echo $this->get_field_id('carousel_autoplay'); ?>" name="<?php echo $this->get_field_name('carousel_autoplay'); ?>" type="checkbox" <?php checked('yes', $carousel_autoplay, true);?> value="yes" />
    </p>

	<p>
        <label for="<?php echo $this->get_field_id('carousel_speed'); ?>"><?php _e('Carousel autoplay speed', 'njt-google-reviews');?></label>
        <input class="review_carousel" id="<?php echo $this->get_field_id('carousel_speed'); ?>" name="<?php echo $this->get_field_name('carousel_speed'); ?>" type="number" placeholder="<?php echo (empty($carousel_speed) ? '3000' : ''); ?>"  value="<?php echo $carousel_speed ?>"/><span>(ms)</span>
	</p>


	<p>
		<label for="<?php echo $this->get_field_id('column'); ?>"><?php _e('Column', 'njt-google-reviews');?></label>
		<select name="<?php echo $this->get_field_name('column'); ?>" id="<?php echo $this->get_field_id('column'); ?>" class="column_shortcode widefat">
			<option <?php selected("1", $column, true);?> value="1"><?php _e('1 Column', 'njt-google-reviews');?></option>
			<option  <?php selected("2", $column, true);?> value="2"><?php _e('2 Column', 'njt-google-reviews');?></option>

		</select>
	</p>
	<p>
		<label for="<?php echo $this->get_field_id('cache'); ?>"><?php _e('Cache Data', 'njt-google-reviews');?></label>
		<select name="<?php echo $this->get_field_name('cache'); ?>" id="<?php echo $this->get_field_id('cache'); ?>"
		        class="widefat">
			<?php $options = array(
    __('None', 'njt-google-reviews'),
    __('1 Hour', 'njt-google-reviews'),
    __('3 Hours', 'njt-google-reviews'),
    __('6 Hours', 'njt-google-reviews'),
    __('12 Hours', 'njt-google-reviews'),
    __('1 Day', 'njt-google-reviews'),
    __('2 Days', 'njt-google-reviews'),
    __('1 Week', 'njt-google-reviews'),
);

foreach ($options as $option) {
    ?>
				<option value="<?php echo $option; ?>"
				        id="<?php echo $option; ?>" <?php if ($cache == $option || empty($cache) && $option == '1 Day') {
        echo ' selected="selected" ';
    }?>>
					<?php echo $option; ?>
				</option>
				<?php }?>
		</select>


	</p>
	<p class="njt-clearfix">
		<span class="cache-message"></span>
		<a href="javascript:void(0)" class="button njt-clear-cache" title="<?php _e('Clear', 'njt-google-reviews');?>" data-transient-id-1="njt_grv_widget_<?php echo substr($place_id, 0, 25); ?>" data-transient-id-2="njt_grv_widget_options_<?php echo substr($place_id, 0, 25); ?>"><?php _e('Clear Cache', 'njt-google-reviews');?></a>
		<!--<span class="cache-clearing-loading spinner"></span>-->
	</p>
</div>


