<?php
$cards = array(
	array(
		'heading'     => __( 'Plugin Docs', 'wp-business-reviews' ),
		'icon'        => 'file-alt',
		'description' => __( 'Need help? Explore our extensive plugin documentation on our website.', 'wp-business-reviews' ),
		'cta_text'    => __( 'View Documentation', 'wp-business-reviews' ),
		'cta_link'    => 'https://wpbusinessreviews.com/documentation/'
	),
	array(
		'heading'     => __( 'Support', 'wp-business-reviews' ),
		'icon'        => 'life-ring',
		'description' => __( 'Still have questions? Get connected to our experienced team of support technicians.', 'wp-business-reviews' ),
		'cta_text'    => __( 'Get Support', 'wp-business-reviews' ),
		'cta_link'    => 'https://wpbusinessreviews.com/support/'
	),
	array(
		'heading'     => __( 'Your Account', 'wp-business-reviews' ),
		'icon'        => 'user-circle',
		'description' => __( 'Looking for past purchases and licenses? It\'s all there inside your account. ', 'wp-business-reviews' ),
		'cta_text'    => __( 'View Account', 'wp-business-reviews' ),
		'cta_link'    => 'https://wpbusinessreviews.com/account/'
	),
)
?>
<div class="wpbr-help-dashboard">
	<div class="wpbr-help-dashboard__main">
		<?php $this->render_partial( WPBR_PLUGIN_DIR . 'views/plugin-settings/viewer.php' ); ?>
	</div>
	<?php foreach ( $cards as $content ) : ?>
		<div class="wpbr-help-dashboard__card">
			<div class="wpbr-card wpbr-card--pad">
				<h3 class="wpbr-card__heading">
					<i class="fas wpbr-icon wpbr-<?php echo esc_attr( $content['icon'] ); ?>"></i>
					<?php echo esc_html( $content['heading'] ); ?>
				</h3>
				<p class="wpbr-card__description">
					<?php echo esc_html( $content['description'] ); ?>
				</p>

				<?php if ( isset( $content['cta_text'] ) && isset( $content['cta_link'] ) ) : ?>
					<a class="wpbr-card__cta" href="<?php echo esc_url( $content['cta_link'] ); ?>" target="_blank" rel="noopener noreferrer">
						<?php echo esc_html( $content['cta_text'] ); ?>
						<i class="fas wpbr-icon wpbr-external-link-alt"></i>
					</a>
				<?php endif; ?>
			</div>
		</div>
	<?php endforeach; ?>
</div>
