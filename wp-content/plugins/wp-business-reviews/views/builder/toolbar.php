<div class="wpbr-toolbar">
	<div class="wpbr-toolbar__title">
		<div class="wpbr-editable-title">
			<h1 id="wpbr-title" class="wpbr-editable-title__text">
				<?php echo esc_html( $this->title ? $this->title : $this->title_default ); ?>
			</h1>
			<input
				id="wpbr-control-title"
				class="wpbr-editable-title__control wpbr-u-hidden"
				type="text"
				name="wpbr_collection[title]"
				value="<?php echo esc_attr( $this->title ? $this->title : $this->title_default ); ?>"
				placeholder="<?php echo esc_attr( $this->title_placeholder ); ?>"
				maxlength="255"
				data-wpbr-default="<?php echo esc_attr( $this->title_default ); ?>"
				>
			<button type="button" id="wpbr-control-title-toggle" class="button wpbr-icon-button wpbr-editable-title__toggle wpbr-tooltip wpbr-tooltip--top" aria-label="<?php echo esc_attr( 'Edit Title', 'wp-business-reviews' ); ?>">
				<i class="fas wpbr-icon wpbr-fw wpbr-pencil-alt" data-fa-transform="grow-2"></i>
			</button>
		</div>
	</div>
	<div class="wpbr-toolbar__controls">
		<ul class="wpbr-inline-list">
			<?php if ( $this->trash_link ) : ?>
				<li class="wpbr-inline-list__item wpbr-inline-list__item--divider">
					<a class="wpbr-toolbar__delete" href="<?php echo esc_url( $this->trash_link ); ?>">
						<?php echo esc_html( __( 'Move to Trash', 'wp-business-reviews' ) ); ?>
					</a>
				</li>
			<?php endif; ?>
			<?php if ( $this->shortcode ) : ?>
				<li class="wpbr-inline-list__item wpbr-inline-list__item--divider">
					<?php
					printf(
						'<button type="button" class="%1$s" aria-label="%2$s" data-wpbr-shortcode="%3$s"><i class="%4$s"></i> %5$s</button>',
						'button wpbr-tooltip wpbr-tooltip--top js-wpbr-shortcode-button',
						esc_attr( $this->shortcode ),
						esc_attr( $this->shortcode ),
						'fas wpbr-icon wpbr-fw wpbr-copy',
						esc_html__( 'Copy Shortcode', 'wp-business-reviews' )
					);
					?>
				</li>
			<?php endif; ?>
			<li class="wpbr-inline-list__item"><button type="submit" id="wpbr-control-save" class="button button-primary"><?php esc_html_e( 'Save', 'wp-business-reviews' ); ?></button></li>
			<li class="wpbr-inline-list__item">
				<button type="button" id="wpbr-control-inspector" class="button wpbr-icon-button wpbr-tooltip wpbr-tooltip--top" aria-label="<?php echo esc_attr( 'Settings', 'wp-business-reviews' ); ?>">
					<i class="fas wpbr-icon wpbr-fw wpbr-cog" data-fa-transform="grow-2"></i>
				</button>
			</li>
		</ul>
	</div>
</div>
