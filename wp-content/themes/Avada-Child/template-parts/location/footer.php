<?php
if( ! function_exists('get_field') ) {
    return;
}

$aHours = get_field( 'ilp_location_hours_ap');
$footer_groups_businesses = (get_field( 'footer_groups_businesses' )) ? get_field( 'footer_groups_businesses' ) : '<p>Looking for a healthy and innovative way to give employees or event attendees the VIP wellness program?</p> 
<p>Our <strong>Mobile Services</strong> are a unique and exciting way to bring the benefits of IV therapy directly on-site to your business or corporate event.</p> 
<p>We accommodate small, medium, and large events. Contact us for details about group and event rates.</p>';
$coming_soon = primeiv_is_location_coming_soon();
$button_text = $coming_soon ? 'Get Notified When We Open' : 'Book Now';
$footer_get_directions = get_field( 'footer_get_directions' );

$footer_payment_methods = get_field('footer_payment_methods');
if( ! $footer_payment_methods ){
    $footer_payment_methods = get_field( 'global_footer_payment_methods', 'option' );
}


?>
<div class="before-footer">
	<div class="fusion-row">
		<div class="content-grid">
			<div class="col-1">
				<div class="col-title">Contact Us</div>

				<?php if ( get_field( 'ilp_address' ) ) : ?>
					<div class="location-info-address">
						<i class="location-info-address-icon"></i>
						<div><?php the_field( 'ilp_address' ); ?></div>
					</div>

                <?php if( $footer_get_directions ) {
                    ?>
                        <p class="directions-link-wrapper"><a target="_blank"  rel="noopener noreferrer" href="<?php the_field( 'footer_get_directions' ); ?>">Get Directions</a></p>
                        <?php
                    } ?>
				<?php endif; ?>

				<?php if ( get_field('ilp_phone_number') ) : ?>
					<div class="location-info-phone">
						<i class="location-info-phone-icon"></i>
						<a class="underline" href="tel:<?php the_field( 'ilp_phone_number' ); ?>"><?php the_field( 'ilp_phone_number' ); ?></a>
					</div>
				<?php endif; ?>

				<?php if ( get_field( 'ilp_email' ) ) : ?>
					<div class="location-info-email">
						<i class="location-info-email-icon"></i>
						<a href="mailto:<?php the_field( 'ilp_email' ); ?>"><?php the_field( 'ilp_email' ); ?></a>
					</div>
				<?php endif; ?>
				<div class="schedule">
                    <?php primeiv_footer_hours_table( $aHours )?>
				</div>
                <?php if( $footer_payment_methods ) {
                    ?>
                    <p><?php echo $footer_payment_methods; ?></p>
                    <?php
                } ?>
			</div>
			<div class="col-2">
				<?php if ( get_field( 'ilp_hide_footer_groups' ) != 'yes' ) { ?>
				<div class="col-title">For Groups & Businesses</div>
				<?php echo $footer_groups_businesses; ?>
				<?php } ?>
			</div>
			<div class="col-3">
				<a class="btn-orange" target="_blank" href="<?php the_field('ilpd_booking_url'); ?>"><?php echo $button_text; ?></a>
				<div class="google-reviews">
					<div class="prime-logo">
						<img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/IVSymbol-Logo.png" alt="Prime IV Hydration & Wellness Logo">
					</div>
					<div class="review-score">
						<div class="review-title">Prime IV Hydration & Wellness</div>
						<div class="review-content"><?php the_field('ilpd_google_rating_shortcode'); ?></div>
						<?php if ( !$coming_soon ): ?>
						<div class="review-logo">Powered by <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/Google.svg" alt="Google icon"></div>
						<?php endif; ?>
					</div>
				</div>
				<div class="follow-us">
					<div class="col-title social-title">Follow Us</div>
					<div class="footer-social-media">
							<a target="_blank" href="<?php the_field('ilpd_facebook_link'); ?>" class="social-media-btn fb"><img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/facebook.svg" alt="Facebook icon"></a>
							<a target="_blank" href="<?php the_field('ilpd_instagram_link'); ?>" class="social-media-btn inst"><img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/instagram.svg" alt="Instagram icon"></a>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<footer id="footer" class="fusion-footer-copyright-area location-footer">
	<div class="fusion-row">
		<div class="fusion-copyright-content">
			<div class="fusion-copyright-notice" style="padding-bottom: 0px;">

				<div class="tmm-copy-text">
					<div class="links-group flex">
						<div><sup>1</sup></div>
						<div class="footer-links">
							<a target="_blank" href="https://solaramentalhealth.com/can-drinking-enough-water-help-my-depression-and-anxiety/">https://solaramentalhealth.com/can-drinking-enough-water-help-my-depression-and-anxiety/</a><br>
							<a target="_blank" href="https://www.cambridge.org/core/journals/british-journal-of-nutrition/article/effects-of-hydration-status-on-cognitive-performance-and-mood/1210B6BE585E03C71A299C52B51B22F7">https://www.cambridge.org/core/journals/british-journal-of-nutrition/article/effects-of-hydration-status-on-cognitive-performance-and-mood/1210B6BE585E03C71A299C52B51B22F7</a>
						</div>
					</div>

					<div class="links-group flex">
						<div><sup>2</sup></div>
						<div class="footer-links">
							<a target="_blank" href="https://khealth.com/learn/hypertension/can-dehydration-cause-high-blood-pressure/#:~:text=Studies%20have%20shown%20that%20dehydration,pituitary%20gland%20to%20secrete%20vasopressin">https://khealth.com/learn/hypertension/can-dehydration-cause-high-blood-pressure/#:~:text=Studies%20have%20shown%20that%20dehydration,pituitary%20gland%20to%20secrete%20vasopressin</a>
						</div>
					</div>

					<div class="links-group flex">
						<div><sup>3</sup></div>
						<div class="footer-links">
							<a target="_blank" href="https://www.spineorthocenter.com/joint-pain-drink-more-water/#:~:text=Drinking%20water%20can%20stimulate%20our,circulation%20and%20the%20immune%20system">https://www.spineorthocenter.com/joint-pain-drink-more-water/#:~:text=Drinking%20water%20can%20stimulate%20our,circulation%20and%20the%20immune%20system</a>
						</div>
					</div>

					<div class="links-group flex">
						<div><sup>4</sup></div>
						<div class="footer-links">
							<a target="_blank" href="https://www.ncbi.nlm.nih.gov/pmc/articles/PMC6723555/">https://www.ncbi.nlm.nih.gov/pmc/articles/PMC6723555/</a>; <a target="_blank" href="https://www.hri.org.au/health/your-health/lifestyle/hydration-and-your-heart">https://www.hri.org.au/health/your-health/lifestyle/hydration-and-your-heart</a>
						</div>
					</div>

					<div class="links-group flex">
						<div><sup>5</sup></div>
						<div class="footer-links">
							<a target="_blank" href="https://bmjopen.bmj.com/content/6/5/e010708">https://bmjopen.bmj.com/content/6/5/e010708/</a><br><a target="_blank" href="https://www.kidney.org/content/6-tips-be-water-wise-healthy-kidneys">https://www.kidney.org/content/6-tips-be-water-wise-healthy-kidneys</a>
						</div>
					</div>

					<div class="links-group flex">
						<div><sup>6</sup></div>
						<div class="footer-links">
							<a target="_blank" href="https://pubmed.ncbi.nlm.nih.gov/17887814/">https://pubmed.ncbi.nlm.nih.gov/17887814/</a>; <a target="_blank" href="https://cathe.com/what-role-does-hydration-play-in-boosting-muscle-hypertrophy/">https://cathe.com/what-role-does-hydration-play-in-boosting-muscle-hypertrophy/</a>
						</div>
					</div>
				</div>

				<div class="terms">
					<p class="tmm-copy-text">The services provided have not been evaluated by the Food and Drug Administration. These products are not intended to diagnose, treat, cure or prevent any disease. All therapies are specific formulations prepared by Prime IV Hydration. The material on this website is provided for informational purposes only and is not medical advice. Any designations or references to therapies are for marketing purposes only. Always consult your physician before beginning any therapy program.</p>
					<p class="tmm-copy-text">Copyright <?php echo date('Y'); ?> | All Rights Reserved | <a href="<?php echo esc_url( home_url( '/privacy-policy/' ) ) ?>">Privacy Policy</a></p>
				</div>
			
			</div>
		</div> <!-- fusion-fusion-copyright-content -->
	</div> <!-- fusion-row -->
</footer>