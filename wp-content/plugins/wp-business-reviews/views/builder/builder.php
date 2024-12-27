<div id="wpbr-builder" class="wpbr-builder js-wpbr-builder">
	<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php?action=wpbr_collection_save' ) ); ?>">
		<?php $this->render_partial( WPBR_PLUGIN_DIR . "views/builder/toolbar.php" ); ?>
		<div id="wpbr-builder-workspace" class="wpbr-builder__workspace">
			<?php
			$this->inspector->render();
			$this->preview->render();
			?>
		</div>
	</form>
</div>
