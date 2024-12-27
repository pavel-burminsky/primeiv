<?php
$coming_soon = $args['is_coming_soon'] ?? false;
?>
<div class="sub-nav-wrapper">
<div class="sub-nav-inner">
	<div class="logo-wrapper">
		<a href="<?php echo get_home_url(); ?>">
			<img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/piv-white-logo.svg" alt="Prime IV Hydration & Wellness Logo">
		</a>
	</div>
	<nav class="sub-nav">
		<ul>
			<li><a href="#overview">Overview</a></li>
			<li><a href="#available-treatments">Treatments</a></li>
			<li><a href="#how-does-it-work">How It Works</a></li>
			<li><a href="#services-prices">Pricing</a></li>
			<li><a href="#faqs">FAQs</a></li>
		</ul>
	</nav>
	<?php if ( ! $coming_soon ) : ?>
	<a target="_blank" href="<?php the_field('ilpd_booking_url'); ?>" class="btn-orange">Book an Appointment</a>
	<?php else :  ?>
		<a class="btn-orange" href="<?php the_field('cs_get_notified_button_link'); ?>"><?php the_field('cs_get_notified_button_text'); ?></a>
	<?php endif; ?>
</div>
</div>