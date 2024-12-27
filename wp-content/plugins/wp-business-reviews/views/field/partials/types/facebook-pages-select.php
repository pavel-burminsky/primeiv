<?php
if ( ! empty( $this->field_args['name'] ) ) {
	$this->render_partial( WPBR_PLUGIN_DIR . 'views/field/partials/name.php' );
}
?>
<div id="wpbr-field-control-wrap-<?php echo esc_attr( $this->field_id ); ?>" class="wpbr-field__control-wrap">
	<?php if ( ! empty( $this->value ) ) : ?>
		<div class="wpbr-scrollable wpbr-scrollable--border">
			<ul class="wpbr-stacked-list wpbr-stacked-list--striped">
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
									<button
										type="button"
										class="wpbr-review-source__button button button-primary js-wpbr-fetch-control"
										data-wpbr-platform="facebook"
										data-wpbr-review-source-id="<?php echo esc_attr( $page_id ); ?>"
									>
										<i class="fas wpbr-icon wpbr-fw wpbr-cloud-download-alt"></i>
										<?php esc_html_e( 'Get Reviews', 'wp-business-reviews' ); ?>
									</button>
								</div>
							</div>
						</div>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	<?php else : ?>
	<div class="wpbr-callout wpbr-callout--error">
		<p>
			<?php
			printf(
				__( 'No Facebook page tokens were found. <a href="%s">Reconnecting to Facebook</a> should fix the issue.', 'wp-business-reviews' ),
				esc_url( admin_url( 'admin.php?page=wpbr-settings&wpbr_tab=platforms&wpbr_subtab=facebook' ) )
			);
			?>
		</p>
	</div>
	<?php endif; ?>
</div>
