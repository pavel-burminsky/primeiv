<?php
namespace WP_Business_Reviews\Views\Admin;

use WP_Business_Reviews\Includes\Admin\Health_Check\Health_Check_Debug_Data;

defined( 'ABSPATH' ) || exit;

$info = $this->info;
?>

<div class="wpbr-system-info">
	<?php
	/** This action is documented in views/plugin-settings/main.php */
	do_action( 'wpbr_admin_notices' );
	?>
	<div class="wpbr-admin-notice notice notice-info">
		<p>
			<?php esc_html_e( 'The system information shown below can be copied and pasted into support requests.', 'wp-business-reviews' ); ?>
		</p>
		<button type="button" class="button button-primary" onclick="document.getElementById('system-information-copy-wrapper').style.display = 'block'; this.style.display = 'none';"><?php esc_html_e( 'Show System Info', 'wp-business-reviews' ); ?></button>
		<?php if ( 'en_US' !== get_locale() && version_compare( get_bloginfo( 'version' ), '4.7', '>=' ) ) : ?>
			<button type="button" class="button" onclick="document.getElementById('system-information-english-copy-wrapper').style.display = 'block'; this.style.display = 'none';"><?php esc_html_e( 'Show System Info in English', 'wp-business-reviews' ); ?></button>
		<?php endif; ?>

		<?php
		if ( 'en_US' !== get_locale() && version_compare( get_bloginfo( 'version' ), '4.7', '>=' ) ) :

			$english_info = Health_Check_Debug_Data::debug_data( 'en_US' );

			// Workaround for locales not being properly loaded back, see issue #30 on GitHub.
			if ( ! is_textdomain_loaded( 'wp-business-reviews' ) && _get_path_to_translation( 'wp-business-reviews' ) ) {
				load_textdomain( 'wp-business-reviews', _get_path_to_translation( 'wp-business-reviews' ) );
			}
			?>
			<div id="system-information-english-copy-wrapper" style="display: none;">
				<textarea id="system-information-english-copy-field" class="widefat" rows="10"><?php Health_Check_Debug_Data::textarea_format( $english_info ); ?></textarea>
				<button type="button" class="button button-primary js-wpbr-system-info-copy"><?php esc_html_e( 'Copy System Info', 'wp-business-reviews' ); ?></button>
			</div>

		<?php endif; ?>

		<div id="system-information-copy-wrapper" style="display: none;">
			<textarea id="system-information-copy-field" class="widefat" rows="10"><?php Health_Check_Debug_Data::textarea_format( $info ); ?></textarea>
			<button type="button" class="button button-primary js-wpbr-system-info-copy"><?php esc_html_e( 'Copy System Info', 'wp-business-reviews' ); ?></button>
		</div>
	</div>

	<h2 id="system-information-table-of-contents">
		<?php esc_html_e( 'Table Of Contents', 'wp-business-reviews' ); ?>
	</h2>
	<div>
		<?php
		$toc = array();

		foreach ( $info as $section => $details ) {
			if ( empty( $details['fields'] ) ) {
				continue;
			}

			$toc[] = sprintf(
				'<a href="#%s" class="js-wpbr-system-info-toc">%s</a>',
				esc_attr( $section ),
				esc_html( $details['label'] )
			);
		}

		echo implode( ' | ', $toc );
		?>
	</div>

	<?php
	foreach ( $info as $section => $details ) :
		if ( ! isset( $details['fields'] ) || empty( $details['fields'] ) ) {
			continue;
		}

		printf(
			'<h2 id="%s">%s%s</h2>',
			esc_attr( $section ),
			esc_html( $details['label'] ),
			( isset( $details['show_count'] ) && $details['show_count'] ? sprintf( ' (%d)', count( $details['fields'] ) ) : '' )
		);

		if ( isset( $details['description'] ) && ! empty( $details['description'] ) ) {
			printf(
				'<p>%s</p>',
				wp_kses( $details['description'], array(
					'a'      => array(
						'href' => true,
					),
					'strong' => true,
					'em'     => true,
				) )
			);
		}
		?>
		<table class="widefat striped wpbr-system-info__table">
			<tbody>
			<?php
			foreach ( $details['fields'] as $field ) {
				if ( is_array( $field['value'] ) ) {
					$values = '';
					foreach ( $field['value'] as $name => $value ) {
						$values .= sprintf(
							'<li>%s: %s</li>',
							esc_html( $name ),
							esc_html( $value )
						);
					}
				} else {
					$values = esc_html( $field['value'] );
				}

				printf(
					'<tr><td>%s</td><td>%s</td></tr>',
					esc_html( $field['label'] ),
					$values
				);
			}
			?>
			</tbody>
		</table>
		<span style="display: block; width: 100%; text-align: <?php echo ( is_rtl() ? 'left' : 'right' ); ?>">
			<a href="#system-information-table-of-contents" class="js-wpbr-system-info-toc"><?php esc_html_e( 'Return to table of contents', 'wp-business-reviews' ); ?></a>
		</span>
	<?php endforeach; ?>

</div>
