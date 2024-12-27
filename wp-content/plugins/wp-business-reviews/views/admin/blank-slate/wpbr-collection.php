<?php
$icon_url   = WPBR_ASSETS_URL . 'images/platform-icon-wpbr.png';
$heading    = __( 'No collections found.', 'wp-business-reviews' );
$message    = __( 'Watch a video tutorial or jump straight into managing your platforms.', 'wp-business-reviews' );
$cta_icon_1 = 'video';
$cta_text_1 = __( 'View Tutorial', 'wp-business-reviews' );
$cta_link_1 = admin_url( 'admin.php?page=wpbr-settings&wpbr_subtab=video-platform-management&wpbr_tab=help' );
$cta_icon_2 = 'cogs';
$cta_text_2 = __( 'Manage Platforms', 'wp-business-reviews' );
$cta_link_2 = admin_url( 'admin.php?page=wpbr-settings&wpbr_subtab=platforms&wpbr_tab=platforms' );
?>

<div class="wpbr-blank-slate">
	<img class="wpbr-blank-slate__icon" src="<?php echo esc_url( $icon_url ); ?>" alt="">
	<h2 class="wpbr-blank-slate__heading"><?php echo esc_html( $heading ); ?></h2>
	<p class="wpbr-blank-slate__message"><?php echo esc_html( $message ); ?></p>
	<ul class="wpbr-blank-slate__nav">
		<li class="wpbr-blank-slate__nav-item">
			<a class="wpbr-blank-slate__cta button button-primary button-hero" href="<?php echo esc_url( $cta_link_1 ); ?>">
				<i class="fas wpbr-icon wpbr-fw wpbr-<?php echo esc_attr( $cta_icon_1 ); ?>"></i>
				<?php echo esc_html( $cta_text_1 ); ?>
			</a>
		</li>
		<li class="wpbr-blank-slate__nav-item">
			<a class="wpbr-blank-slate__cta button button-hero" href="<?php echo esc_url( $cta_link_2 ); ?>">
				<i class="fas wpbr-icon wpbr-fw wpbr-<?php echo esc_attr( $cta_icon_2 ); ?>"></i>
				<?php echo esc_html( $cta_text_2 ); ?>
			</a>
		</li>
	</ul>
</div>
