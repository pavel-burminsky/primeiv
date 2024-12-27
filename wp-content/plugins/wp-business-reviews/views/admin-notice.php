<div class="<?php echo esc_attr( $this->class_name ); ?>" data-wpbr-admin-notice="<?php echo esc_attr( $this->id ); ?>">
	<p><?php echo wp_kses_post( $this->message ); ?></p>
	<?php if ( ! empty( $this->cta ) ): ?>
		<a class="button button-primary" href="<?php echo esc_url( $this->cta['url'] ); ?>">
			<?php echo esc_html( $this->cta['text'] ); ?>
		</a>
	<?php endif; ?>
</div>
