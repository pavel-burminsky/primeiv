<?php
if ( ! empty( $this->field_args['name'] ) ) {
	$this->render_partial( WPBR_PLUGIN_DIR . 'views/field/partials/name.php' );
}
?>

<div id="wpbr-field-control-wrap-<?php echo esc_attr( $this->field_id ); ?>" class="wpbr-field__control-wrap">
	<?php if ( ! empty( $this->value ) ) : ?>
		<ul class="wpbr-stacked-list wpbr-stacked-list--striped wpbr-stacked-list--border">
			<?php foreach ( $this->value as $page_id => $page_atts ) : ?>
				<?php
				$image_url = 'https://graph.facebook.com/' . $page_id . '/picture';
				$page_name = ! empty( $page_atts['name'] ) ? $page_atts['name'] : '';
				$page_url  = 'https://facebook.com/' . $page_id;
				?>
				<li class="wpbr-stacked-list__item wpbr-stacked-list__item--border-bottom">
					<div class="wpbr-media">
						<div class="wpbr-media__figure wpbr-media__figure--medium wpbr-media__figure--round">
							<img src="<?php echo esc_url( $image_url ); ?>">
						</div>
						<div class="wpbr-media__body">
							<div class="wpbr-review-source">
								<a class="wpbr-review-source__name" href="<?php echo esc_url( $page_url ); ?>" target="_blank" rel="noopener noreferrer">
									<?php echo esc_html( $page_name ); ?>
								</a>
								<span class="wpbr-review-source__id">
									<?php printf( esc_html__( 'ID: %s', 'wp-business-reviews' ), $page_id ); ?>
								</span>
							</div>
						</div>
					</div>
				</li>
			<?php endforeach; ?>
		</ul>
		<input class="js-wpbr-fb-disconnect-input" type="hidden" name="wpbr_disconnect_facebook">
		<button class="button js-wpbr-fb-disconnect-button"><?php esc_html_e( 'Disconnect Facebook', 'wp-business-reviews' ) ?></button>
	<?php else : ?>
		<?php
		if ( 'development' === WPBR_ENV ) {
			$scheme = 'https';
			$host   = defined( 'WPBR_SERVER_URL' ) ? WPBR_SERVER_URL : 'wpbusinessreviews.com';
		} else {
			$scheme = 'https';
			$host   = 'wpbusinessreviews.com';
		}

		$redirect_url = wp_nonce_url(
			admin_url( 'admin.php?page=wpbr-settings&wpbr_subtab=facebook&wpbr_tab=platforms' ),
			'wpbr_facebook_token_save',
			'wpbr_facebook_token_nonce'
		);
		$request_url  = add_query_arg( array(
			'wpbr_redirect' => urlencode( $redirect_url ),
		), $scheme . '://' . $host . '/facebook-token/request/' );
		?>

		<a class="wpbr-facebook-button" href="<?php echo esc_url( $request_url ); ?>">
			<i class="fab wpbr-icon wpbr-facebook-square"></i>
			<span class="wpbr-facebook-button__text"><?php _e( 'Continue with Facebook', 'wp-business-reviews' ); ?></span>
		</a>

		<?php
		if ( ! empty( $this->field_args['description'] ) ) {
			$this->render_partial( WPBR_PLUGIN_DIR . 'views/field/partials/description.php' );
		}
		?>
	<?php endif; ?>
</div>
