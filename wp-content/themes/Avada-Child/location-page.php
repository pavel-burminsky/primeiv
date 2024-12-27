<?php
/**
 * Template Name: Individual location page
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct script access denied.' );
}

$coming_soon = primeiv_is_location_coming_soon();
?>

<?php get_header(); ?>

<section id="content" class="full-width">

	<?php while ( have_posts() ) : ?>
		<?php the_post(); ?>
		<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<?php echo fusion_render_rich_snippets_for_pages(); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			<?php avada_singular_featured_image(); ?>
			<div class="post-content">
				<?php //the_content(); ?>
				<?php //fusion_link_pages(); ?>

				<?php get_template_part( 'template-parts/location/secondary-nav','', ['is_coming_soon' => $coming_soon] ); ?>

				<?php get_template_part( 'template-parts/location/overview', '', ['is_coming_soon' => $coming_soon] ); ?>

				<?php if ( $coming_soon && get_field( 'cs_intro_offer' ) == 'on' ) : ?>
					<?php get_template_part( 'template-parts/location/coming-soon' ); ?>
				<?php endif; ?>

				<?php get_template_part( 'template-parts/location/details', '', ['is_coming_soon' => $coming_soon] ); ?>

			</div>
		</div>
	<?php endwhile; ?>
</section>

<div class="clearfix"></div>

<?php if ( get_field( 'ilpd_wp_business_reviews_shortcode' ) ) : ?>
<div class="testimonials dots-bg">
	<div class="testimonials-wrapper">
		<?php echo do_shortcode( get_field( 'ilpd_wp_business_reviews_shortcode' ) ); ?>
	</div>
</div>
<?php endif; ?>

<div class="location-mobile-bottom-menu">
	<div class="location-mobile-bottom-menu-inner">
		<div class="mobile-phone">
			<a href="tel:<?php the_field( 'ilp_phone_number' ); ?>">
				<i class="fa fa-phone" aria-hidden="true"></i>
			</a>
		</div>
		<div class="mobile-chat"></div>
		<div class="mobile-appointment">
			<?php if ( ! $coming_soon ) : ?>
				<a class="btn-orange" target="_blank" href="<?php the_field('ilpd_booking_url'); ?>">Book an Appointment</a>
			<?php else : ?>
				<a class="btn-orange" href="<?php the_field('cs_get_notified_button_link'); ?>"><?php the_field('cs_get_notified_button_text'); ?></a>
			<?php endif; ?>
		</div>
	</div>
</div>

<script type="text/javascript">
	jQuery(document).ready(function(){

		jQuery('.top-group').matchHeight({ property: 'min-height' });

		// Get position of the element on scroll
		var isAdminBar = jQuery('#wpadminbar').length;
		var stickyNavTop = (isAdminBar) ? jQuery('.sub-nav-wrapper').offset().top - jQuery('#wpadminbar').height() : jQuery('.sub-nav-wrapper').offset().top;
		let itemsInViewport = [];

		onScrollEvents();
		onResizeEvents();
		onClickEvents();
		
		setTimeout(function(){
			initChatWidget();
		}, 2000);

		function onClickEvents() {
			jQuery(document).on('click', '.mobile-collapse', function(e){
				e.preventDefault();
				let element = jQuery(this).data('section');
				jQuery(`#${element}`).slideToggle();
				jQuery(this).toggleClass('active');
				jQuery(`#${element}`).addClass('active');
			});

			jQuery(document).on('click', '.see-more-why', function(e){
				e.preventDefault();
				jQuery(this).toggleClass('active');
				let content = jQuery('.why-content');
				content.find('p:not(:first-child)').toggle();
			});

			jQuery(document).on('click', '.sidebar-menu a',function() {
				let that = this;
				setTimeout(() => {
					jQuery('.sidebar-menu a').removeClass('active');
					jQuery(that).addClass('active');
				}, 100);
			});
		}

		function onResizeEvents() {
			toggleMobileNav();
			window.addEventListener('resize', function() {
				toggleMobileNav();
				initChatWidget();
			});
		}

		function toggleMobileNav() {
			let toggles = jQuery('.mobile-collapse');
			jQuery.each(toggles, function(index, toggle) {
				let content = jQuery(toggle).data('section');
				if (jQuery(window).width() > 1250) {
					jQuery(`#${content}`).show();
					jQuery(`#${content}`).removeClass('active');
					jQuery(toggle).removeClass('active');
				} 
			});
		}

		function onScrollEvents(){
			let sideNav = document.getElementById('location-sidebar-wrapper');
			if(sideNav !== null) {
				window.addEventListener('scroll', function () {
					sideNavScroll(sideNav);
				});
			}

			window.addEventListener('scroll', function () {
				var scrollTop = jQuery(document).scrollTop();
				if (scrollTop > stickyNavTop) {
				jQuery('.sub-nav-wrapper').addClass('sticky-nav');
				} else {
				jQuery('.sub-nav-wrapper').removeClass('sticky-nav');
				}
			});
		}

		function clearSelectedSideNav() {
			let sideNavSelected = document.querySelectorAll('.sidebar-menu li a.active');
			sideNavSelected.forEach(sideNav => {
				sideNav.classList.remove('active');
			});
		}

		function sideNavScroll(sideNav) {

			let sideNavLinks = document.querySelectorAll('.sidebar-menu li a');
			let fromTop = window.scrollY + 100;

			let sectionsID = jQuery(".section-id");
			jQuery.each(sectionsID, function () {
				// If is in viewport
				if (jQuery(this).isInViewport()) {
					itemsInViewport.push({
						id:jQuery(this).attr('id'),
						top:jQuery(this).offset().top
					});
					//return false;
				}
			});

			// Get the closest section to the top
			if ( itemsInViewport.length > 0 ) {
				let closestSection = itemsInViewport.reduce(function(prev, curr) {
					return (Math.abs(curr.top - fromTop) < Math.abs(prev.top - fromTop) ? curr : prev);
				});

				//console.log(closestSection, fromTop);
				let sectionID = closestSection.id;
				let sideNavSelected = document.querySelector('.sidebar-menu li a[href="#' + sectionID + '"]');
				let stckyNavSelected = document.querySelector('.sub-nav li a[href="#' + sectionID + '"]');
				if (sideNavSelected !== null) {
					clearSelectedSideNav();
					sideNavSelected.classList.add('active');
				}

				if (stckyNavSelected !== null) {
					jQuery('.sub-nav li a').removeClass('active');
					stckyNavSelected.classList.add('active');
				}
			}
		}

		function initChatWidget() {

			let parent = jQuery('.location-mobile-bottom-menu');
			let chatwidget = jQuery('chat-widget');
			if (chatwidget.length > 0) {
				let sr = chatwidget[0].shadowRoot;
				let button = sr.querySelector('.lc_text-widget--btn');
				let prompt = sr.querySelector('.Prompt');
				let style = document.createElement('style');
					style.innerHTML = `
						.chat-widget--open {
							right: 0 !important;
							left: 71px !important;
							bottom: 16px !important;
							background-color: #F4F4F4 !important;
						}
						.chat-widget--open svg {
							fill: #05518F;
						}
						.chat-widget--open:hover, .chat-widget--open:focus {
							box-shadow: 0px 0px 0px #fff !important;
							filter: brightness(1) !important;
							background-color: #F4F4F4 !important;
						}
						.Prompt.hide {
							display: none !important;
						}
						@media(max-width: 380px) {
							.chat-widget--open {
								left: 16% !important;
								bottom: 24px !important;
							}
						}
					`;

				if (  jQuery(window).width() <= 699 ) {
					// Check if parent is visible
					if (parent.is(':visible') && ! parent.hasClass('active')) {
						parent.addClass('active');
						sr.appendChild(style);
						button.classList.add('chat-widget--open');
						if (prompt !== null) {
							prompt.classList.add('hide');
						}
					} 
				} else {
					
					if (button !== null) {
						button.classList.remove('chat-widget--open');
						parent.removeClass('active');
					}

					if (prompt !== null) {
						prompt.classList.remove('hide');
					}
				}
			}
		}


		jQuery(".page-template-location-page .see-more-btn.smbtn1 a.smbtn1").on("click", function() {

			if (jQuery(".page-template-location-page .see-more-btn.smbtn1 span.see-less").is(":hidden")) {
				jQuery('.page-template-location-page .see-more-btn.smbtn1 a i.see-more-btn-icon').addClass('less');
				jQuery('.page-template-location-page .see-more-btn.smbtn1 span.see-more').addClass('hide');
				jQuery('.page-template-location-page .see-more-btn.smbtn1 span.see-less').addClass('show');
				jQuery('.page-template-location-page #content .post-content .showTherapy1 table tr').addClass('show');
			} else {
				jQuery('.page-template-location-page .see-more-btn.smbtn1 a i.see-more-btn-icon').removeClass('less');
				jQuery('.page-template-location-page .see-more-btn.smbtn1 span.see-more').removeClass('hide');
				jQuery('.page-template-location-page .see-more-btn.smbtn1 span.see-less').removeClass('show');
				jQuery('.page-template-location-page #content .post-content .showTherapy1 table tr').removeClass('show');
			}

		});

		jQuery(".page-template-location-page .see-more-btn.smbtn2 a.smbtn2").on("click", function() {

			if (jQuery(".page-template-location-page .see-more-btn.smbtn2 span.see-less").is(":hidden")) {
				jQuery('.page-template-location-page .see-more-btn.smbtn2 a i.see-more-btn-icon').addClass('less');
				jQuery('.page-template-location-page .see-more-btn.smbtn2 span.see-more').addClass('hide');
				jQuery('.page-template-location-page .see-more-btn.smbtn2 span.see-less').addClass('show');
				jQuery('.page-template-location-page #content .post-content .showTherapy2 table tr').addClass('show');
			} else {
				jQuery('.page-template-location-page .see-more-btn.smbtn2 a i.see-more-btn-icon').removeClass('less');
				jQuery('.page-template-location-page .see-more-btn.smbtn2 span.see-more').removeClass('hide');
				jQuery('.page-template-location-page .see-more-btn.smbtn2 span.see-less').removeClass('show');
				jQuery('.page-template-location-page #content .post-content .showTherapy2 table tr').removeClass('show');
			}

		});

		jQuery(".page-template-location-page .see-more-btn.smbtn3 a").on("click", function() {

			if (jQuery(".page-template-location-page .see-more-btn.smbtn3 span.see-less").is(":hidden")) {
				jQuery('.page-template-location-page .see-more-btn.smbtn3 a i.see-more-btn-icon').addClass('less');
				jQuery('.page-template-location-page .see-more-btn.smbtn3 span.see-more').addClass('hide');
				jQuery('.page-template-location-page .see-more-btn.smbtn3 span.see-less').addClass('show');
				jQuery('.page-template-location-page #content .post-content .showTherapy3 table tr').addClass('show');
			} else {
				jQuery('.page-template-location-page .see-more-btn.smbtn3 a i.see-more-btn-icon').removeClass('less');
				jQuery('.page-template-location-page .see-more-btn.smbtn3 span.see-more').removeClass('hide');
				jQuery('.page-template-location-page .see-more-btn.smbtn3 span.see-less').removeClass('show');
				jQuery('.page-template-location-page #content .post-content .showTherapy3 table tr').removeClass('show');
			}

		});

		jQuery(".page-template-location-page .see-more-btn.smbtn4 a").on("click", function() {

			if (jQuery(".page-template-location-page .see-more-btn.smbtn4 span.see-less").is(":hidden")) {
				jQuery('.page-template-location-page .see-more-btn.smbtn4 a i.see-more-btn-icon').addClass('less');
				jQuery('.page-template-location-page .see-more-btn.smbtn4 span.see-more').addClass('hide');
				jQuery('.page-template-location-page .see-more-btn.smbtn4 span.see-less').addClass('show');
				jQuery('.page-template-location-page #content .post-content .showTherapy4 table tr').addClass('show');
			} else {
				jQuery('.page-template-location-page .see-more-btn.smbtn4 a i.see-more-btn-icon').removeClass('less');
				jQuery('.page-template-location-page .see-more-btn.smbtn4 span.see-more').removeClass('hide');
				jQuery('.page-template-location-page .see-more-btn.smbtn4 span.see-less').removeClass('show');
				jQuery('.page-template-location-page #content .post-content .showTherapy4 table tr').removeClass('show');
			}

		});

		jQuery(".page-template-location-page .see-more-btn.smbtn5 a").on("click", function() {

			if (jQuery(".page-template-location-page .see-more-btn.smbtn5 span.see-less").is(":hidden")) {
				jQuery('.page-template-location-page .see-more-btn.smbtn5 a i.see-more-btn-icon').addClass('less');
				jQuery('.page-template-location-page .see-more-btn.smbtn5 span.see-more').addClass('hide');
				jQuery('.page-template-location-page .see-more-btn.smbtn5 span.see-less').addClass('show');
				jQuery('.page-template-location-page #content .post-content .showTherapy5 table tr').addClass('show');
			} else {
				jQuery('.page-template-location-page .see-more-btn.smbtn5 a i.see-more-btn-icon').removeClass('less');
				jQuery('.page-template-location-page .see-more-btn.smbtn5 span.see-more').removeClass('hide');
				jQuery('.page-template-location-page .see-more-btn.smbtn5 span.see-less').removeClass('show');
				jQuery('.page-template-location-page #content .post-content .showTherapy5 table tr').removeClass('show');
			}

		});

		jQuery(".page-template-location-page .see-more-btn.smbtn6 a").on("click", function() {
			jQuery(".showTherapy6").css("height", "auto");
			if (jQuery(".page-template-location-page .see-more-btn.smbtn6 span.see-less").is(":hidden")) {
				jQuery('.page-template-location-page .see-more-btn.smbtn6 a i.see-more-btn-icon').addClass('less');
				jQuery('.page-template-location-page .see-more-btn.smbtn6 span.see-more').addClass('hide');
				jQuery('.page-template-location-page .see-more-btn.smbtn6 span.see-less').addClass('show');
				jQuery('.page-template-location-page #content .post-content .showTherapy6 table tr').addClass('show');
			} else {
				jQuery('.page-template-location-page .see-more-btn.smbtn6 a i.see-more-btn-icon').removeClass('less');
				jQuery('.page-template-location-page .see-more-btn.smbtn6 span.see-more').removeClass('hide');
				jQuery('.page-template-location-page .see-more-btn.smbtn6 span.see-less').removeClass('show');
				jQuery('.page-template-location-page #content .post-content .showTherapy6 table tr').removeClass('show');
			}

		});

		jQuery.fn.isInViewport = function() {
			var elementTop = jQuery(this).offset().top;
			var elementBottom = elementTop + jQuery(this).outerHeight();
			var id = jQuery(this).attr('id');
			if (id == 'how-does-it-work') {
				elementTop = jQuery('#how-does-it-work-wrap').offset().top;
				elementBottom =  elementTop + jQuery('#how-does-it-work-wrap').outerHeight();
			}

			if (id == 'memberships' ) {
				elementTop = elementTop + 100;
			}

			var viewportTop = jQuery(window).scrollTop();
			var viewportBottom = viewportTop + jQuery(window).height();
			
			return elementBottom > viewportTop && elementTop < viewportBottom;
		};

	});
</script>

<script type="text/javascript">

	function findAllVideos() {
		let videos = document.querySelectorAll('.page-template-location-page .location-page-video-section .video-item .video');

		for (let i = 0; i < videos.length; i++) {
			setupVideoPlay(videos[i]);
		}
	}

	function setupVideoPlay(video) {
		let link = video.querySelector('.video__link');
		let button = video.querySelector('.video__button');
		let id = link.id;

		video.addEventListener('click', () => {
			let iframe = createVideoIframe(id);

			link.remove();
			button.remove();
			video.setAttribute('style', 'padding:56.25% 0 0 0;position:relative;');
			video.appendChild(iframe);
		});

		link.removeAttribute('href');
		video.classList.add('video--enabled');
	}

	function createVideoIframe(id) {
		let iframe = document.createElement('iframe');

		iframe.setAttribute('style', 'position:absolute;top:0;left:0;width:100%;height:100%;');
		iframe.setAttribute('frameborder', '0');
		iframe.setAttribute('allow', 'autoplay; fullscreen; picture-in-picture');
		iframe.setAttribute('allowfullscreen', '');
		iframe.setAttribute('src', generateVideoURL(id));
		//iframe.classList.add('video__media');

		return iframe;
	}

	function generateVideoURL(id) {
		let query = '?autoplay=1';

		return 'https://player.vimeo.com/video/' + id + query;
	}

	findAllVideos();

</script>

<script src="https://player.vimeo.com/api/player.js"></script>

<script type="text/javascript">
	(function($){
		$(document).ready(function(){
			var fb = $('.location-page-custom-footer a.fb').attr('href'),
				inst = $('.location-page-custom-footer a.inst').attr('href');
			if(fb){
				$('.fusion-footer a.fusion-facebook').attr('href', fb);
			}
			if(inst){
				$('.fusion-footer a.fusion-instagram').attr('href', inst);
			}

			$('.location-video-section .thumb').click(function(){
				$(this).hide();
				var video = $(this).data('video');
				$('.location-video-section').append('<div class="iframe"><iframe width="560" height="315" src="'+video+'" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe></div>');
			});
		});
	})(jQuery);
</script>

<?php
add_action( 'avada_render_footer', function() {
	get_template_part( 'template-parts/location/footer' );
} );
get_footer();
?>
