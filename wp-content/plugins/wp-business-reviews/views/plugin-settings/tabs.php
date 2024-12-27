<div class="wpbr-settings__tabs">
	<nav>
		<ul class="wpbr-tabs js-wpbr-tabs">
			<?php foreach ( $this->config as $tab_id => $tab ) : ?>
				<li class="wpbr-tabs__item">
					<a
						id="wpbr-tab-<?php echo esc_attr( $tab_id ); ?>"
						class="wpbr-tabs__link js-wpbr-tab"
						href="#wpbr-panel-<?php echo esc_attr( $tab_id ); ?>"
						data-wpbr-tab-id="<?php echo esc_attr( $tab_id ); ?>"
						>
						<?php echo esc_html( $tab['name'] ); ?>
					</a>
				</li>
			<?php endforeach; ?>
			<li class="wpbr-tabs__item">
				<a
					id="wpbr-tab-help"
					class="wpbr-tabs__link js-wpbr-tab"
					href="#wpbr-panel-help"
					data-wpbr-tab-id="help"
					>
					<?php echo esc_html__( 'Help', 'wp-business-reviews' ); ?>
				</a>
			</li>
		</ul>
	</nav>
</div>
