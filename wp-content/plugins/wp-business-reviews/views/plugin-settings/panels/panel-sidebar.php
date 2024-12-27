<div class="wpbr-panel__sidebar">
	<ul class="wpbr-subtabs wpbr-subtabs--border-right js-wpbr-subtabs">
		<?php foreach ( $this->sections as $section_id => $section ) : ?>
			<?php
			$status_modifier = '';

			// Skip platform panel if not active.
			if (
				'platforms' === $this->tab_id
				&& 'platforms' !== $section_id
				&& ! in_array( $section_id, array_keys( $this->active_platforms ) ) ) {
				continue;
			}

			// Set status icon based on connection status.
			if ( 'status' === $section['icon'] ) {
				$is_connected    = in_array( $section_id, array_keys( $this->connected_platforms ) );
				$status_modifier = $is_connected ? ' is-success'  : ' is-error';
				$section['icon'] = $is_connected ? 'check-circle': 'exclamation-circle';
			}
			?>
			<li class="wpbr-subtabs__item">
				<a
					id="wpbr-subtab-<?php echo esc_attr( $section_id ); ?>"
					class="wpbr-subtabs__link js-wpbr-subtab<?php echo esc_attr( $status_modifier ); ?>"
					href="#wpbr-section-<?php echo esc_attr( $section_id ); ?>"
					data-wpbr-subtab-id="<?php echo esc_attr( $section_id ); ?>"
					>
					<i class="fas wpbr-icon wpbr-fw wpbr-<?php echo esc_attr( $section['icon'] ); ?>"></i>
					<?php echo esc_html( $section['name'] ); ?>
				</a>
			</li>
		<?php endforeach; ?>
	</ul>
</div>
