<div class="wpbr-builder__preview">
	<?php
	/** This action is documented in views/plugin-settings/main.php */
	do_action( 'wpbr_admin_notices' );
	?>
	<?php if ( ! empty( $this->collection ) ) : ?>
		<?php $this->collection->render(); ?>
	<?php else : ?>
		<div class="wpbr-wrap">
			<div class="wpbr-collection-wrap js-wpbr-collection-wrap">
			</div>
		</div>
	<?php endif; ?>
</div>
