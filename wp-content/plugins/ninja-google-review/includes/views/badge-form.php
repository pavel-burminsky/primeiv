<?php
$locations = get_option('njt_gg_reviews_location', array());
if (empty($locations)) {
    $locations = array();
}

?>
<div class="form-njt-create-badge">
	<table class="form-table njt-create-badge">
	<tr>
		<th scope="row"><label for="njt-autocomplete"><?php _e('Location', 'njt-google-reviews');?><?php ?></label></label></th>
		<td>
			<select style="max-width: 100%; width: 100%;" id="place_id">
					<option value=""><?php _e( 'Select a location', 'njt-google-reviews' ); ?></option>
					<?php
	if (count($locations) > 0) {
			foreach ($locations["place_id"] as $k => $v) {
					echo '<option data-location="' . $locations["location_name"][$k] . '" value="' . $v . '">' . $locations["location_look"][$k] . '</option>';
			}
	}
	?>
			</select>
		</td>
	</tr>
	<tr>
		<th>

			<label for="shadow-shortcode"><?php _e('Box Shadow', 'njt-google-reviews');?></label>
		</th>
		<td>
			 <label class="shortcode-switch" for="shadow-badge">
                <input class="widefat review_shadow" id="shadow-badge" name="shadow" type="checkbox" value="yes" />
                <div class="slider round"></div>
            </lable>
		</td>
	</tr>

	<tr>
		<th><label for="cache_badge"><?php _e('Cache Data', 'njt-google-reviews');?></label></th>
		<td>
			<select name="cache" id="cache_badge"
							class="cache">
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
					<option value="<?php echo $option; ?>" id="">
						<?php echo $option; ?>
					</option>
					<?php
	}?>
			</select>
		</td>
	</tr>
	<tr>
		<th><label for="shortcode_babe"><?php _e('Shortcode', 'njt-google-reviews');?></label></th>
		<td>
			<textarea id="shortcode_babe" onClick="this.select()" style="width: 100%" class="regular-text badge-content"></textarea>
			<a class="button button-primary" href="#" onclick="return false;"  id="btn-create-badge"> Create badge </a>
		</td>
	</tr>
	</table>
	<div class="badge-place-review">
		<div class="nta-browser">
			<div class="browser-top">   
				<div class="circle-div">
					<div class="circle red-circle"></div>
					<div class="circle yellow-circle"></div>
					<div class="circle green-circle"></div>
				</div>
				<div class="browser-ui-block">
					<div class="arrow-buttons"></div>
					<div class="search-box"></div>
				</div>
			</div>
			<div class="badge-place-review-shortcode">
				<!-- Place to review shortcode -->
			</div>
		</div>
	</div>
</div>