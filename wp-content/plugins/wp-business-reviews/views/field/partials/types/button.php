<?php
$icon          = '';
$icon_class    = '';
$icon_position = 'before';

// Set icon if available.
if ( ! empty( $this->field_args['icon'] ) ) {
	$icon       = $this->field_args['icon'];
	$icon_class = 'fas wpbr-icon wpbr-fw wpbr-' . $icon;

	if ( ! empty( $this->field_args['icon_position'] ) ) {
		$icon_position = $this->field_args['icon_position'];
	}
}
?>

<button
	type="button"
	class="button js-wpbr-control"
	value="<?php echo esc_attr( $this->value ); ?>"
	data-wpbr-control-id="<?php echo esc_attr( $this->field_id ); ?>"
	>

	<?php
	if ( $icon && 'before' === $icon_position ) {
		echo '<i class="' . esc_attr( $icon_class ) . '"></i> ';
	}

	esc_html_e( $this->field_args['button_text'] );

	if ( $icon && 'after' === $icon_position ) {
		echo ' <i class="' . esc_attr( $icon_class ) . '"></i>';
	}
	?>
</button>
