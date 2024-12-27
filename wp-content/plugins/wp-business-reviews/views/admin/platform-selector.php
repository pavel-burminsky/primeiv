<?php
$platform_count = count( $this->active_platforms );
?>

<div class="wpbr-platform-selector">
	<?php
	/** This action is documented in views/plugin-settings/main.php */
	do_action( 'wpbr_admin_notices' );
	?>
	<div class="wpbr-platform-selector__platforms">
		<ul class="wpbr-platform-gallery">
			<?php
			foreach ( $this->active_platforms as $platform => $platform_name ) :
				$platform_slug = str_replace( '_', '-', $platform );
				$image_url     = WPBR_ASSETS_URL . "images/platform-icon-{$platform_slug}.png";
				$cta_link      = '';
				$cta_class     = 'button';

				// Set platform components.
				if ( 'review_tag' === $platform ) {
					// Handle Review Tags with specific logic for detecting terms.
					$terms = get_terms( array(
						'taxonomy'   => 'wpbr_review_tag',
						'hide_empty' => false,
					) );

					if ( empty( $terms ) ) {
						$cta_class = 'button';
						$cta_text = sprintf( __( 'Add Your First Tag', 'wp-business-reviews' ) );
						$cta_link = add_query_arg( array(
							'taxonomy' => 'wpbr_review_tag',
						), admin_url( 'edit-tags.php' ) );
					} else {
						$cta_class = 'button button-primary';
						$cta_text = sprintf( __( 'Add %1$s Collection', 'wp-business-reviews' ), esc_html( $platform_name ) );
						$cta_link = add_query_arg( array(
							'page'          => 'wpbr-builder',
							'wpbr_platform' => $platform,
						), admin_url( 'admin.php' ) );
					}

				} elseif ( in_array( $platform, array_keys( $this->connected_platforms ) ) ) {
					// Platform is connected.
					$cta_class = 'button button-primary';
					$cta_text = sprintf( __( 'Add %1$s Collection', 'wp-business-reviews' ), esc_html( $platform_name ) );
					$cta_link = add_query_arg( array(
						'page'          => 'wpbr-builder',
						'wpbr_platform' => $platform,
					), admin_url( 'admin.php' ) );
				} else {
					// Platform is not connected.
					$cta_text = sprintf( __( 'Connect to %1$s', 'wp-business-reviews' ), esc_html( $platform_name ) );
					$cta_link = add_query_arg( array(
						'page'        => 'wpbr-settings',
						'wpbr_tab'    => 'platforms',
						'wpbr_subtab' => $platform,
					), admin_url( 'admin.php' ) );
				}
				?>

				<li class="wpbr-platform-gallery__item wpbr-platform-gallery__item--<?php echo esc_attr( $platform_count ); ?>">
					<div class="wpbr-card wpbr-card--pad">
						<img class="wpbr-platform-gallery__image" src="<?php echo esc_attr( $image_url ); ?>" alt="">
						<a class="<?php echo esc_attr( $cta_class ); ?>" href="<?php echo esc_url( $cta_link ); ?>">
							<?php echo esc_html( $cta_text ); ?>
						</a>
					</div>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</div>
