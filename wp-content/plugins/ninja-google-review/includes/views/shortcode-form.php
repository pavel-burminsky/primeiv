<?php
$locations = get_option('njt_gg_reviews_location', array());
if (empty($locations)) {
    $locations = array();
}
?>
<table class="form-table njt-creat-shortcode">
    <tr>
        <th colspan="2" class="table-heading">
            <label for="review_filter">
                <?php _e( 'Reviews', 'njt-google-reviews' ); ?>
            </label>
        </th>
    </tr>
    <tr>
        <th scope="row"><label for="njt-autocomplete"><?php _e('Location', 'njt-google-reviews');?>
            <?php ?></label></label></th>
        <td>
            <select style="width:50%; max-width: 100%" id="location_id">
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
        <!-- <th><h4 class="njt-widget-toggler"><?php _e('Review Options', 'njt-google-reviews');?>:<span></span></h4></th> -->
    </tr>

    <tr>
        <th>
            <label for="review_filter"><?php _e('Minimum Review Rating', 'njt-google-reviews');?></label>
        </th>
        <td>
            <select class="review_filter" id="review_filter" class="" name="review_filter">
                <option value="none"><?php _e('No filter', 'njt-google-reviews');?></option>
                <option value="5"><?php _e('5 Stars', 'njt-google-reviews');?></option>
                <option value="4"><?php _e('4 Stars', 'njt-google-reviews');?></option>
                <option value="3"><?php _e('3 Stars', 'njt-google-reviews');?></option>
                <option value="2"><?php _e('2 Stars', 'njt-google-reviews');?></option>
                <option value="1"><?php _e('1 Star', 'njt-google-reviews');?></option>
            </select>

        </td>
    </tr>

    <tr>
        <th>
            <label for="review_limit"><?php _e('Limit Number of Reviews', 'njt-google-reviews');?></label>
        </th>
        <td>
            <input class="review_limit" value="5" id="review_limit" name="review_limit" type="text"
                placeholder="Enter Limit Number of Reviews" />
        </td>
    </tr>

    <tr>
        <th>
            <label for="review_characters"><?php _e('Characters review limit', 'njt-google-reviews');?></label>
        </th>
        <td>
            <input class=" review_characters" value="20" id="review_characters" name="review_characters" type="text"
                placeholder="<?php echo (empty($review_characters) ? '20' : ''); ?>" />
        </td>
    </tr>
    <tr>
        <th colspan="2" class="table-heading">
            <label for="review_filter">
                <?php _e('Design', 'njt-google-reviews');?>
            </label>
        </th>
    </tr>
    <tr>
        <th>
            <label for="hide_header"><?php _e('Hide Business Information', 'njt-google-reviews');?></label>
        </th>
        <td>
            <label class="shortcode-switch" for="hide_header">
                <input id="hide_header" name="hide_header" type="checkbox" />
                <div class="slider round"></div>
            </lable>
        </td>
    </tr>

    <tr>
        <th>
            <label
                for="write-a-review-shortcode"><?php _e('Enable write a review button', 'njt-google-reviews');?></label>
        </th>
        <td>
            
            <label class="shortcode-switch" for="write-a-review-shortcode">
                <input class="widefat review_carousel" id="write-a-review-shortcode" name="write-a-review-shortcode"
                type="checkbox" value="yes" />
                <div class="slider round"></div>
            </lable>
        </td>
    </tr>

    <tr>
        <th>
            <label
                for="write-a-review-name-shortcode"><?php _e('Write a review button name', 'njt-google-reviews');?></label>
        </th>
        <td>
            <input class=" review_characters" value="Write a review" id="write-a-review-name-shortcode"
                name="write-a-review-name-shortcode" type="text"
                placeholder="<?php echo (empty($review_characters) ? 'Write a review' : ''); ?>" />
        </td>
    </tr>

    <tr>
        <th>
            <label for="shadow-shortcode"><?php _e('Box Shadow', 'njt-google-reviews');?></label>
        </th>
        <td>
            <label class="shortcode-switch" for="shadow">
                <input class="widefat review_shadow" id="shadow" name="shadow" type="checkbox" value="yes" />
                <div class="slider round"></div>
            </lable>
        </td>
    </tr>

    <tr>
        <th>
            <label for="carousel-shortcode"><?php _e('Carousel', 'njt-google-reviews');?></label>
        </th>
        <td>
            <label class="shortcode-switch" for="carousel">
                <input class="widefat review_carousel" id="carousel" name="carousel" type="checkbox" value="yes" />
                <div class="slider round"></div>
            </lable>
            
        </td>
    </tr>
   <tr>
       <th colspan="2" class="nta-carousel-option">
           <table class="nta-table-carousel">
               <tbody>
                    <tr class="hidden">
                        <th>
                            <label for="carousel-autoplay"><?php _e('Carousel autoplay', 'njt-google-reviews');?></label>
                        </th>
                        <td>
                            <label class="shortcode-switch" for="carousel-autoplay">
                            <input class="widefat review_carousel" id="carousel-autoplay" name="carousel-autoplay" type="checkbox" value="yes" />
                                <div class="slider round"></div>
                            </lable>
                        </td>
                    </tr>

                    <tr class="hidden">
                        <th>
                            <label for="carousel-speed"><?php _e('Carousel autoplay speed', 'njt-google-reviews');?></label>
                        </th>
                        <td>
                            <input class="review_carousel" id="carousel-speed" name="carousel-speed" type="number" value="3000" /><span>(ms)</span>
                        </td>
                    </tr>
                </tbody>
           </table>
       </th>
   </tr>
    
     
    <tr>
        <th>
            <label for="column_shortcode"><?php _e('Column', 'njt-google-reviews');?></label>
        </th>
        <td>
            <div class="button-group button-large">  
                <input id="column-1" type="radio" value="1" name="column_shortcode" class="column_shortcode" checked/>
                <label for="column-1" class="button">
                <?php _e('1 Column', 'njt-google-reviews');?>
                </label>
                <input id="column-2" type="radio" value="2" name="column_shortcode" class="column_shortcode"/>
                <label for="column-2" class="button">    
                <?php _e('2 Column', 'njt-google-reviews');?>
                </label>
            </div>
        </td>
    </tr>
    <tr>
        <th colspan="2" class="table-heading">
        </th>
    </tr>
    <tr>
        <th>
            <label for="cache"><?php _e('Cache Data', 'njt-google-reviews');?></label>
        </th>
        <td>
            <select name="cache" id="cache" class="cache">
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
        <th>

            <label for="cache"><?php _e('Shortcode', 'njt-google-reviews');?></label>
        </th>
        <td>
            <textarea onClick="this.select()" style="width: 50%" class="shorecode-content"></textarea>
            <div>
                <a class="button button-primary" href="#" onclick="return false;" id="btn-create-shortcode">
                    <?php _e('Create Shortcode', 'njt-google-reviews');?>
                </a>
            </div>
            
        </td>
    </tr>
</table>