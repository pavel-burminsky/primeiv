
<?php do_action('njt_page_before');?>
	<?php
$screen = get_current_screen();

if ($screen->base == 'google-reviews_page_njt-ggreviews-settings') {
    ?>
	<div id="njt-plugin-settings" class="nta-mgr-r20">
	<h1 id="njt-plugin-tabs" class="nav-tab-wrapper hide-if-no-js">
		<?php
// Show tabs
    $this->render_tabs_general_settings();
    ?>
	</h1>
	<form action="options.php" method="post" id="njt-plugin-options-form" class="pd-0-20">
		<?php
$this->render_panes_general_settings();
    ?>
	</form>
</div>
<?php
}
do_action('njt_page_after');?>
