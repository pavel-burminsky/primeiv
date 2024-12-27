<?php
$list_option_schedule = array(
    // 'minute' => 'Minutes',
    // 'hour' => 'Hours',
    //'day' => 'Days',
    //'week' => 'Weeks',
    'month' => 'Months',
    'year' => 'Years',
);
$value = get_option($option['id']);
settings_fields('njt_options_group');
?>
<table class="form-table">
<tr>
	<th scope="row"><label for="njt-plugin-field-<?php echo $option['id']; ?>"><?php echo $option['name']; ?></label></th>
	<td>
		<input type="text" value="<?php echo isset($value) ? $value : ''; ?>" name="<?php echo $option['id']; ?>" id="njt-plugin-field-<?php echo $option['id']; ?>" class="regular-text" />
		<p class="description"><?php echo $option['desc']; ?></p>
	</td>
</tr>

<!-- === UPDATE 21-02-2019 === -->
<tr>
    <th scope="row"><label for="nta-wa-switch-control"><?php echo __("Auto Update Reviews", "njt-google-reviews"); ?></label></th>
        <td>
            <div class="nta-wa-switch-control">
                <input class="google_reviews_schedule_on_off" type="checkbox" id="nta-wa-switch" name="google_reviews_schedule_on_off" <?php echo get_option("google_reviews_schedule_on_off") ? 'checked=""' : ''; ?> >
                <label for="nta-wa-switch" class="green"></label>
            </div>
        </td>
</tr>

<tr style="display: none">
    <th scope="row"><label for="googlereviews_schedule_update"><?php echo __("Schedule Update Reviews", "njt-google-reviews"); ?></label></th>
    	 <td>
            <select id="googlereviews_schedule_update" name="googlereviews_schedule_update">
                <option value=""><?php _e('Select time to schedule update', 'njt-google-reviews');?></option>
                    <?php foreach ($list_option_schedule as $key => $value) {?>
                        <option <?php echo get_option('googlereviews_schedule_update') == $key ? 'selected="selected"' : ''; ?> value="<?php echo $key; ?>"><?php echo $value; ?></option>
                    <?php }?>
			</select>
        </td>
</tr>
<tr class="ggreviews_value_schedule ggreviews_show_hide_update_reviews">
    <th scope="row"><label for="googlereviews_schedule_value"><?php echo __("Time to update", "njt-google-reviews"); ?></label></th>
        <td><input placeholder="Enter Value schedule" id="googlereviews_schedule_value" name="googlereviews_schedule_value" value="<?php echo get_option('googlereviews_schedule_value'); ?>" type="text"><span class="show_option_schedule"><?php _e('month(s)', 'njt-google-reviews');?></span>
        </td>
</tr>
<!-- === UPDATE 21-02-2019 === -->
</table>