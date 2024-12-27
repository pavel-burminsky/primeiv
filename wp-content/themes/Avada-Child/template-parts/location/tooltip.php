<?php
$tooltip_content = $args['tooltip_content'];
?>

<div class="tooltip-icon">
	<img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/tooltip.svg" alt="Tooltip">
	<div class="location-tootip">
		<p><?php echo wp_kses_post( $tooltip_content ); ?></p>
	</div>
</div>