<?php

$coming_soon = $args['is_coming_soon'] ?? false;

$how_title = get_field( 'ilpd_how_does_it_work_title' );
$how       = get_field( 'ilpd_how_does_it_work' );
$how       = $how ? $how : get_field( 'iv_locations_how_does_it_work', 'option' );
$how_title = $how_title ? $how_title : get_field( 'iv_locations_how_does_it_work_title', 'option' );

$vitamins_title = get_field('ilpd_why_not_vitamins_title');
$percentage_title = get_field('ilpd_absorption_percentage_title');
$percentage_text = get_field('ilpd_absorption_percentage_text');
$percentage_iv = get_field('ilpd_absorption_percentage_iv');
$percentage_oral = get_field('ilpd_absorption_percentage_oral');
$time_title = get_field('ilpd_absorption_time_title');
$time_text = get_field('ilpd_absorption_time_text');
$time_iv = get_field('ilpd_absorption_time_iv');
$time_oral = get_field('ilpd_absorption_time_oral');
$time_iv_caption = get_field('ilpd_absorption_time_iv_caption');
$time_oral_caption = get_field('ilpd_absorption_time_oral_caption');
$vitamins_title = $vitamins_title ? $vitamins_title : get_field('iv_locations_why_not_vitamins_title', 'option');
$percentage_title = $percentage_title ? $percentage_title : get_field('iv_locations_absorption_percentage_title', 'option');
$percentage_text = $percentage_text ? $percentage_text : get_field('iv_locations_absorption_percentage_text', 'option');
$percentage_iv = $percentage_iv ? $percentage_iv : get_field('iv_locations_absorption_percentage_iv', 'option');
$percentage_oral = $percentage_oral ? $percentage_oral : get_field('iv_locations_absorption_percentage_oral', 'option');
$time_title = $time_title ? $time_title : get_field('iv_locations_absorption_time_title', 'option');
$time_text = $time_text ? $time_text : get_field('iv_locations_absorption_time_text', 'option');
$time_iv = $time_iv ? $time_iv : get_field('iv_locations_absorption_time_iv', 'option');
$time_iv_caption = $time_iv_caption ? $time_iv_caption : get_field('iv_locations_absorption_time_iv_caption', 'option');
$time_oral = $time_oral ? $time_oral : get_field('iv_locations_absorption_time_oral', 'option');
$time_oral_caption = $time_oral_caption ? $time_oral_caption : get_field('iv_locations_absorption_time_oral_caption', 'option');

$showTherapy1   = get_field( 'ilp_its_show' ); 
$showTherapy5   = get_field( 'ilp_nadit_show' );
$showTherapy3   = get_field( 'ilp_cts_show' );
$showTherapy6   = get_field( 'ilp_pts_show' );
$showTherapy4   = get_field( 'ilp_tbs_show' );
$showTherapy2   = get_field( 'ilp_injts_show' );
$mobile_iv_show = get_field( 'ilp_mobileiv_show' );
$show_gift_certificate = get_field( 'gift_certificate_show' ) != 'off' && !$coming_soon ? true : false;

$availabe_treatments = ( get_field( 'ilp_treatments' ) ) ? get_field( 'ilp_treatments' ) : primeiv_get_default_available_treatments( 'global_iv_treatments' );
$boost_treatments    = ( get_field( 'ilp_boost_treatments' ) ) ? get_field( 'ilp_boost_treatments' ) : primeiv_get_default_available_treatments( 'global_iv_treatment_boosts' );
$other_treatments    = ( get_field( 'ilp_other_treatments' ) ) ? get_field( 'ilp_other_treatments' ) : primeiv_get_default_available_treatments( 'global_other_treatments' );

$memberships_section_features = get_field( 'ilpd_memberships_section_features' );

$show_packages = ( ! get_field( 'ilpd_show_packages_section' ) ) ? 'yes' : get_field( 'ilpd_show_packages_section' );

$packages = primeiv_get_packages();

$video_testimonials = get_field( 'global_video_testimonials', 'option' );
$global_faqs        = get_field( 'global_faqs', 'option' );

$global_intravenous = get_field( 'global_it_price_tooltips', 'option' );
$global_injection   = get_field( 'global_ij_price_tooltips', 'option' );
$global_infusion    = get_field( 'global_ni_price_tooltips', 'option' );
$global_peptides    = get_field( 'global_pep_price_tooltips', 'option' );
$global_cryotherapy = get_field( 'global_cr_price_tooltips', 'option' );
$global_supplement  = get_field( 'global_pbs_price_tooltips', 'option' );

$sale_price = get_field( 'ilp_introductory_offer_section_sale_price' );
?>

<?php if ( $sale_price ) : ?>
    <style>
    .page-template-location-page .location-page-introductory-section .wrap .subtitle span:before,
    .page-template-location-page .initial-visit span:before {
        content: '<?php echo $sale_price; ?>';
    }
    </style>
<?php endif; ?>

