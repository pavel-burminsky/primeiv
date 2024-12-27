<div class="wpbr-settings__panels">
	<?php
	/**
	 * Triggers action to render plugin-specific admin notices.
	 *
	 * @since 0.1.0
	 */
	do_action( 'wpbr_admin_notices' );
	?>

	<?php foreach ( $this->config as $tab_id => $tab ) : ?>
		<div id="wpbr-panel-<?php echo esc_attr( $tab_id ); ?>" class="wpbr-panel js-wpbr-panel" data-wpbr-tab-id="<?php echo esc_attr( $tab_id ); ?>">
			<?php
			// Render the panel navigation if more than one section exists.
			if ( ! empty( $tab['sections'] ) && count( $tab['sections'] ) > 1 ) {
				$this->render_partial(
					WPBR_PLUGIN_DIR . 'views/plugin-settings/panels/panel-sidebar.php',
					array(
						'tab_id'              => $tab_id,
						'sections'            => $tab['sections'],
						'active_platforms'    => $this->active_platforms,
						'connected_platforms' => $this->connected_platforms,
					)
				);
			}

			$this->render_partial(
				WPBR_PLUGIN_DIR . 'views/plugin-settings/panels/panel-body.php',
				array(
					'tab_id'           => $tab_id,
					'sections'         => $tab['sections'],
					'field_repository' => $this->field_repository,
					'active_platforms' => $this->active_platforms,
				)
			);
			?>
		</div>
	<?php endforeach; ?>

	<div id="wpbr-panel-help" class="wpbr-panel wpbr-panel--seamless js-wpbr-panel" data-wpbr-tab-id="help">
		<?php $this->render_partial( WPBR_PLUGIN_DIR . 'views/plugin-settings/help-dashboard.php' ); ?>
	</div>
</div>
