<?php
$coming_soon = $args['is_coming_soon'] ?? false;
?>

<div id="overview" class="comingsoon section-id location-page-first-top-section">
	<div class="left">
		<div>
			<?php if ($coming_soon) { 
                $opening_year = primeiv_get_location_opening_date(true)
                ?>
			<div class="location-coming-soon">
				<div></div>
                <?php if( $opening_year ) { ?>
                    <span><?php echo esc_html($opening_year); ?></span>
                    <?php
                }?>
			</div>
			<?php } ?>
			<h2>Prime IV Hydration & Wellness</h2>
			<div class="locations-tagline">Look, Feel, and Perform Better</div>
			<h1><?php the_field('ilpd_location_name'); ?></h1>
			<div class="info">
				<div class="location-general-info">
					<?php if ( get_field('ilp_address') ) { ?>
						<div class="location-info-address">
							<i class="location-info-address-icon"></i>
							<p><?php the_field('ilp_address'); ?></p>
						</div>
					<?php } ?>
					<?php if ( get_field('ilp_phone_number') ) { ?>
						<div class="location-info-phone">
							<i class="location-info-phone-icon"></i>
							<span><?php the_field('ilp_phone_number'); ?></span>
						</div>
					<?php } ?>
					<?php if ( get_field('ilp_email') ) { ?>
						<div class="location-info-email">
							<i class="location-info-email-icon"></i>
							<a href="mailto:<?php the_field('ilp_email'); ?>"><?php the_field('ilp_email'); ?></a>
						</div>
					<?php } ?>
				</div>
				<div class="google-rating">
					<?php the_field('ilpd_google_rating_shortcode'); ?>				
				</div>
			</div>
			<div class="buttons">
				<?php if ($coming_soon) { ?>
					<a class="btn-orange" href="<?php the_field('cs_get_notified_button_link'); ?>"><?php the_field('cs_get_notified_button_text'); ?></a>
				<?php } else { ?>
					<a class="btn-orange" target="_blank" href="<?php the_field('ilpd_booking_url'); ?>">Book an Appointment</a>
				<?php } ?>
			</div>
			<?php if(get_field('ilp_awards')): ?>
			<div class="ilp-awards">
				<?php foreach(get_field('ilp_awards') as $award){ ?>
				<div>
					<img src="<?php echo $award['ilp_award_badge']['url']; ?>" alt="<?php echo $award['ilp_award_badge']['alt']; ?>">
				</div>
				<?php } ?>
			</div>
			<?php endif; ?>
		</div>
	</div>
	<div class="right">
		<div class="location-map"><?php the_field('ilpd_map_code'); ?></div>
	</div>
</div>