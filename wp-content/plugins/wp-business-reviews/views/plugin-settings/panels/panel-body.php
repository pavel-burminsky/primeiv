<div class="wpbr-panel__body">
	<?php foreach ( $this->sections as $section_id => $section ) : ?>
		<?php
		// Skip platform panel if not active.
		if (
			'platforms' === $this->tab_id
			&& 'platforms' !== $section_id
			&& ! in_array( $section_id, array_keys( $this->active_platforms ) ) ) {
			continue;
		}
		?>
		<div id="wpbr-section-<?php echo esc_attr( $section_id ); ?>" class="wpbr-panel__section js-wpbr-section" data-subtab-id="<?php echo esc_attr( $section_id ); ?>">
			<div class="wpbr-admin-header">
				<h2 class="wpbr-admin-header__heading"><?php echo esc_html( $section['heading'] ); ?></h2>

				<?php if ( ! empty( $section['description'] ) ) : ?>
					<p class="wpbr-admin-header__subheading">
						<i class="fas wpbr-icon wpbr-fw wpbr-play-circle"></i>
						<?php echo wp_kses_post( $section['description'] ); ?>
					</p>
				<?php endif;?>


			</div>

			<?php if ( ! empty( $section['fields'] ) ) : ?>
				<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php?action=wpbr_settings_save' ) ); ?>">
					<input type="hidden" name="wpbr_tab" value="<?php echo esc_attr( $this->tab_id ); ?>">
					<input type="hidden" name="wpbr_subtab" value="<?php echo esc_attr( $section_id ); ?>">
					<?php
					wp_nonce_field(
						'wpbr_option_save',
						'wpbr_option_nonce'
					);
					foreach ( $section['fields'] as $field_id => $field_args ) {
						if ( $this->field_repository->has( $field_id ) ) {
							$field_object = $this->field_repository->get( $field_id );
							$field_object->render();
						}
					}
					?>
				</form>
			<?php endif; ?>
		</div>
	<?php endforeach; ?>
</div>