<div class="location-page-full-width-wrapper flex">
	<div id="location-sidebar-wrapper" class="location-sidebar-wrapper">
		<aside class="location-sidebar">
			<nav class="sidebar-menu">
				<ul>
					<li class="available-treatments"><a class="" href="#available-treatments">Available Treatments</a></li>
					<li class="why-use-iv-therapy"><a href="#why-use-iv-therapy">Why Use IV Therapy</a></li>
					<li class="how-does-it-work"><a href="#how-does-it-work">How Does It Work?</a></li>
					<li class="services-prices">
						<a href="#services-prices">Services & Prices</a>
						<ul class="sidebar-sub-menu">
							<li class="nmemberships"><a href="#memberships">Memberships</a></li>
							<li class="ntreatments-prices"><a href="#treatments-prices">Treatments Prices</a></li>
							<?php if ( $show_packages == 'yes' ) : ?>
							<li class="npackages"><a href="#packages">Packages</a></li>
							<?php endif; ?>
						</ul>
					</li>
					<li class="testimonials"><a href="#testimonials">Testimonials</a></li>
					<li class="faqs"><a href="#faqs">FAQs</a></li>
				</ul>
				<div class="book-appointment">
					<?php if ( ! $coming_soon ) : ?>
					<a class="btn-orange" target="_blank" href="<?php the_field('ilpd_booking_url'); ?>">Book an Appointment</a>
					<?php else : ?>
						<a class="btn-orange btn-comingsoon" href="<?php the_field('cs_get_notified_button_link'); ?>"><?php the_field('cs_get_notified_button_text'); ?></a>
					<?php endif; ?>
				</div>
                <div class="location-sidebar-contact">
                <?php
                    primeiv_print_location_map(true);
                    primeiv_print_location_address(true);
                    primeiv_print_location_phone_link(true);
                    primeiv_print_location_email_link(true, true);
                    ?>
                </div>
            </nav>
		</aside>
	</div>
	<div class="location-content">

		<div class="mobile-collapse" data-section="available-treatments-wrap">
			<div class="collapse-wrapper">
				<div class="mobile-collapse-title">
					Available Treatments
				</div>
				<div class="mobile-collapse-icon">
				<i class="fas fa-chevron-down"></i>
				</div>
			</div>
		</div>
		<div id="available-treatments-wrap" class="mobile-collapse-content">
			<div id="available-treatments" class="section-id location-section location-treatments dots-bg">
				<div class="semi-wrap">
					<div class="treatment-header flex items-center">
						<div class="col">
							<h3 class="title-medium">Available Treatments</h3>
							<div class="hsa-wrapper mobile">
								<img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/HSA&FSA.png" alt="Pay for your treatments with pre-tax dollars">
								<?php get_template_part( 'template-parts/location/tooltip', 'icon', array( 'tooltip_content' => 'You can use your pre-tax dollars for all of our services. Please check with your individual provider before service.' ) ) ?>
							</div>
							<p>See <a class="semi-bold underline" href="#memberships" primeiv-go-to-memberships>Membership & Pricing</a> at this location.</p>
						</div>
						<div class="col hsa-wrapper desktop">
							<img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/HSA&FSA.png" alt="Pay for your treatments with pre-tax dollars">
							<?php get_template_part( 'template-parts/location/tooltip', 'icon', array( 'tooltip_content' => 'You can use your pre-tax dollars for all of our services. Please check with your individual provider before service.' ) ) ?>
						</div>
					</div>

					<?php if ( $availabe_treatments ) : ?>
					<div class="iv-treatments">
						<h4 class="sub-title treatments-title">
							IV Treatments
						</h4>
						<div class="treatments-grid">
							<?php foreach ( $availabe_treatments as $treatment ) : ?>
								<?php primeiv_available_treatment_box( $treatment ); ?>
							<?php endforeach; ?>
						</div>
					</div>
					<?php endif; ?>

					<?php if ( $boost_treatments ) : ?>
					<div class="boost-treatments">
						<div class="treatments-grid">
							<div class="treatment-item item-info">
								<h4 class="sub-title">
									IV Treatment Boosts
								</h4>
								<p>Further customize your IV with a boost package!</p>
							</div>
							<?php foreach ( $boost_treatments as $treatment ) : ?>
								<?php primeiv_available_treatment_box( $treatment, 'rounded', 'global_iv_treatment_boosts' ); ?>
							<?php endforeach; ?>
						</div>
					</div>
					<?php endif; ?>

					<?php if ( $other_treatments ) : ?>
					<div class="other-treatments">
						<h4 class="sub-title">
							Other Treatments
						</h4>
						<div class="treatments-grid">
							<?php foreach ( $other_treatments as $treatment ) : ?>
								<?php primeiv_available_treatment_box( $treatment, '', 'global_other_treatments' ); ?>
							<?php endforeach; ?>
						</div>
					</div>
					<?php endif; ?>
				</div>
			</div>
			<?php if ( ! $coming_soon ) : ?>
			<div class="location-section introductory-section">
				<div class="location-page-introductory-section">
					<div class="wrap">
						<h3><?php the_field('ilp_introductory_offer_section_title'); ?></h3>
						<p class="subtitle"><?php the_field('ilp_introductory_offer_section_subtitle'); ?></p>
						<div class="content"><?php the_field('ilp_introductory_offer_section_text_only'); ?></div>
						<a class="btn-orange" target="_blank" href="<?php the_field('ilp_introductory_offer_section_btn_url'); ?>"><?php the_field('ilp_introductory_offer_section_btn_title'); ?></a>
					</div>
				</div>
			</div>
			<?php endif; ?>
		</div>

		<div class="mobile-collapse" data-section="why-use-iv-therapy-wrap">
			<div class="collapse-wrapper">
				<div class="mobile-collapse-title">
					<?php the_field( 'global_why_hydration_title', 'option' ); ?>
				</div>
				<div class="mobile-collapse-icon">
				<i class="fas fa-chevron-down"></i>
				</div>
			</div>
		</div>
		<div id="why-use-iv-therapy-wrap" class="mobile-collapse-content">
			<div id="why-use-iv-therapy" class="section-id location-section why-section">
				<div class="location-page-why-use-section">
					<div class="wrap">
						<h3><?php the_field( 'global_why_hydration_title', 'option' ); ?></h3>
						<div class="separator-wrap"><div class="separator"></div></div>
						<div class="content why-content"><?php the_field( 'global_why_hydration_content', 'option' ); ?></div>
						<div class="see-more-btn">
							<a href="javascript:void(0);" class="see-more-why underline"><span class="see-more">Read More</span><span class="see-less">Read Less</span></a></div>
					</div>
				</div>
				<div class="proper-section">
					<div class="wrap text-center">
						<h3 class="main-title text-center">
							<?php the_field( 'global_hydration_so_important_title', 'option' ); ?>
						</h3>
						<div class="separator-wrap"><div class="separator"></div></div>
						<h4>
							<?php the_field( 'global_hydration_so_important_subtitle', 'option' ); ?>
						</h4>
					</div>
					<div class="semi-wrap">
						<div class="image-wrapper">
							<?php
							$image = get_field( 'global_hydration_so_important_image', 'option' );
							$size  = 'full';
							if ( $image ) {
								echo wp_get_attachment_image( $image['id'], $size );
							}
							?>
						</div>
						<div class="description-box dots-bg">
							<?php the_field( 'global_hydration_so_important_box_content', 'option' ); ?>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="mobile-collapse" data-section="how-does-it-work-wrap">
			<div class="collapse-wrapper">
				<div class="mobile-collapse-title">
					How Does It Work?
					<?php //echo $how_title; ?>
				</div>
				<div class="mobile-collapse-icon">
				<i class="fas fa-chevron-down"></i>
				</div>
			</div>
		</div>
		<div id="how-does-it-work-wrap" class="mobile-collapse-content">
			<div id="how-does-it-work" class="section-id location-section location-how-it-works dots-bg">
				<div class="semi-wrap">
					<h3 class="main-title"><?php echo $how_title; ?></h3>
					<div class="how-it-works-grid how">
						<?php if ( $how ) : ?>
							<?php foreach ( $how as $item ) : ?>
								<div>
									<img src="<?php echo $item['icon']['url']; ?>" alt="<?php echo $item['icon']['alt']; ?>">
									<div class="item-title"><?php echo $item['title']; ?></div>
									<p><?php echo $item['text']; ?></p>
								</div>
							<?php endforeach; ?>
						<?php endif; ?>
					</div>
					<div class="why">
						<div>
							<h3><?php echo $vitamins_title; ?></h3>
							<div class="top">
								<div class="comparison">
									<div class="info">
										<h4><?php echo $percentage_title; ?></h4>
										<p><?php echo $percentage_text; ?></p>
									</div>
									<div>
										<div class="cell">
											<span>IV</span>
											<?php if ( $iv = $percentage_iv ) {
												?>
												<img src="<?php echo $iv['url']; ?>" alt="<?php echo $iv['alt']; ?>">
											<?php } ?>
										</div>
									</div>
									<div>
										<div class="cell">
											<span>Oral</span>
											<?php if ( $oral = $percentage_oral ) {
												?>
												<img src="<?php echo $oral['url']; ?>" alt="<?php echo $oral['alt']; ?>">
											<?php } ?>
										</div>
									</div>
								</div>
							</div>
							<div class="bottom">
								<div class="comparison">
									<div class="info">
										<h4><?php echo $time_title; ?></h4>
										<p><?php echo $time_text; ?></p>
									</div>
									<div>
										<div class="cell">
											<?php if ( $iv = $time_iv ) {
												?>
												<img src="<?php echo $iv['url']; ?>" alt="<?php echo $iv['alt']; ?>">
											<?php } ?>
											<p><?php echo $time_iv_caption; ?></p>
										</div>
									</div>
									<div>
										<div class="cell">
											<?php if ( $oral = $time_oral ) {
												?>
												<img src="<?php echo $oral['url']; ?>" alt="<?php echo $oral['alt']; ?>">
											<?php } ?>
											<p><?php echo $time_oral_caption; ?></p>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="location-section extra-service">
				<div class="semi-wrap">
					<h3 class="main-title extra-service-title text-center"><?php the_field( 'global_massage_title', 'option' ); ?></h3>
					<div class="image-wrapper">
						<?php
							$chair_image = ( get_field( 'ilp_massage_chair_image' ) ) ? get_field( 'ilp_massage_chair_image' ) : get_field( 'global_massage_image', 'option' );
							$size        = 'full';
							if ( $chair_image ) {
								echo wp_get_attachment_image( $chair_image['id'], $size );
							}
						?>
					</div>
					<div class="info text-center">
						<?php the_field( 'global_massage_description', 'option' ); ?>
					</div>
				</div>
			</div>
		</div>

		<div class="mobile-collapse" data-section="services-prices-wrap">
			<div class="collapse-wrapper">
				<div class="mobile-collapse-title">
					Services & Prices
				</div>
				<div class="mobile-collapse-icon">
				<i class="fas fa-chevron-down"></i>
				</div>
			</div>
		</div>
		<div id="services-prices-wrap" class="mobile-collapse-content">
			<div id="services-prices" class="section-id location-section services">
				<div class="semi-wrap">
					<div class="location-page-memberships-section">
						<h3>Services and Prices at Our Location</h3>
						<div class="separator-wrap"><div class="separator separator__blue"></div></div>

                        <?php do_action('primeiv_location_services_and_prices_top', get_the_ID()) ?>
                        
						<div id="memberships" class="section-id item">
							<div class="item-content">
								<div class="item-title">
									<span>Simplify and Save With a Membership</span>
									<?php if ( $show_packages == 'yes' ) : ?>
									<div>Also see our <a class="underline" href="#packages">packages</a></div>
									<?php endif; ?>
								</div>
							</div>
						</div>
						<div class="item-text"><p><?php the_field( 'ilpd_memberships_section_description' ); ?></p></div>
						<div class="wrap">
							<div class="list-columns">
								<?php echo $memberships_section_features; ?>
							</div>
						</div>
						<div class="memberships-wrap">
							<?php
								$memValue = get_field('ilp_membership_section_vp');
								$memOnly = get_field('ilp_membership_section_mp');
							?>
							<div class="memberships-option select">
								<div class="title"><span>Select</span></div>
								<div class="wrap">
									<div class="content"><?php the_field('ilpd_memberships_select_content'); ?></div>
									<div class="extra-wrap">
										<div class="price-wrap">
											<div class="price">
												<div class="value"><?php echo $memValue['ilp_membership_section_vp_select']; ?></div>
												<div class="only"><?php echo $memOnly['ilp_membership_section_mp_select']; ?></div>
											</div>
											<div class="icon">
												<img src="<?php echo esc_url( site_url( '/' ) ); ?>wp-content/uploads/2021/04/orange-drop.png" alt="Orange drop icon">
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="memberships-option essentials">
								<div class="title"><span>Essentials</span></div>
								<div class="wrap">
									<div class="content"><?php the_field('ilpd_memberships_essentials_content'); ?></div>
									<div class="extra-wrap">
										<div class="price-wrap">
											<div class="price">
												<div class="value"><?php echo $memValue['ilp_membership_section_vp_essentials']; ?></div>
												<div class="only"><?php echo $memOnly['ilp_membership_section_mp_essentials']; ?></div>
											</div>
											<div class="icon">
												<img src="<?php echo esc_url( site_url( '/' ) ); ?>wp-content/uploads/2021/04/lightblue-drop.png" alt="Lightblue drop icon">
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="memberships-option transformation">
								<div class="title"><span>Transformation</span></div>
								<div class="wrap">
									<div class="content"><?php the_field('ilpd_memberships_transformation_content'); ?></div>
									<div class="extra-wrap">
										<div class="price-wrap">
											<div class="price">
												<div class="value"><?php echo $memValue['ilp_membership_section_vp_transformation']; ?></div>
												<div class="only"><?php echo $memOnly['ilp_membership_section_mp_transformation']; ?></div>
											</div>
											<div class="icon">
												<img src="<?php echo esc_url( site_url( '/' ) ); ?>wp-content/uploads/2021/04/royalblue-drop.png" alt="Royalblue drop icon">
											</div>
										</div>
									</div>
								</div>
							</div>
			
						</div>
					</div>
				</div>
			</div>
			<div id="treatments-prices" class="section-id location-section therapies">
				<div class="semi-wrap">
					<div class="therapies-grid">
						
						<?php if ( $showTherapy1 == 'yes' ) : ?>
							<div class="location-page-int-therapies-section showTherapy1 therapy-card">
								<div class="therapy-wrapper">
									<div class="top-group">
										<div class="item">
											<div class="icon"><img src="<?php echo esc_url( site_url( '/' ) ); ?>wp-content/uploads/2021/04/intravenous-saline-drip-2x.png" alt="Intravenous saline drip"></div>
											<div class="item-content">
												<div class="item-title"><h4><?php the_field( 'global_it_therapy_title', 'option' ); ?></h4></div>
												<div class="item-text"><?php the_field( 'global_it_therapy_description', 'option' ); ?></div>
											</div>
										</div>
										<div class="info-list">
											<?php the_field( 'global_it_therapy_features', 'option' ); ?>
										</div>
										<?php if ( get_field( 'global_it_therapy_extras', 'option' ) ) : ?>
										<div class="add-treatment">
											<div class="add-treatment-title">Add to any treatment:</div>
											<div class="icon-list">
												<?php foreach ( get_field( 'global_it_therapy_extras', 'option' ) as $xtra ) : ?>
												<div>
													<?php
														$image = $xtra['extra_icon'];
														$size  = 'full';
														if( $image ) {
															echo wp_get_attachment_image( $image['id'], $size );
														}
													?>
													<?php echo $xtra['extra_title']; ?>
												</div>
												<?php endforeach; ?>
											</div>
										</div>
										<?php endif; ?>
									</div>
									<div class="buttons">
										<a class="btn-orange" target="_blank" href="<?php the_field('ilpd_booking_url'); ?>">Book Now</a>
										<a class="btn-blue" href="/iv-treatments/">Learn More</a>
									</div>
									<div class="int-therapies-price">
										<table>
												<tr class="main-titles">
													<td>THERAPY</td>
													<td class="more-info">MORE INFO</td>
													<td class="blank"></td>
													<td class="price">PRICE</td>
												</tr>
												<?php
												if( have_rows('ilp_ints_items') ):
													while ( have_rows('ilp_ints_items') ) : the_row();
												?>
													<tr class="price-row">
														<td class="therapy"><?php the_sub_field('name'); ?></td>
														<td class="more-info">
															<?php get_template_part( 'template-parts/location/tooltip', 'icon', array(
																'tooltip_content' => primeiv_price_tooltip( get_sub_field( 'name' ), $global_intravenous )
															) ); ?>
														</td>
														<td class="blank"></td>
														<td class="price"><?php the_sub_field('ilp_ints_items_price'); ?></td>
													</tr>
												<?php
													endwhile;
												endif;
												?>
										</table>
										<?php $showSMbtn1 = get_field('ilp_its_see_more_button'); ?>
										<?php if ($showSMbtn1 == 'show') { ?>
										<div class="see-more-btn smbtn1"><a class="smbtn1"><i class="see-more-btn-icon"></i><span class="see-more">SEE MORE</span><span class="see-less">SEE LESS</span></a></div>
										<?php } ?>
									</div>
								</div>
							</div>
						<?php endif; ?>

						<?php if ( $showTherapy2 == 'yes' ) : ?>
							<div class="location-page-inj-therapies-section showTherapy2 therapy-card">
								<div class="therapy-wrapper">
									<div class="top-group">
										<div class="item">
											<div class="icon"><img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/injection-therapies.png" alt="Injection therapies"></div>
											<div class="item-content">
												<div class="item-title"><h4><?php the_field('global_ij_therapy_title', 'option'); ?></h4></div>
												<div class="item-text"><?php the_field('global_ij_therapy_description', 'option'); ?></div>
											</div>
										</div>
										<div class="info-list">
											<?php the_field('global_ij_therapy_features', 'option'); ?>
										</div>
									</div>
									<div class="buttons">
										<a class="btn-orange" target="_blank" href="<?php the_field('ilpd_booking_url'); ?>">Book Now</a>
										<a class="btn-blue" href="/iv-treatments/">Learn More</a>
									</div>
								<div class="inj-therapies-price">
									<table>
											<tr class="main-titles">
												<td>THERAPY</td>
												<td class="more-infno">MORE INFO</td>
												<td class="blank"></td>
												<td class="price">PRICE</td>
											</tr>
											<?php
											if( have_rows('ilp_injts_items') ):
												while ( have_rows('ilp_injts_items') ) : the_row();
											?>
												<tr class="price-row">
													<td class="therapy"><?php the_sub_field('name'); ?></td>
													<td class="more-info">
														<?php get_template_part( 'template-parts/location/tooltip', 'icon', array(
															'tooltip_content' => primeiv_price_tooltip( get_sub_field( 'name' ), $global_injection )
														) ); ?>
													</td>
													<td class="blank"></td>
													<td class="price"><?php the_sub_field('ilp_injts_items_price'); ?></td>
												</tr>
											<?php
												endwhile;
											endif;
											?>
									</table>
									<?php $showSMbtn2 = get_field('ilp_injts_see_more_button'); ?>
									<?php if ($showSMbtn2 == 'show') { ?>
									<div class="see-more-btn smbtn2"><a class="smbtn2"><i class="see-more-btn-icon"></i><span class="see-more">SEE MORE</span><span class="see-less">SEE LESS</span></a></div>
									<?php } ?>
								</div>
								</div>
							</div>
						<?php endif; ?>
						
						<?php if ($showTherapy5 == 'yes') : ?>
							<div class="location-page-inj-therapies-section nadit showTherapy5 therapy-card">
								<div class="therapy-wrapper">
								<div class="top-group">
									<div class="item">
										<div class="icon"><i></i></div>
										<div class="item-content">
											<div class="item-title"><h4><?php the_field( 'global_ni_therapy_title', 'options' ); ?></h4></div>
											<div class="item-text"><?php the_field( 'global_ni_therapy_description', 'option' ); ?></div>
										</div>
									</div>
									<div class="info-list">
										<?php the_field( 'global_ni_therapy_features', 'option' ); ?>
									</div>
								</div>
								<div class="buttons">
									<?php
										$book_now = get_field('ilp_nadit_book_now');
										$learn_more = get_field('ilp_nadit_learn_more');
									?>
									<a class="btn-orange" target="_blank" href="<?php
										echo $book_now ? $book_now : get_field('ilpd_booking_url');
									?>">Book Now</a>
									<a class="btn-blue" href="<?php
										echo $learn_more ? $learn_more : '/iv-treatments/';
									?>">Learn More</a>
								</div>
								<div class="inj-therapies-price">
									<table>
											<tr class="main-titles">
												<td>THERAPY</td>
												<td class="more-info">MORE INFO</td>
												<td class="blank"></td>
												<td class="price">PRICE</td>
											</tr>
											<?php
											if( have_rows('ilp_nadit_items') ):
												while ( have_rows('ilp_nadit_items') ) : the_row();
											?>
												<tr class="price-row">
													<td class="therapy"><?php the_sub_field('name'); ?></td>
													<td class="more-info">
														<?php get_template_part( 'template-parts/location/tooltip', 'icon', array(
															'tooltip_content' => primeiv_price_tooltip( get_sub_field( 'name' ), $global_infusion )
														) ); ?>
													</td>
													<td class="blank"></td>
													<td class="price"><?php the_sub_field('ilp_nadit_items_price'); ?></td>
												</tr>
											<?php
												endwhile;
											endif;
											?>
									</table>
									<?php $showSMbtn5 = get_field('ilp_nadit_see_more_button'); ?>
									<?php if ($showSMbtn5 == 'show') { ?>
									<div class="see-more-btn smbtn5"><a class="smbtn5"><i class="see-more-btn-icon"></i><span class="see-more">SEE MORE</span><span class="see-less">SEE LESS</span></a></div>
									<?php } ?>
								</div>
								</div>
							</div>
						<?php endif; ?>

						<?php if ( $showTherapy6 == 'yes' ) :
							$section_title = get_field( 'ilp_pts_section_title' );
							$section_text = get_field( 'ilp_pts_description' );
							$section_features = get_field( 'ilp_pts_therapy_features' );
							?>
							<div class="location-page-tb-therapies-section showTherapy6 therapy-card">
								<div class="therapy-wrapper">
									<div class="top-group">
										<div class="item">
											<div class="icon"><img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/peptides.svg" alt="Peptides"></div>
											<div class="item-content">
												<div class="item-title"><h4><?php echo $section_title ? $section_title : get_field('global_pep_therapy_title', 'option'); ?></h4></div>
												<div class="item-text"><?php echo $section_text ? $section_text : get_field('global_pep_therapy_description', 'option'); ?></div>
											</div>
										</div>
										<div class="info-list">
											<?php echo $section_features ? $section_features : get_field('global_pep_therapy_features', 'option'); ?>
										</div>
									</div>
									<div class="buttons">
										<?php
											$book_now = get_field('ilp_pts_book_now');
											$learn_more = get_field('ilp_pts_learn_more');
										?>
										<a class="btn-orange" target="_blank" href="<?php
											echo $book_now ? $book_now : get_field('ilpd_booking_url');
										?>">Book Now</a>
										<a class="btn-blue" href="<?php
											echo $learn_more ? $learn_more : '/peptide-therapy/';
										?>">Learn More</a>
									</div>
								<?php if ( have_rows('ilp_pts_items') ): ?>
								<div class="tb-therapies-price">
									<table>
											<tr class="main-titles">
												<td>PACKAGE</td>
												<td class="more-info">MORE INFO</td>
												<td class="blank"></td>
												<td class="price">PRICE</td>
											</tr>
											<?php
												while ( have_rows('ilp_pts_items') ) : the_row();
                                                
                                                if( 
                                                    strpos(strtolower(get_sub_field('name')), strtolower('ipamorelin')) !== false
                                                    || strpos(strtolower(get_sub_field('name')), strtolower('BPC-157')) !== false
                                                ) {
                                                    continue;
                                                }
											?>
												<tr class="price-row">
													<td class="therapy"><?php the_sub_field( 'name' ); ?></td>
													<td class="more-info">
														<?php get_template_part( 'template-parts/location/tooltip', 'icon', array(
															'tooltip_content' => primeiv_price_tooltip( get_sub_field( 'name' ), $global_peptides )
														) ); ?>
													</td>
													<td class="blank"></td>
													<td class="price"><?php the_sub_field( 'ilp_pts_items_price' ); ?></td>
												</tr>
											<?php
												endwhile;
											?>
									</table>
									<?php $showSMbtn6 = get_field('ilp_pts_see_more_button'); ?>
									<?php if ($showSMbtn6 == 'show') { ?>
									<div class="see-more-btn smbtn6"><a><i class="see-more-btn-icon"></i><span class="see-more">SEE MORE</span><span class="see-less">SEE LESS</span></a></div>
									<?php } ?>
								</div>
								<?php endif; ?>
								</div>
							</div>
						<?php endif; ?>
						
						<?php if ( $showTherapy3 == 'yes' ) : ?>
							<div class="location-page-cryo-therapies-section showTherapy3 therapy-card">
								<div class="therapy-wrapper">
									<div class="top-group">
										<div class="item">
											<div class="icon"><img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/cryotherapy.png" alt="Cryotherapy"></div>
											<div class="item-content">
												<div class="item-title"><h4><?php the_field('global_cr_therapy_title', 'option'); ?></h4></div>
												<div class="item-text"><?php the_field('global_cr_therapy_description', 'option'); ?></div>
											</div>
										</div>
										<div class="info-list">
											<?php the_field('global_cr_therapy_features', 'option'); ?>
										</div>
									</div>
									<div class="buttons">
										<a class="btn-orange" target="_blank" href="<?php the_field('ilpd_booking_url'); ?>">Book Now</a>
										<a class="btn-blue" href="/cryotherapy/">Learn More</a>
									</div>
									<div class="cryo-therapies-price">
										<table>
												<tr class="main-titles">
													<td>THERAPY</td>
													<td class="more-info">MORE INFO</td>
													<td class="blank"></td>
													<td class="price">PRICE</td>
												</tr>
												<?php
												if( have_rows('ilp_cts_items') ):
													while ( have_rows('ilp_cts_items') ) : the_row();
												?>
													<tr class="price-row">
														<td class="therapy"><?php the_sub_field('name'); ?></td>
														<td class="more-info">
															<?php get_template_part( 'template-parts/location/tooltip', 'icon', array(
																'tooltip_content' => primeiv_price_tooltip( get_sub_field( 'name' ), $global_cryotherapy )
															) ); ?>
														</td>
														<td class="blank"></td>
														<td class="price"><?php the_sub_field('ilp_cts_items_price'); ?></td>
													</tr>
												<?php
													endwhile;
												endif;
												?>
										</table>
										<?php $showSMbtn3 = get_field('ilp_cts_see_more_button'); ?>
										<?php if ($showSMbtn3 == 'show') { ?>
										<div class="see-more-btn smbtn3"><a><i class="see-more-btn-icon"></i><span class="see-more">SEE MORE</span><span class="see-less">SEE LESS</span></a></div>
										<?php } ?>
									</div>
								</div>
							</div>
						<?php endif; ?>
					
						<?php if ( $showTherapy4 == 'yes' ) : ?>
							<div class="location-page-tb-therapies-section showTherapy4 therapy-card">
								<div class="therapy-wrapper fix-h">
									<div class="top-group">
										<div class="item">
											<div class="icon"><img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/personalized-biometric-supplement-line.png" alt="Personalized biometric supplement line"></div>
											<div class="item-content">
												<div class="item-title"><h4><?php the_field('global_pbs_therapy_title', 'option'); ?></h4></div>
												<div class="item-text"><?php the_field('global_pbs_therapy_description', 'option'); ?></div>
											</div>
										</div>
										<div class="info-list">
											<?php the_field('global_pbs_therapy_features', 'option'); ?>
										</div>
									</div>
									<div class="buttons">
										<a class="btn-orange" target="_blank" href="<?php echo get_field('ilp_tbs_see_more_button_link') ? get_field('ilp_tbs_see_more_button_link') : 'https://rootinevitamins.df7rps.net/c/2201420/788664/10924'; ?>">More Info</a>
									</div>
								<?php if ( have_rows('ilp_tbts_items') ): ?>
								<div class="tb-therapies-price">
									<table>
											<tr class="main-titles">
												<td>PACKAGE</td>
												<td class="more-info">MORE INFO</td>
												<td class="blank"></td>
												<td class="price">PRICE</td>
											</tr>
											<?php
												while ( have_rows('ilp_tbts_items') ) : the_row();
											?>
												<tr class="price-row">
													<td class="therapy"><?php the_sub_field( 'name' ); ?></td>
													<td class="more-info">
														<?php get_template_part( 'template-parts/location/tooltip', 'icon', array(
															'tooltip_content' => primeiv_price_tooltip( get_sub_field( 'name' ), $global_supplement )
														) ); ?>
													</td>
													<td class="blank"></td>
													<td class="price"><?php the_sub_field( 'ilp_tbts_items_price' ); ?></td>
												</tr>
											<?php
												endwhile;
											?>
									</table>
									<?php $showSMbtn4 = get_field('ilp_tbs_see_more_button'); ?>
									<?php if ($showSMbtn4 == 'show') { ?>
									<div class="see-more-btn smbtn4"><a><i class="see-more-btn-icon"></i><span class="see-more">SEE MORE</span><span class="see-less">SEE LESS</span></a></div>
									<?php } ?>
								</div>
								<?php endif; ?>
								</div>
							</div>
						<?php endif; ?>

						<?php if ( $mobile_iv_show == 'yes' ) : ?>
							<div class="location-page-tb-therapies-section ilp-mobileiv therapy-card">
								<div class="therapy-wrapper">
									<div class="top-group">
										<div class="item">
											<div class="icon"><img src="<?php echo esc_url( site_url( '/' ) ); ?>wp-content/uploads/2021/08/mobile-iv.png" alt="Mobile IV Therapy"></div>
											<div class="item-content">
												<div class="item-title"><h4><?php the_field('global_mb_therapy_title', 'option'); ?></h4></div>
												<div class="item-text"><?php the_field('global_mb_therapy_description', 'option'); ?></div>
											</div>
										</div>
									</div>
									<div class="buttons">
										<a class="btn-orange" href="<?php the_field('ilp_mobileiv_contact_button_link'); ?>">Contact Us Now</a>
									</div>
								</div>
							</div>
						<?php endif; ?>

						<?php if ( $show_gift_certificate ) : ?>
						<div class="location-page-gift-certificate">
							<div class="gift-certificate-title"><?php the_field( 'iv_locations_gift_certificate_title', 'option' ); ?></div>
							<div class="gift-certificate-text"><?php the_field( 'iv_locations_gift_certificate_description', 'option' ); ?></div>
							<div class="gift-certificate-button">
								<span><?php the_field( 'iv_locations_gift_certificate_note', 'option' ); ?></span>
							</div>
						</div>
						<?php endif; ?>

					</div>
				</div>
			</div>

			<?php if (
                $show_packages == 'yes'
                && ! empty($packages)
            ) : ?>
			<div id="packages" class="section-id location-section packages">
				<div class="semi-wrap">
					<h3 class="main-title">Get Optimal Results With a Package</h3>
					<div class="packages-grid">
						<?php foreach ( $packages as $p ) : ?>
						<div class="package-item">
							<div class="package-item-title">
								<?php echo $p['iv_global_package_title'] ?>
							</div>
							<div class="package-item-header">
								<?php echo $p['iv_global_package_features']; ?>
								<div class="item-divider"></div>
							</div>
							<div class="package-item-content">
								<?php echo $p['iv_global_package_description']; ?>
							</div>
							<div class="package-item-footer text-center">
								<div class="item-divider"></div>
								<p><?php echo $p['iv_global_package_notes']; ?></p>
								<a class="btn-orange" target="_blank" href="<?php the_field('ilpd_booking_url'); ?>">Book Now</a>
							</div>
						</div>
						<?php endforeach; ?>
					</div>
				</div>
			
			</div>
			<?php endif; ?>
		</div>

		<div class="mobile-collapse" data-section="testimonials-wrap">
			<div class="collapse-wrapper">
				<div class="mobile-collapse-title">
					Testimonials
				</div>
				<div class="mobile-collapse-icon">
				<i class="fas fa-chevron-down"></i>
				</div>
			</div>
		</div>
		<div id="testimonials-wrap" class="mobile-collapse-content">
			<div id="testimonials" class="section-id location-section videos-section">
				<div class="location-page-video-section semi-wrap">

					<?php if ( $video_testimonials ) : ?>
					<div class="videos-wrapper">
						<h3>WHAT PEOPLE ARE SAYING</h3>
						<div class="separator-wrap"><div class="separator"></div></div>
						<div class="videos-wrap">

							<?php foreach ( $video_testimonials as $vt ) : ?>
								<div class="video-item">
									<div class="video-title"><?php echo $vt['global_wpas_section_video_title']; ?></div>
									<div class="video-desc"><?php echo $vt['global_wpas_section_video_desc']; ?></div>
									<div class="video">
										<a class="video__link" href="" id="<?php echo $vt['glolbal_wpas_section_video_url']; ?>">
											<picture>
												<img class="video__media" src="<?php echo $vt['global_wpas_section_video_imagecover']['url']; ?>" alt="Video thumbnail">
											</picture>
										</a>
										<button class="video__button">
											<i></i>
										</button>
									</div>
								</div>
							<?php endforeach; ?>

						</div>
					</div>
					<?php endif; ?>
				</div>
			</div>
		</div>

		<div class="mobile-collapse" data-section="faqs-wrap">
			<div class="collapse-wrapper">
				<div class="mobile-collapse-title">
					FAQs
				</div>
				<div class="mobile-collapse-icon">
					<i class="fas fa-chevron-down"></i>
				</div>
			</div>
		</div>
		<div id="faqs-wrap" class="mobile-collapse-content">
			<div id="faqs" class="section-id location-section faq-section">
				<div class="semi-wrap">
					<div class="faq-wrapper">
						<div class="faq-content">
							<h3 class="main-title">Frequently Asked Questions</h3>
							<div class="separator-wrap"><div class="separator"></div></div>
							<h4>QUESTIONS?</h4>
							<p>Let’s walk you through the educational and scientific details you may need.</p>
							<div class="questions">
								<?php if ( $global_faqs) : ?>
									<?php foreach ( $global_faqs as $q ) : ?>
										<div class="question-item">
											<div class="question-header">
												<div class="question-title"><?php echo $q['faq_title'] ?></div>
												<div class="question-icon"><i class="fa prime-faq"></i></div>
											</div>
											<div class="question-content">
												<p><?php echo $q['faq_content'] ?></p>
											</div>
										</div>
									<?php endforeach; ?>
								<?php endif; ?>

								<?php if ( get_field( 'location_faqs' ) ) : ?>
									<?php foreach ( get_field( 'location_faqs' ) as $q ) : ?>
										<div class="question-item">
											<div class="question-header">
												<div class="question-title"><?php echo $q['faq_title'] ?></div>
												<div class="question-icon"><i class="fa prime-faq"></i></div>
											</div>
											<div class="question-content">
												<p><?php echo $q['faq_content'] ?></p>
											</div>
										</div>
									<?php endforeach; ?>
								<?php endif; ?>
							</div>
						</div>

						<?php if( get_field( 'ilp_phone_number' ) ): ?>
						<div class="faq-cta">
							<div class="cta-content">
								<div class="cta-title">Can’t find your answer?</div>
								<div class="cta-text">
									<p>Give us a call!</p>
								</div>
								<div class="cta-button">
									<a class="btn-orange" href="tel:<?php the_field( 'ilp_phone_number' ); ?>"><?php the_field( 'ilp_phone_number' ); ?></a>
								</div>
							</div>
						</div>
						<?php endif; ?>

					</div>
				</div>
			</div>
		</div>

	</div>
</div>
