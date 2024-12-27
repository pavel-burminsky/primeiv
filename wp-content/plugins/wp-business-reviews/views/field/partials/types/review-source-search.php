<?php
if ( ! empty( $this->field_args['name'] ) ) {
	$this->render_partial( WPBR_PLUGIN_DIR . 'views/field/partials/label.php' );
}

foreach ( $this->field_args['subfields'] as $subfield ) {
	$subfield->render();
}
?>

<?php if (
	! empty( $this->field_args['powered_by_image'] )
	&& ! empty( $this->field_args['powered_by_text'] )
): ?>
	<div class="wpbr-field__powered-by js-wpbr-powered-by">
		<img
			src="<?php echo esc_url( $this->field_args['powered_by_image'] ); ?>"
			alt="<?php echo esc_attr( $this->field_args['powered_by_text'] ); ?>"
			>
	</div>
<?php endif; ?>

<?php
	if ( ! empty( $this->field_args['description'] ) ) {
		$this->render_partial( WPBR_PLUGIN_DIR . 'views/field/partials/description.php' );
	}
?>
