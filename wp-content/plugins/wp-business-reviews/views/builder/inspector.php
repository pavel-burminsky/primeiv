<?php
$platform = str_replace( '_', '-', $this->platform );
$post_id  = $this->post_id;
$class    = 'wpbr-builder__inspector wpbr-builder__inspector--' . $platform;
?>

<div id="wpbr-builder-inspector" class="<?php echo esc_attr( $class ); ?>">
	<?php
	wp_nonce_field( 'wpbr_collection_save', 'wpbr_collection_nonce', false );
	wp_nonce_field( 'wpbr_review_source_save', 'wpbr_review_source_nonce', false );
	wp_nonce_field( 'wpbr_review_save', 'wpbr_review_nonce', false );
	wp_referer_field();
	?>
	<input id="wpbr-control-action" type="<?php echo WP_DEBUG ? 'text' : 'hidden'; ?>" name="action" value="wpbr_collection_save">
	<input id="wpbr-control-review-source" type="<?php echo WP_DEBUG ? 'text' : 'hidden'; ?>" name="wpbr_review_source">
	<input id="wpbr-control-review" type="<?php echo WP_DEBUG ? 'text' : 'hidden'; ?>" name="wpbr_review">
	<input
		id="wpbr-control-review-source-id"
		type="hidden"
		name="wpbr_review_source_id"
		value="<?php echo esc_attr( $this->review_source_id ); ?>"
		>
	<input
		id="wpbr-control-post-id"
		type="<?php echo WP_DEBUG ? 'text' : 'hidden'; ?>"
		name="wpbr_collection[post_id]"
		value="<?php echo esc_attr( $this->post_id ); ?>"
		>
	<input
		id="wpbr-control-platform"
		type="<?php echo WP_DEBUG ? 'text' : 'hidden'; ?>"
		name="wpbr_collection[platform]"
		value="<?php echo esc_attr( $this->platform ); ?>"
		>
	<?php
	foreach ( $this->config as $section_id => $section ) {
		$view   = 'section';
		$status = isset( $section['status'] ) ? $section['status']  : '';

		if (
			! $post_id
			&& 'review-tag' !== $platform
			&& 'locked' === $status
		) {
			// This is an unsaved API-based collection, so lock the section.
			$view = 'section-locked';
		}

		$this->render_partial(
			WPBR_PLUGIN_DIR . "views/section/{$view}.php",
			array(
				'section_id'       => $section_id,
				'section'          => $section,
				'field_repository' => $this->field_repository,
			)
		);
	}
	?>
</div>
