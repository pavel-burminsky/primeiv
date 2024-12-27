<?php
if ( ! empty( $this->field_args['name'] ) ) {
	$this->render_partial( WPBR_PLUGIN_DIR . 'views/field/partials/label.php' );
}
$license_status = get_option( 'wpbr_license_status' );
?>

<div id="wpbr-field-control-wrap-<?php echo esc_attr( $this->field_id ); ?>" class="wpbr-field__control-wrap">

	<?php
	// Activation status messages.
	if ( isset( $_GET['sl_activation'] ) && ( isset( $_GET['license_message'] ) && ! empty( $_GET['license_message'] ) ) ) {

		$message = urldecode( $_GET['license_message'] );

		switch ( $_GET['sl_activation'] ) {

			case 'false':
				?>
				<div class="wpbr-callout wpbr-callout--error">
					<?php
					if ( 'failed' === $message ) {
						echo wp_kses_post( sprintf(
							/* translators: 1: customer account link, 2: closing anchor tag, 3: support link, 4: closing anchor tag */
							__( 'The license failed to deactivate. It is possible that this site is not currently associated with the license key. Please review your %1$slicense details%2$s and %3$scontact support%4$s for further assistance.', 'wp-business-reviews' ),
							'<a href="https://wpbusinessreviews.com/account/licenses/" target="_blank" rel="noopener noreferrer">',
							'</a>',
							'<a href="https://wpbusinessreviews.com/support/" target="_blank" rel="noopener noreferrer">',
							'</a>'
						) );
					} else {
						echo esc_html( $message );
					}
					?>
				</div>
				<?php
				break;

			case 'true':
				?>
				<div class="wpbr-callout wpbr-callout--success">
					<?php echo esc_html( $message ); ?>
				</div>
				<?php
				break;
		}
	}

	// Initial status messages.
	if ( ! empty( $license_status ) && 'valid' === $license_status ) : ?>
		<div class="wpbr-callout wpbr-callout--success">
			<strong><?php echo 'License Active'; ?></strong>
			- <?php echo wp_kses_post( __( 'You are receiving updates and support.', 'wp-business-reviews' ) ); ?>
		</div>
	<?php elseif ( empty( $license_status ) && ! isset( $_GET['sl_activation'] ) ) : ?>
		<div class="wpbr-callout wpbr-callout--error">
			<strong><?php echo 'License Inactive'; ?></strong>
			- <?php echo wp_kses_post( __( 'Activate your license to receive important updates and support.', 'wp-business-reviews' ) ); ?>
		</div>
	<?php endif; ?>

	<input
			id="wpbr-control-<?php echo esc_attr( $this->field_id ); ?>"
			class="wpbr-field__input js-wpbr-control"
			type="<?php echo empty( $this->value ) ? 'text' : 'password'; ?>"
		<?php echo ! empty( $this->value ) ? 'readonly' : ''; ?>
			name="<?php echo esc_attr( "{$this->prefix}[{$this->field_id}]" ); ?>"
			value="<?php echo esc_attr( $this->value ); ?>"
			placeholder="<?php echo esc_attr( $this->field_args['placeholder'] ); ?>"
			data-wpbr-control-id="<?php echo esc_attr( $this->field_id ); ?>"
	>

	<?php if ( ! empty( $this->field_args['description'] ) && 'valid' !== $license_status ) : ?>
		<?php $this->render_partial( WPBR_PLUGIN_DIR . 'views/field/partials/description.php' ); ?>
	<?php elseif ( 'valid' === $license_status ) : ?>
		<p class="wpbr-field__description">
			<?php
			echo wp_kses_post(
				sprintf(
					/* translators: 1: customer account link, 2: closing anchor tag */
					__( 'Thank you for activating your license key. This license can be found in your %1$sWP Business Reviews account%2$s, where you can also manage the active sites connected to your license.', 'wp-business-reviews' ),
					'<a href="https://wpbusinessreviews.com/account/licenses/" target="_blank" rel="noopener noreferrer">',
					'</a>'
				)
			);
			?>
		</p>
	<?php endif; ?>


<div id="wpbr-field-save_license" style="padding: 20px 0 40px;">
	<?php if ( $license_status !== false && 'valid' === $license_status ) : ?>
		<button type="submit" class="button-secondary button-small" name="edd_license_deactivate"
				value="deactivate_license"><?php _e( 'Deactivate License' ); ?></button>
	<?php else : ?>
	<input type="submit" name="save_advanced" id="save_advanced" class="button button-primary" value="Activate License">
	<?php endif; ?>
</div>

</div>
