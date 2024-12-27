
<?php do_action('njt_page_before');?>
	<?php
$screen = get_current_screen();

if ($screen->base == 'google-reviews_page_njt-ggreviews-locations') {
    ?>
	<div id="njt-plugin-settings" class="nta-mgr-r20">

	<form action="options.php" method="post" id="njt-plugin-options-form" class="nta-list-location pd-0-20">
		<?php
$file_location = $this->views . 'location.php';
    if (file_exists($file_location)) {
        include $file_location;
    } else {
        trigger_error('Option file <strong>' . $file_location . '</strong> not found!', E_USER_NOTICE);
    }
    ?>
    <?php submit_button();?>
	</form>
</div>
<?php
}
do_action('njt_page_after');?>