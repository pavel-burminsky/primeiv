<div
	id="wpbr-section-<?php echo esc_attr( $this->section_id ); ?>"
	class="wpbr-builder__section js-wpbr-section"
	data-wpbr-section-id="<?php echo esc_attr( $this->section_id ); ?>"
	data-wpbr-section-status="locked"
>
	<div class="wpbr-builder__section-header wpbr-builder__section-header--closed js-wpbr-section-header">
		<h3 class="wpbr-builder__section-title wpbr-tooltip wpbr-tooltip--left wpbr-tooltip--locked" aria-label="<?php echo esc_attr( 'Available after save.', 'wp-business-reviews' ); ?>">
			<i class="fas wpbr-icon wpbr-fw wpbr-lock"></i>
			<?php esc_html_e( $this->section['name'] ); ?>
		</h3>
	</div>
	<div class="wpbr-builder__section-body wpbr-u-hidden js-wpbr-section-body">
		<?php
		foreach ( $this->section['fields'] as $field_id => $field_args ) {
			// Render the field object that matches the field ID present in the config.
			$field_object = $this->field_repository->get( $field_id )->render();
		}
		?>
	</div>
</div>
