<?php 
	settings_fields( 'njt_options_group');
	$place_id = get_option('njt_google_place_id');
	$options_by_locationID = get_option($place_id);
	// $place_name = get_option('njt_google_rich_place_name');
	$name =get_option('njt_google_rich_name'); 	
	$desctions = get_option('njt_google_rich_descritions');
	$def_des = get_bloginfo('description' );
	$locations = get_option('njt_gg_reviews_location', array());
	if (empty($locations)) {
		$locations = array();
	}
?>

<div class="form-njt-google-rich-snippet">
	<table class="form-table njt-google-rich-snippet" style="width: 50%">
			<tr>
					<th scope="row"><label for="njt-autocomplete"><?php _e('Location', 'njt-google-reviews');?>:
									<?php ?></label></label></th>
					<td>
							<select id="location_id" name="njt_google_place_id" class="regular-text">
									<option value="<?php echo esc_attr('none_location'); ?>"><?php _e('Select a location', 'njt-google-reviews');?></option>
			<?php
		if (count($locations) > 0) {
			foreach ($locations["place_id"] as $k => $v) {
				echo '<option data-location="' . $locations["location_name"][$k] . '" value="' . $v . '"';
				echo $v == $place_id ? ' selected' : '';
				echo '>' . $locations["location_look"][$k] . '</option>';
			}
		}
	?>
									<!-- <tr>
		<th scope="row"><label for="njt-autocomplete-snippet"><?php //_e( 'Location Lookup', 'njt-google-reviews' ); ?>: <?php  ?></label></label></th>
		<td>
			<input class="njt-autocomplete-snippet regular-text" id="njt-autocomplete-snippet"
						name="" type="text"/>
		</td>
	</tr>

	<tr>
		<th>
			<label for="location-rich"><?php //_e( 'Location', 'njt-google-reviews' ); ?>: </label>
		</th>
		<td>
			<input class="location-rich regular-text"  readonly
						id="location-rich"
					name="njt_google_rich_place_name" type="text"
						value="<?php //echo isset($place_name)?$place_name:'' ?>" 
						placeholder="<?php //echo empty($place_name)?'No location set':'' ?>"
						/>
		
		</td>
	</tr>
	<tr>
		<th>
			<label for="njt_google_place_id"><?php //_e( 'Location Place ID', 'njt-google-reviews' ); ?>:</label>
		</th>
		<td>
			<input class="njt_google_place_id regular-text" readonly id="njt_google_place_id1" name="njt_google_place_id1" type="text" placeholder="No location set " value="<?php //echo isset($place_id)?$place_id:'' ?>"/>
		
		</td>
	</tr> -->
			<tr>
					<th>
							<label for="njt_google_rich_name"><?php _e( 'Rich Name', 'njt-google-reviews' ); ?>:</label>
					</th>
					<td>
							<input class="njt_google_rich_name regular-text " id="njt_google_rich_name" name="njt_google_rich_name"
									type="text" placeholder="<?php echo get_bloginfo('sitename') ?>"
									value="<?php echo isset($name)?$name:'' ?>" />
					</td>
			</tr>
			<tr>
					<th>
							<label
									for="njt_google_rich_descritions"><?php _e( 'Rich Descriptions', 'njt-google-reviews' ); ?>:</label>
					</th>
					<td>
							<textarea class="njt_google_rich_descritions regular-text" id="njt_google_rich_descritions"
									name="njt_google_rich_descritions"><?php echo !empty($desctions)?$desctions:$def_des; ?></textarea>
					</td>
			</tr>
			<!--  -->
			<tr>
					<th>
							<label
									for="njt_google_rich_image_site"><?php _e( 'Image site', 'njt-google-reviews' ); ?>:</label>
					</th>
					<td>
							<input disabled type="text" class="njt_google_rich_image_site regular-text nta-input-required <?php echo(empty($options_by_locationID['place_avatar']) && $place_id != 'none_location' ? 'nta-input-error' : '')?>" id="njt_google_rich_image_site"
									name="njt_google_rich_image_site" value="<?php echo(!empty($options_by_locationID['place_avatar']) ? esc_attr($options_by_locationID['place_avatar']) : '')?>"/>
							<?php if(empty($options_by_locationID['place_avatar']) && $place_id != 'none_location') {?>
								<p class="description nta-text-required"><?php _e( 'This field is required, please complete ', 'njt-google-reviews' ); ?><a href="https://business.google.com/" class="new-window" target="_blank"><?php _e( 'your location\'s info.', 'njt-google-reviews' ); ?></a></p>
							<?php }?>
					</td>
			</tr>
			<tr>
					<th>
							<label
									for="njt_google_rich_phone"><?php _e( 'Phone', 'njt-google-reviews' ); ?>:</label>
					</th>
					<td>
							<input disabled type="text" class="njt_google_rich_phone regular-text nta-input-required <?php echo(empty($options_by_locationID['international_phone_number']) && $place_id != 'none_location' ? 'nta-input-error' : '')?>" id="njt_google_rich_phone"
									name="njt_google_rich_phone" value="<?php echo(!empty($options_by_locationID['international_phone_number']) ? esc_attr($options_by_locationID['international_phone_number']) : '')?>"/>
									<?php if(empty($options_by_locationID['international_phone_number']) && $place_id != 'none_location') {?>
										<p class="description nta-text-required"><?php _e( 'This field is required, please complete ', 'njt-google-reviews' ); ?><a href="https://business.google.com/" class="new-window" target="_blank"><?php _e( 'your location\'s info.', 'njt-google-reviews' ); ?></a></p>
									<?php }?>
					</td>
			</tr>
			<tr>
					<th>
							<label
									for="njt_google_rich_weburl"><?php _e( 'Website Url', 'njt-google-reviews' ); ?>:</label>
					</th>
					<td>
							<input disabled type="text" class="njt_google_rich_weburl regular-text nta-input-required <?php echo(empty($options_by_locationID['website']) && $place_id != 'none_location' ? 'nta-input-error' : '')?>" id="njt_google_rich_weburl"
									name="njt_google_rich_weburl" value="<?php echo(!empty($options_by_locationID['website']) ? esc_attr($options_by_locationID['website']) : '')?>"/>
							<?php if(empty($options_by_locationID['website']) && $place_id != 'none_location') {?>
								<p class="description nta-text-required"><?php _e( 'This field is required, please complete ', 'njt-google-reviews' ); ?><a href="https://business.google.com/" class="new-window" target="_blank"><?php _e( 'your location\'s info.', 'njt-google-reviews' ); ?></a></p>
							<?php }?>
					</td>
			</tr>
			<tr>
					<th>
							<label
									for="njt_google_rich_address"><?php _e( 'Address', 'njt-google-reviews' ); ?>:</label>
					</th>
					<td>
							<textarea disabled class="njt_google_rich_address regular-text nta-input-required <?php echo(empty($options_by_locationID['formatted_address']) && $place_id != 'none_location' ? 'nta-input-error' : '')?>" id="njt_google_rich_address"
							name="njt_google_rich_address"><?php echo(!empty($options_by_locationID['formatted_address']) ? esc_textarea($options_by_locationID['formatted_address']) : '') ?></textarea>
							<?php if(empty($options_by_locationID['formatted_address']) && $place_id != 'none_location' ) {?>
								<p class="description nta-text-required"><?php _e( 'This field is required, please complete ', 'njt-google-reviews' ); ?><a href="https://business.google.com/" class="new-window" target="_blank"><?php _e( 'your location\'s info.', 'njt-google-reviews' ); ?></a></p>
							<?php }?>
					</td>		
			</tr>
			<!--  -->
			<tr>
			<th>
			<?php 
				submit_button();
			?>
			</th>
			
			</tr>
	</table>
	<div class="njt-example-gg-rich-snippet-gg-rv" style="width: 50%">
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
			<div class="nta-searchresult">
			<div class="nta-link-snippet">
				<?php echo(str_replace("http://", "", esc_url( home_url( '/' ) )))?>
				<button type="button">▼</button>
				<h2 class="name"><?php  echo($name)?></h2>
			</div>
				<p class="nta-descriptions"><?php echo($desctions)?></p>
				<div class="snippet-rate-vote">
					<div class="star-rating">
						<?php
							$options_by_locationID = get_option($place_id);
							$overall_rating = isset($options_by_locationID['rating']) ? $options_by_locationID['rating'] : '';
							if ($overall_rating) {
								$reviews = new google_api(null, $place_id);
									echo $reviews->get_star_rating($overall_rating, null, null, null);
							} else {
								?>
									<div class="njt-fr-starslist-wrapper njt-fr-starslist-wrapper-google-review">
											<div class="njt-fr-starslist-container">
													<div class="njt-fr-starslist-background">
															<div class="njt-fr-star"></div>
															<div class="njt-fr-star"></div>
															<div class="njt-fr-star"></div>
															<div class="njt-fr-star"></div>
															<div class="njt-fr-star"></div>
													</div>
											</div>
									</div>
								<?php
							}
						?>
					</div>
					<div >
						<?php _e( 'Rating:', 'njt-google-reviews' ); ?>
						<span class="nta-rating"><?php echo($options_by_locationID['rating'] ? $options_by_locationID['rating'] : 0);?></span>
						-
						<span class="nta-ratings_count"><?php echo(number_format($options_by_locationID['user_ratings_total'],0,"","."));?></span>
						<?php _e( 'reviews', 'njt-google-reviews' ); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="clear"></div>