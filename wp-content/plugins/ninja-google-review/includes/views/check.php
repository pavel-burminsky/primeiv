<?php 
$value = get_option($option['id']);
	settings_fields( 'njt_options_group');
?>
<tr>
	<th scope="row"><label for="njt-plugin-field-<?php echo $option['id']; ?>"><?php echo $option['name']; ?></label></th>
	<td>
		<input <?php checked( "yes", $value, true ); ?> type="checkbox" value="yes" name="<?php echo $option['id']; ?>" id="njt-plugin-field-<?php echo $option['id']; ?>" class="regular-text" />
		<p class="description"><?php echo $option['desc']; ?></p>
	</td>
</tr>

