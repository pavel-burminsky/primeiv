<?php
if ( ! empty( $this->field_args['name'] ) ) {
	$this->render_partial( WPBR_PLUGIN_DIR . 'views/field/partials/name.php' );
}
?>

<div id="wpbr-field-control-wrap-<?php echo esc_attr( $this->field_id ); ?>" class="wpbr-field__control-wrap">
	<fieldset class="wpbr-field__fieldset">
		<legend class="screen-reader-text"><?php echo esc_html( $this->field_args['name'] ); ?></legend>
		<ul class="wpbr-field__options">
			<?php
			$terms = get_terms( array(
				'taxonomy'   => 'wpbr_review_tag',
				'hide_empty' => false,
			) );
			?>
			<?php foreach ( $terms as $term ) : ?>
				<li class="wpbr-field__option">
					<input
						type="checkbox"
						id="wpbr-control-<?php echo esc_attr( $term->term_id ); ?>"
						class="wpbr-field__checkbox js-wpbr-tag-control"
						name="<?php echo esc_attr( "{$this->prefix}[{$this->field_id}][]" ); ?>"
						value="<?php echo esc_attr( $term->term_id ); ?>"
						data-wpbr-control-id="<?php echo esc_attr( $term->term_id ); ?>"
						<?php checked( $this->value && in_array( $term->term_id, $this->value ) ); ?>
						>
					<label for="wpbr-control-<?php echo esc_attr( $term->term_id ); ?>">
						<?php echo esc_html( $term->name . ' (' . $term->count . ')' ); ?>
					</label>
				</li>
			<?php endforeach; ?>
		</ul>
	</fieldset>

	<button
		type="button"
		class="button js-wpbr-fetch-control"
		style="margin-top: 20px"
		disabled
		>
		<i class="fas wpbr-icon wpbr-fw wpbr-sync-alt"></i> <?php echo esc_html__( 'Apply Changes', 'wp-business-reviews' ); ?>
	</button>

	<?php
	if ( ! empty( $this->field_args['description'] ) ) {
		$this->render_partial( WPBR_PLUGIN_DIR . 'views/field/partials/description.php' );
	}
	?>
</div>
