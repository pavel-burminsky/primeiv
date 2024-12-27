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


<h4 class="njt-widget-toggler"><?php _e('Advanced Options:', 'njt-google-reviews');?><span></span></h4>
<div class="advanced-options toggle-item">
	<p>
		<label for="<?php echo $this->get_field_id('shadow'); ?>"><?php _e('Box Shadow', 'njt-google-reviews');?></label>
		<input class="widefat review_carousel"   id="<?php echo $this->get_field_id('shadow'); ?>" name="<?php echo $this->get_field_name('shadow'); ?>" type="checkbox" placeholder="<?php echo (empty($shadow) ? '20' : ''); ?>" <?php checked('yes', $shadow, true);?> value="yes"/>
		   <?php _e('enable shadow', 'njt-google-reviews' ); ?>
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
				<?php
}?>
		</select>


	</p>
	<p class="njt-clearfix">
		<span class="cache-message"></span>
		<a href="javascript:void(0)" class="button njt-clear-cache" title="<?php _e('Clear', 'njt-google-reviews');?>" data-transient-id-1="njt_grv_shortcode_badge_api_<?php echo substr($place_id, 0, 25); ?>" data-transient-id-2="njt_grv_shortcode_badge_<?php echo substr($place_id, 0, 25); ?>"><?php _e('Clear Cache', 'njt-google-reviews');?></a>
		<!-- <span class="cache-clearing-loading spinner"></span> -->
	</p>
</div>


