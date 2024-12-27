<?php
$status_slug  = isset( $this->value['status'] ) ? $this->value['status'] : '';
$status_class = 'wpbr-platform-status wpbr-platform-status--error';
$description  = $this->field_args['description'];

if ( ! empty( $this->field_args['name'] ) ) {
	$this->render_partial( WPBR_PLUGIN_DIR . 'views/field/partials/name.php' );
}

// Set the platform status and description text based on saved status.
switch( $status_slug ) {
	case 'connected' :
		$status             = __( 'Connected', 'wp-business-reviews' );
		$status_class       = 'wpbr-platform-status wpbr-platform-status--success';
		$builder_url        = admin_url( 'admin.php?page=wpbr-builder' );
		$new_collection_url = add_query_arg( 'wpbr_platform', $this->field_args['platform'], $builder_url );
		$description        = sprintf(
			__( 'Now that you\'re connected, %sadd a collection%s!', 'wp-business-reviews' ),
			'<a href="' . esc_url( $new_collection_url )  . '">',
			'</a>'
		);
		break;

	case 'google_places_over_query_limit' :
		$status          = __( 'Billing Not Enabled', 'wp-business-reviews' );
		$billing_doc_url = 'https://wpbusinessreviews.com/documentation/platforms/google/#billing-not-enabled';
		$description     = sprintf(
			__( 'A valid API key was entered, but billing is not enabled. As of July 16th, 2018, Google requires users to enable billing before accessing the Google Places API. %sLearn how to enable billing%s.', 'wp-business-reviews' ),
			'<a href="' . esc_url( $billing_doc_url )  . '" target="_blank" rel="noopener noreferrer">',
			'</a>'
		);
		break;

	case 'google_places_restricted' :
		$status           = __( 'Restricted Key Detected', 'wp-business-reviews' );
		$restrictions_doc = 'https://wpbusinessreviews.com/documentation/platforms/google/#restricted-key-detected';
		$description      = sprintf(
			__( 'A valid API key was entered, but it may have HTTP referrer restrictions that cannot be used with the Google Places API. Remove restrictions on the saved key or create a new key without restrictions. %sLearn how to modify restrictions%s.', 'wp-business-reviews' ),
			'<a href="' . esc_url( $restrictions_doc )  . '" target="_blank" rel="noopener noreferrer">',
			'</a>'
		);
		break;

	case 'google_places_needs_attention' :
		$status      = __( 'Needs Attention', 'wp-business-reviews' );
		$description = __( 'One or more Google collections failed to refresh. Confirm that the Google Places API key is valid, has billing enabled, and is not restricting access by HTTP Referrer. New collections and automatic refreshing may not be possible until the platform is reconnected.', 'wp-business-reviews' );
		break;

	case 'facebook_needs_attention' :
		$status      = __( 'Needs Attention', 'wp-business-reviews' );
		$description = __( 'One or more Facebook collections failed to refresh. Reconnect to Facebook and confirm that the "Manage Pages" permission has been granted for all Facebook Pages that have a collection on this site. New Facebook collections and automatic refreshing may not be possible until the platform is reconnected.', 'wp-business-reviews' );
		break;

	case 'yelp_needs_attention' :
		$status      = __( 'Needs Attention', 'wp-business-reviews' );
		$description = __( 'One or more Yelp collections failed to refresh. Confirm that the Yelp API key is valid. New Yelp collections and automatic refreshing may not be possible until the platform is reconnected.', 'wp-business-reviews' );
		break;

	case 'yp_needs_attention' :
		$status      = __( 'Needs Attention', 'wp-business-reviews' );
		$description = __( 'One or more YP collections failed to refresh. Confirm that the YP API key is valid. New YP collections and automatic refreshing may not be possible until the platform is reconnected.', 'wp-business-reviews' );
		break;

	case 'zomato_needs_attention' :
		$status      = __( 'Needs Attention', 'wp-business-reviews' );
		$description = __( 'One or more Zomato collections failed to refresh. Confirm that the Zomato API key is valid. New Zomato collections and automatic refreshing may not be possible until the platform is reconnected.', 'wp-business-reviews' );
		break;

	default:
		$status = __( 'Disconnected', 'wp-business-reviews' );
}
?>

<div id="wpbr-field-control-wrap-<?php echo esc_attr( $this->field_id ); ?>" class="wpbr-field__control-wrap">
	<div class="wpbr-field__flex">
		<strong class="<?php echo esc_attr( $status_class ); ?>">
			<?php echo esc_html( $status ); ?>
		</strong>

		<?php if ( isset( $this->value['last_checked'] ) ) : ?>
			<?php
			$now          = time();
			$last_checked = $this->value['last_checked'];
			$difference   = human_time_diff( $last_checked, $now );
			$tooltip      = sprintf( __( 'Last verified %s ago.', 'wp-business-reviews' ), $difference );
			?>
			<span
				id="wpbr-tooltip-<?php echo esc_attr( $this->field_id ); ?>"
				class="wpbr-tooltip wpbr-tooltip--medium wpbr-tooltip--top"
				aria-label="<?php echo esc_attr( $tooltip ); ?>"
				>
				<i class="fas wpbr-icon wpbr-fw wpbr-question-circle"></i>
			</span>
		<?php endif; ?>
	</div>

	<?php if ( ! empty( $description ) ) : ?>
		<p id="wpbr-field-description-<?php echo esc_attr( $this->field_id ); ?>" class="wpbr-field__description"><?php echo wp_kses_post( $description ); ?></p>
	<?php endif; ?>
</div>
