<div
	id="wpbr-section-<?php echo esc_attr( $this->section_id ); ?>"
	class="wpbr-builder__section js-wpbr-section"
	data-wpbr-section-id="<?php echo esc_attr( $this->section_id ); ?>"
>
	<div class="wpbr-builder__section-header wpbr-builder__section-header--closed js-wpbr-section-header">
		<button class="wpbr-builder__section-toggle js-wpbr-section-toggle" aria-expanded="true">
			<span class="screen-reader-text">Toggle section: <?php esc_html_e( $this->section['name'] ); ?></span>
			<span class="dashicons dashicons-arrow-right js-wpbr-section-toggle-icon" aria-hidden="true"></span>
		</button>
		<h3 class="wpbr-builder__section-title">
			<i class="<?php echo esc_attr( $this->section['icon'] ); ?>"></i>
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
