<?php
$settings_link = admin_url( 'admin.php?page=wpbr-settings' );
$tutorial_link = admin_url( 'admin.php?page=wpbr-settings&wpbr_subtab=video-overview&wpbr_tab=help' );
?>

<div id="wpbr-activation-banner" class="wpbr-activation-banner notice is-dismissible">
	<div class="wpbr-activation-banner__logo">
		<img src="<?php esc_attr_e( WPBR_ASSETS_URL . 'images/platform-icon-wpbr.png' ) ?>" alt="">
	</div>
	<div class="wpbr-activation-banner__body">
		<h2 class="wpbr-activation-banner__heading">
			<?php esc_html_e( 'Welcome to WP Business Reviews', 'wp-business-reviews' );  ?>
		</h2>
		<p class="wpbr-activation-banner__description">
			<?php esc_html_e( 'Display your best online reviews on the most important online marketing platform—your website.', 'wp-business-reviews' );  ?>
		</p>
		<ul class="wpbr-inline-list">
			<li class="wpbr-inline-list__item">
				<a href="<?php echo esc_url( $settings_link ); ?>">
					<i class="fas wpbr-icon wpbr-fw wpbr-cogs"></i>
					<?php esc_html_e( 'Go to Settings', 'wp-business-reviews' ); ?>
				</a>
			</li>
			<li class="wpbr-inline-list__item">
			<a href="<?php echo esc_url( $tutorial_link ); ?>">
					<i class="fas wpbr-icon wpbr-fw wpbr-play-circle"></i>
					<?php esc_html_e( 'View Tutorial', 'wp-business-reviews' ); ?>
				</a>
			</li>
		</ul>
	</div>
</div>

