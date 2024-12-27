<?php
$heading     = __( 'WP Business Reviews Help', 'wp-business-reviews' );
$description = sprintf(
	/* translators: link to documentation */
	__( 'These introductory videos will help you get up and running in minutes. To learn more, visit the %1$splugin documentation%2$s.', 'wp-business-reviews' ),
	'<a href="https://wpbusinessreviews.com/documentation/" target="_blank" rel="noopener noreferrer">',
	'</a>'
);
$videos      = array(
	'video-overview' => array(
		'title' => __( 'Plugin Overview', 'wp-business-reviews' ),
		'id'    => '279699083',
	),
	'video-google-places' => array(
		'title' => __( 'Connecting to Google', 'wp-business-reviews' ),
		'id'    => '279553827',
	),
	'video-facebook' => array(
		'title' => __( 'Connecting to Facebook', 'wp-business-reviews' ),
		'id'    => '336219230',
	),
	'video-yelp' => array(
		'title' => __( 'Connecting to Yelp', 'wp-business-reviews' ),
		'id'    => '279551721',
	),
	'video-zomato' => array(
		'title' => __( 'Connecting to Zomato', 'wp-business-reviews' ),
		'id'    => '336145814',
	),
);
?>

<div id="wpbr-viewer" class="wpbr-viewer wpbr-card">
	<div class="wpbr-viewer__main">
		<div id="wpbr-player" class="wpbr-viewer__player"></div>
	</div>
	<div class="wpbr-viewer__sidebar">
		<div class="wpbr-viewer__description">
			<div class="wpbr-admin-header">
				<h2 class="wpbr-admin-header__heading"><?php echo esc_html( $heading ); ?></h2>
				<p class="wpbr-admin-header__subheading">
					<?php echo wp_kses_post( $description ); ?>
				</p>
			</div>
		</div>
		<div class="wpbr-viewer__nav">
			<ul class="wpbr-subtabs">
				<?php foreach ( $videos as $video_id => $video_atts ) : ?>
					<li class="wpbr-subtabs__item">
						<a
							id="wpbr-subtab-<?php echo esc_attr( $video_id ); ?>"
							class="wpbr-subtabs__link js-wpbr-subtab js-wpbr-video-subtab"
							href="<?php echo esc_url( $video_atts['id'] ); ?>"
							data-wpbr-subtab-id="<?php echo esc_attr( $video_id ); ?>"
							data-wpbr-video-id="<?php echo esc_attr( $video_atts['id'] ); ?>"
							>
							<i class="fas wpbr-icon wpbr-fw wpbr-play-circle"></i>
							<?php echo esc_html( $video_atts['title'] ); ?>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	</div>
</div>
