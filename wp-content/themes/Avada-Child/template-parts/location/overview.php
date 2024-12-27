<?php

$coming_soon = $args['is_coming_soon'] ?? false;

$benefits_suptitle = get_field( 'ilpd_benefits_suptitle' );
$benefits_title    = get_field( 'ilpd_benefits_title' );
$benefits          = get_field( 'ilpd_benefits' );
$benefits_suptitle = $benefits_suptitle ? $benefits_suptitle : get_field( 'iv_locations_benefits_suptitle', 'option' );
$benefits_title    = $benefits_title ? $benefits_title : get_field( 'iv_locations_benefits_title', 'option' );
$benefits          = $benefits ? $benefits : get_field( 'iv_locations_benefits', 'option' );

$shortcode = get_post_meta( get_the_ID(), 'ilpd_google_rating_shortcode', true );
$place_id  = preg_match( '/place_id="([^"]+)"/', $shortcode, $matches ) ? $matches[1] : '';
$options_by_locationID = ( $place_id ) ? get_option( $place_id ) : array();

$google_rating_shortcode = get_field( 'ilpd_google_rating_shortcode' );


$sale_price = get_field( 'ilp_introductory_offer_section_sale_price' );
$text_after_price = get_field( 'ilp_introductory_offer_section_text_after_price' );

if( ! $text_after_price ) {
    $text_after_price = 'Intro Offer';
}

?>
<div class="fusion-row">

	<?php if ( ! $coming_soon ) : ?>
	<div id="overview" class="section-id location-page-first-top-section">
		<div class="left">
			<div>
				<h2>Prime IV Hydration & Wellness</h2>
				<div class="locations-tagline">Look, Feel, and Perform Better</div>
				<h1><?php the_field( 'ilpd_location_name' ); ?></h1>

				<div class="info-wrapper">
					<div class="location-general-info">
                        <?php primeiv_print_location_map(); ?>
						<div class="info-details">
							<?php 
                            primeiv_print_location_address();
                            primeiv_print_location_phone_link();
                            primeiv_print_location_email_link();
                            if ( get_field('ilp_mobileiv_show' ) == 'yes' ) : ?>
								<div class="location-mobile-therapy">
									<div class="location-mobile-therapy-icon">
										<img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/prime-iv-mobile-therapy.svg" alt="Prime IV Mobile Therapy">
									</div>
									<p>
										This location offers <strong>Mobile IV Therapy.</strong><br>
										Let us come to you!
									</p>
								</div>
							<?php endif; ?>
						
							<div class="location-book-appointment">
								<a class="btn-orange btn-outline" target="_blank" href="<?php the_field('ilpd_booking_url'); ?>">Book an Appointment</a>
							</div>

						</div>

					</div>

					<div class="google-rating">

						<?php if ( $google_rating_shortcode ): ?>
							<div class="patient-ratings">
								<h3>Google Customer Ratings</h3>
							</div>

							<?php echo $google_rating_shortcode; ?>
						<?php endif; ?>
						
						<?php if ( isset( $options_by_locationID['user_ratings_total'] ) ) : ?>
								<a target="_blank" href="https://search.google.com/local/reviews?placeid=<?php echo $place_id; ?>" class="reviews-text">Based on <?php echo $options_by_locationID['user_ratings_total']; ?> reviews</a>
						<?php endif; ?>
						
					</div>
				</div>

			</div>
		</div>
		<div class="right">
			<div class="initial-visit  <?php echo $sale_price ? 'with-sale' : ''; ?>">
                <span><?php echo the_field( 'ilp_introductory_offer_section_regular_price' ); ?></span> <?php if( get_field( 'ilp_hide_intro_offer' ) !== 'yes' ) { echo $text_after_price; } ?>
			</div>
			<?php
            $ilpd_form_code = get_field( 'ilpd_form_code' ); 
            if ( $ilpd_form_code ) : ?>
				<div class="form-iframe"><?php echo $ilpd_form_code; ?></div>
			<?php endif; ?>
			<div class="form-text">
				<p>Offer for first-time visits only.<br> 
				Existing customers: <a target="_blank" href="<?php the_field('ilpd_booking_url'); ?>">Book an Appointment</a>.</p>
			</div>
			<div class="form-text-bottom">
				<p>Your information will not be shared or sold. <br>Read our <a href="<?php echo esc_url( home_url( '/privacy-policy/' ) ) ?>">Privacy Policy</a>.</p>
			</div>
		</div>
		
	</div>
	<?php else : 
        ?>
		<?php get_template_part( 'template-parts/location/coming-soon-top', '', ['is_coming_soon' => $coming_soon] ); ?>
	<?php endif; ?>

	<div class="location-page-work-section">
		<div class="left">
			<p class="ilpd-benefits-suptitle"><?php echo $benefits_suptitle; ?></p>
			<h3><?php echo $benefits_title; ?></h3>
			<div class="items-wrap">
				<?php if ( $benefits ) : ?>
					<?php foreach ( $benefits as $benefit ) : ?>
						<div class="item">
							<div class="icon"><img src="<?php echo $benefit['icon']['url']; ?>" alt="<?php echo $benefit['icon']['alt']; ?>"></div>
							<div class="item-content">
								<div class="item-title"><span><?php echo $benefit['title']; ?></span></div>
								<div class="item-text"><p><?php echo $benefit['text']; ?></p></div>
							</div>
						</div>
					<?php endforeach; ?>
				<?php endif; ?>
			</div>
		</div>
		<div class="right">
			<div class="location-hours">
				<div class="title"><span class="title">Location hours</span></div>
				<div class="separator-wrap"><div class="separator"></div></div>
				<?php
				$wiHours  = get_field( 'ilp_location_hours_wi' );
				$aHours   = get_field( 'ilp_location_hours_ap');
				$wiToggle = get_field( 'ilp_location_hours_wi_toggle' ) == 'off' ? 'wi-off' : '';

				$weekdays       = ['mon','tue','wed','thu','fri','sat','sun'];
				$bottom_message = get_field( 'location_hours_bottom_message' );
				?>
				<table class="<?php echo $wiToggle; ?>">
					<tr class="main-titles">
						<td></td>
						<td class="wi">Walk-ins</td>
						<td>Appointments</td>
					</tr>
					
					<?php foreach ( $weekdays as $weekday ) : ?>
						<tr class="hours-row">
							<td class="day"><?php echo strtoupper($weekday) ?></td>
							<td class="hours wi"><?php echo isset( $wiHours['ilp_location_hours_wi_' . $weekday] ) ?  $wiHours['ilp_location_hours_wi_' . $weekday] : ''; ?></td>
							<td class="hours"><?php echo isset( $aHours['ilp_location_hours_ap_' . $weekday] ) ?  $aHours['ilp_location_hours_ap_' . $weekday] : ''; ?></td>
						</tr>
					<?php endforeach; ?>
				</table>
				<?php if ( '' !== trim( $bottom_message ) ) : ?>
					<p class="location-hours__bottom-message"><?php echo esc_html( $bottom_message ); ?></p>
				<?php endif; ?>
			</div>
		</div>
	</div>

</div>