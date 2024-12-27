<?php
/**
 * Defines the Admin_Review_Editor class
 *
 * @package WP_Business_Reviews\Includes\Admin
 * @since   0.1.0
 */

namespace WP_Business_Reviews\Includes\Admin;

use WP_Business_Reviews\Includes\Deserializer\Review_Deserializer;
use WP_Business_Reviews\Includes\Deserializer\Review_Source_Deserializer;
use WP_Business_Reviews\Includes\Serializer\Review_Serializer;
use WP_Business_Reviews\Includes\Platform_Manager;
use WP_Business_Reviews\Includes\Review;
use WP_Business_Reviews\Includes\Location;

/**
 * Customizes single reviews.
 *
 * @since 0.1.0
 */
class Admin_Review_Editor {
	/**
	 * The Review.
	 *
	 * @since 0.1.0
	 * @var Review $review
	 */
	private $review;

	/**
	 * Review deserializer.
	 *
	 * @since 0.1.0
	 * @var Review_Deserializer $review_deserializer
	 */
	private $review_deserializer;

	/**
	 * Review source deserializer.
	 *
	 * @since 0.1.0
	 * @var Review_Source_Deserializer $review_source_deserializer
	 */
	private $review_source_deserializer;

	/**
	 * Review serializer.
	 *
	 * @since 0.1.0
	 * @var Review_Serializer $review_serializer
	 */
	private $review_serializer;

	/**
	 * Platform manager.
	 *
	 * @since 1.1.0
	 * @var Platform_Manager $platform_manager
	 */
	private $platform_manager;

	/**
	 * Instantiates the Admin_Review_Editor object.
	 *
	 * @since 1.1.0 Added platform manager to manage active platforms.
	 * @since 0.1.0
	 *
	 * @param Review_Deserializer        $review_deserializer Retriever of reviews.
	 * @param Review_Source_Deserializer $review_deserializer Retriever of review sources.
	 * @param Review_Serializer          $review_serializer Saver of reviews.
	 * @param Platform_Manager           $platform_manager Platform manager.
	 */
	public function __construct(
		Review_Deserializer $review_deserializer,
		Review_Source_Deserializer $review_source_deserializer,
		Review_Serializer $review_serializer,
		Platform_Manager $platform_manager
	) {
		$this->review_deserializer        = $review_deserializer;
		$this->review_source_deserializer = $review_source_deserializer;
		$this->review_serializer          = $review_serializer;
		$this->platform_manager           = $platform_manager;
	}

	/**
	 * Registers functionality with WordPress hooks.
	 *
	 * @since 0.1.0
	 */
	public function register() {
		add_action( 'add_meta_boxes_wpbr_review', array( $this, 'init' ) );
		add_action( 'add_meta_boxes_wpbr_review', array( $this, 'add_meta_boxes' ) );
		add_filter( 'screen_options_show_screen', array( $this, 'remove_screen_options' ), 10, 2 );
		add_action( 'save_post_wpbr_review', array( $this, 'save' ), 10, 2 );
	}

	/**
	 * Initializes the object for use.
	 *
	 * @since 0.1.0
	 *
	 * @param WP_Post $post The post object.
	 */
	public function init( $post ) {
		$this->review = $this->review_deserializer->get_review( $post->ID );
	}

	/**
	 * Adds meta boxes for single review.
	 *
	 * @since 0.1.0
	 */
	public function add_meta_boxes() {
		add_meta_box(
			'wpbr-review-content-meta-box',
			__( 'Review Content', 'wp-business-reviews' ),
			array( $this, 'render_review_content_meta_box' ),
			'wpbr_review',
			'normal',
			'high'
		);

		add_meta_box(
			'wpbr-review-meta-meta-box',
			__( 'Review Meta', 'wp-business-reviews' ),
			array( $this, 'render_review_meta_meta_box' ),
			'wpbr_review',
			'side',
			'default'
		);
	}

	/**
	 * Removes the screen options pull down.
	 *
	 * @since 0.1.0
	 *
	 * @param bool      $display_boolean  Whether to display screen options.
	 * @param WP_Screen $wp_screen_object The screen object.
	 * @return bool Whether to display screen options.
	 */
	public function remove_screen_options( $display_boolean, $wp_screen_object ) {
		if ( 'wpbr_review' === $wp_screen_object->id ) {
			return false;
		}

		// Don't mess with other screens.
		return $display_boolean;
	}

	/**
	 * Renders the main review meta box.
	 *
	 * @since 0.1.0
	 *
	 * @param WP_Post $post The post object.
	 */
	public function render_review_content_meta_box( $post ) {
		$date_picker_date = $timestamp = '';
		$review_rating    = isset( $this->review->components['rating'] ) ? $this->review->components['rating'] : '0';
		$review_type      = 'rating';
		$rating_class     = '';
		$float_class      = ' wpbr-u-hidden';
		$reco_class       = ' wpbr-u-hidden';
		$review_content   = isset( $this->review->components['content'] ) ? $this->review->components['content'] : '';
		$reviewer_name    = isset( $this->review->components['reviewer_name'] ) ? $this->review->components['reviewer_name'] : '';
		$review_url       = isset( $this->review->components['review_url'] ) ? $this->review->components['review_url'] : '';
		$review_platform  = isset( $this->review->platform ) ? $this->review->platform : 'general';
		$review_source_id = isset( $this->review->review_source_id ) ? $this->review->review_source_id : '';
		$is_custom        = true;

		/**
		 * A review is "custom" if it is first created in the review editor rather than
		 * imported from a platform API. Custom reviews do not have a review source ID.
		 */
		if ( ! empty( $review_source_id ) ) {
			$is_custom = false;
		}

		// Determine review type and hide other rating fields accordingly.
		if ( is_numeric( $review_rating) && is_float( $review_rating + 0 ) ) {
			$review_type  = 'float_rating';
			$rating_class = $reco_class = ' wpbr-u-hidden';
			$float_class  = '';
		} elseif ( 'positive' === $review_rating || 'negative' === $review_rating ) {
			$review_type  = 'recommendation';
			$rating_class = $float_class = ' wpbr-u-hidden';
			$reco_class   = '';
		}

		if ( isset( $this->review->components['timestamp'] ) ) {
			$unix_time        = strtotime( $this->review->components['timestamp'] );
			$date_picker_date = date_i18n( get_option( 'date_format' ), $unix_time );
			$timestamp        = date_i18n( 'Y-m-d', $unix_time );
		} else {
			$date_picker_date = date_i18n( get_option( 'date_format' ) );
			$timestamp        = date_i18n( 'Y-m-d' );
		}

		wp_nonce_field( 'wpbr_review_save', 'wpbr_review_nonce', false );
		?>

		<input id="wpbr-control-post-id" type="hidden" name="wpbr_review[post_id]" value="<?php echo $post->ID; ?>">

		<div id="wpbr-field-reviewer-image" class="wpbr-field-wrap">
			<p class="wpbr-label">
				<label for="wpbr-control-reviewer-name"><?php esc_html_e( 'Reviewer Image', 'wp-business-reviews' ); ?></label>
				<span class="wpbr-field-description"><?php esc_html_e( 'Displays a photo or avatar of the reviewer. For best results, use a 120x120 pixel square image.', 'wp-business-reviews' ); ?></span>
			</p>
			<?php $this->render_image_selector( $post ); ?>
		</div>

		<div id="wpbr-field-reviewer-name" class="wpbr-field-wrap">
			<p class="wpbr-label">
				<label for="wpbr-control-reviewer-name"><?php esc_html_e( 'Reviewer Name', 'wp-business-reviews' ); ?></label>
			</p>
			<input id="wpbr-control-reviewer-name" type="text" name="wpbr_review[components][reviewer_name]"
					value="<?php echo esc_attr( $reviewer_name ); ?>">
		</div>

		<div id="wpbr-field-review-type" class="wpbr-field-wrap">
			<fieldset>
				<legend class="wpbr-legend"><?php esc_html_e( 'Review Type', 'wp-business-reviews' ); ?></legend>
				<span class="wpbr-field-description"><?php esc_html_e( 'Determines whether the review displays a rating or recommendation type.', 'wp-business-reviews' ); ?></span>
				<div class="wpbr-field__option">
					<input id="wpbr-control-review-type-rating" class="js-wpbr-control-review-type" type="radio" name="wpbr_review[wpbr_review_type]" value="rating" <?php checked( $review_type, 'rating' ); ?>>
					<label for="wpbr-control-review-type-rating"><?php echo esc_html__( 'Star Rating', 'wp-business-reviews' ); ?></label>
				</div>
				<div class="wpbr-field__option">
					<input id="wpbr-control-review-type-float-rating" class="js-wpbr-control-review-type" type="radio" name="wpbr_review[wpbr_review_type]" value="float_rating" <?php checked( $review_type, 'float_rating' ); ?>>
					<label for="wpbr-control-review-type-float-rating"><?php echo esc_html__( 'Numerical Rating', 'wp-business-reviews' ); ?></label>
				</div>
				<div class="wpbr-field__option">
					<input id="wpbr-control-review-type-reco" class="js-wpbr-control-review-type" type="radio" name="wpbr_review[wpbr_review_type]" value="recommendation" <?php checked( $review_type, 'recommendation' ); ?>>
					<label for="wpbr-control-review-type-reco"><?php echo esc_html__( 'Recommendation', 'wp-business-reviews' ); ?></label>
				</div>
			</fieldset>
		</div>

		<div id="wpbr-field-rating" class="wpbr-field-wrap<?php echo esc_attr( $rating_class ); ?>">
			<fieldset>
				<legend class="wpbr-legend"><?php esc_html_e( 'Rating', 'wp-business-reviews' ); ?></legend>
				<span class="wpbr-field-description"><?php esc_html_e( 'Defines the rating associated with the review. Rating style is based on the selected platform.', 'wp-business-reviews' ); ?></span>
				<div class="wpbr-rating">
					<input id="wpbr-control-five-star" type="radio" name="wpbr_review[components][rating]" value="5" <?php checked( $review_rating, '5' ); ?>>
					<label for="wpbr-control-five-star"></label>
					<input id="wpbr-control-four-star" type="radio" name="wpbr_review[components][rating]" value="4" <?php checked( $review_rating, '4' ); ?>>
					<label for="wpbr-control-four-star"></label>
					<input id="wpbr-control-three-star" type="radio" name="wpbr_review[components][rating]" value="3" <?php checked( $review_rating, '3' ); ?>>
					<label for="wpbr-control-three-star"></label>
					<input id="wpbr-control-two-star" type="radio" name="wpbr_review[components][rating]" value="2" <?php checked( $review_rating, '2' ); ?>>
					<label for="wpbr-control-two-star"></label>
					<input id="wpbr-control-one-star" type="radio" name="wpbr_review[components][rating]" value="1" <?php checked( $review_rating, '1' ); ?>>
					<label for="wpbr-control-one-star"></label>
					<input id="wpbr-control-zero-stars" type="radio" name="wpbr_review[components][rating]" value="0" <?php checked( $review_rating, '0' ); ?>>
				</div>
			</fieldset>
		</div>

		<div id="wpbr-field-float-rating" class="wpbr-field-wrap<?php echo esc_attr( $float_class ); ?>">
			<p class="wpbr-label">
				<label for="wpbr-control-float-rating"><?php esc_html_e( 'Numerical Rating', 'wp-business-reviews' ); ?></label>
				<span class="wpbr-field-description"><?php esc_html_e( 'Define the numerical rating as a whole number or decimal value. Intended for use with a Zomato review source.', 'wp-business-reviews' ); ?></span>
			</p>
			<input id="wpbr-control-float-rating" type="number" name="wpbr_review[float_rating]" value="<?php echo esc_attr( $review_rating ); ?>" min="0" step="0.1">
		</div>

		<div id="wpbr-field-recommendation" class="wpbr-field-wrap<?php echo esc_attr( $reco_class ); ?>">
			<fieldset>
				<legend class="wpbr-legend"><?php esc_html_e( 'Recommendation', 'wp-business-reviews' ); ?></legend>
				<span class="wpbr-field-description"><?php esc_html_e( 'Defines the positive or negative nature of the recommendation. Intended for use with a Facebook review source.', 'wp-business-reviews' ); ?></span>
				<div class="wpbr-field__option">
					<input id="wpbr-control-positive" type="radio" name="wpbr_review[components][rating]" value="positive" <?php checked( $review_rating, 'positive' ); ?>>
					<label for="wpbr-control-positive"><?php echo esc_html__( 'Positive', 'wp-business-reviews' ); ?></label>
				</div>
				<div class="wpbr-field__option">
					<input id="wpbr-control-negative" type="radio" name="wpbr_review[components][rating]" value="negative" <?php checked( $review_rating, 'negative' ); ?>>
					<label for="wpbr-control-negative"><?php echo esc_html__( 'Negative', 'wp-business-reviews' ); ?></label>
				</div>
			</fieldset>
		</div>

		<div id="wpbr-field-review-date" class="wpbr-field-wrap">
			<?php if ( $is_custom ) : ?>
				<p class="wpbr-label">
					<label for="wpbr-control-timestamp"><?php esc_html_e( 'Review Date', 'wp-business-reviews' ); ?></label>
					<span class="wpbr-field-description"><?php esc_html_e( 'Enter the date of the review.', 'wp-business-reviews' ); ?></span>
				</p>
				<div class="wpbr-date">
					<span class="dashicons dashicons-calendar-alt"></span>
					<input id="wpbr-control-datepicker" type="text" name="wpbr_datepicker" class="js-wpbr-datepicker" value="<?php echo $date_picker_date; ?>">
					<input id="wpbr-control-timestamp" type="hidden" name="wpbr_review[components][custom_timestamp]" class="js-wpbr-timestamp" value="<?php echo $timestamp; ?>">
				</div>

				<script>
					// Initialize datepicker.
					jQuery( document ).ready( function () {
						jQuery( '.js-wpbr-datepicker' ).datepicker({
							altField: '.js-wpbr-timestamp',
							altFormat: 'yy-mm-dd'
						});

						var wpbrDatePicker = jQuery( 'body > #ui-datepicker-div' );

						// Wrap the datepicker (only if it hasn't already been wrapped).
						if ( jQuery( wpbrDatePicker ).length ) {
							wpbrDatePicker.wrap( '<div class="wpbr-ui-datepicker">' );
						}
					} );
				</script>
			<?php else : ?>
				<p class="wpbr-label">
					<label for="wpbr-control-timestamp"><?php esc_html_e( 'Review Date', 'wp-business-reviews' ); ?></label>
					<span class="wpbr-field-description"><?php esc_html_e( 'This review date cannot be changed because the review was delivered from the platform API.', 'wp-business-reviews' ); ?></span>
				</p>
				<div class="wpbr-date">
					<span class="dashicons dashicons-calendar-alt"></span>
					<input id="wpbr-control-timestamp" type="text" name="wpbr_review[components][locked_timestamp]" value="<?php echo $date_picker_date; ?>" disabled>
				</div>
			<?php endif; ?>
		</div>

		<div id="wpbr-field-review-url" class="wpbr-field-wrap">
			<p class="wpbr-label">
				<label for="wpbr-control-review-url"><?php esc_html_e( 'Review URL', 'wp-business-reviews' ); ?></label>
				<span class="wpbr-field-description"><?php esc_html_e( 'Links to the URL of the full review if it exists on another website. This URL may be used if the full review is truncated.', 'wp-business-reviews' ); ?></span>
			</p>
			<div class="wpbr-url">
				<span class="dashicons dashicons-admin-site"></span>
				<input id="wpbr-control-review-url" type="url" name="wpbr_review[components][review_url]" placeholder="https://example.com" value="<?php echo esc_attr( $review_url ); ?>">
			</div>
		</div>

		<div id="wpbr-field-content" class="wpbr-field-wrap">
			<p class="wpbr-label">
				<label for="wpbr-control-content"><?php esc_html_e( 'Review Content', 'wp-business-reviews' ); ?></label>
			</p>
			<textarea id="wpbr-control-content" class="wpbr-textarea" name="wpbr_review[components][content]" rows="4" placeholder="<?php esc_html_e( 'Review content goes here...', 'wp-business-reviews' ); ?>"><?php echo $review_content; ?></textarea>
		</div>
	<?php
	}

	/**
	 * Renders the review meta box.
	 *
	 * @since 1.1.0
	 *
	 * @param WP_Post $post The post object.
	 */
	public function render_review_meta_meta_box( $post ) {
		$review_sources   = $this->review_source_deserializer->query_review_sources(
			array(
				'posts_per_page' => 50,
			)
		);
		$platforms        = $this->platform_manager->get_active_platforms();
		$current_platform = $this->review ? $this->review->get_platform(): '';
		unset( $platforms['review_tag'] )
		?>

		<div id="wpbr-field-platform" class="wpbr-field-wrap">
			<p class="wpbr-label">
				<label for="wpbr-control-platform">
					<?php esc_html_e( 'Platform', 'wp-business-reviews' ); ?>
				</label>
				<span class="wpbr-field-description"><?php esc_html_e( 'Select a platform to determine how the review is displayed and organized.', 'wp-business-reviews' ); ?></span>
			</p>
			<?php

			echo '<select id="wpbr-control-platform" class="js-wpbr-control-platform" name="wpbr_review[platform]">';
			echo '<option value="custom">' . __( 'Custom', 'wp-business-reviews' ) . '</option>';

			foreach ( $platforms as $platform => $platform_name ) {
				printf(
					'<option value="%1$s" %2$s>%3$s</option>',
					esc_attr( $platform ),
					selected( $platform, $current_platform ),
					esc_html( $platform_name )
				);
			}

			echo '</select>';
			?>
		</div>

		<?php if ( $review_sources ) : ?>
			<div id="wpbr-field-post-parent" class="wpbr-field-wrap wpbr-u-hidden">
				<p class="wpbr-label">
					<label for="wpbr-control-post-parent">
						<?php esc_html_e( 'Review Source', 'wp-business-reviews' ); ?>
					</label>
					<span class="wpbr-field-description"><?php esc_html_e( 'Select a review source to include this review in existing collections.', 'wp-business-reviews' ); ?></span>
				</p>
				<select id="wpbr-control-post-parent" class="js-wpbr-control-post-parent" name="wpbr_review[post_parent]">
					<option value=''><?php echo esc_html( '-- Select --', 'wp-business-reviews' ); ?></option>

					<?php
					$post_parent_choices = array();
					$post_parent           = 0;

					if ( ! empty( $this->review ) ) {
						$post_parent = $this->review->get_post_parent();
					}

					foreach ( $review_sources as $review_source ) {
						$address  = '';
						$location = $review_source->get_component( 'location' );

						if ( $location instanceof Location ) {
							$address = $location->get_formatted_address();
						}

						// Add data to be consumed by Choices.js
						$post_parent_choices[] = array(
							'value' => $review_source->get_post_id(),
							'label' => $review_source->get_component( 'name' ),
							'selected' => selected( $post_parent, $review_source->get_post_id() ),
							'customProperties' => array(
								'platform' => $review_source->get_platform(),
								'address'  => $address,
							),
						);

						// Add fallback options in case Choices.js fails.
						printf(
							'<option value="%1$s" %2$s>%3$s</option>',
							esc_attr( $review_source->get_post_id() ),
							selected( $post_parent, $review_source->get_post_id() ),
							esc_html( $review_source->get_component( 'name' ) )
						);
					}
					?>

				</select>
			</div>
			<?php wp_localize_script( 'wpbr-admin-main-script', 'wpbrPostParents', $post_parent_choices ); ?>
		<?php endif; ?>
	<?php
	}

	/**
	 * Renders the image selector.
	 *
	 * - If a custom image has been set by the user, it is rendered.
	 * - If a custom image does not exist, the API image is rendered.
	 * - If no image exists, a placeholder image is rendered.
	 *
	 * @since 1.1.0
	 *
	 * @param WP_Post $post The post object.
	 */
	public function render_image_selector( $post ) {
		$image = $custom_image = $selected_image = '';
		$set_button_text      = __( 'Set Image', 'wp-business-reviews' );
		$reset_button_text    = __( 'Reset', 'wp-business-reviews' );
		$platform_slug        = ! empty( $this->review ) ? $this->review->get_platform_slug() : 'custom';
		$image_selector_class = 'wpbr-image-selector js-wpbr-image-selector wpbr-theme-' . $platform_slug;
		$set_button_class     = 'wpbr-image-selector__button button js-wpbr-set-image-button';
		$reset_button_class   = 'wpbr-image-selector__button button js-wpbr-reset-image-button';
		$image_class          = 'wpbr-image-selector__image wpbr-image-selector__image js-wpbr-reviewer-image';
		$custom_image_class   = 'wpbr-image-selector__image wpbr-image-selector__image--custom js-wpbr-reviewer-image-custom';
		$placeholder_class    = 'wpbr-image-selector__image js-wpbr-placeholder';

		// Determine whether to display image, custom image, or placeholder.
		if ( ! empty( $this->review ) ) {
			$image              = $this->review->components['reviewer_image'];
			$custom_image       = $this->review->components['reviewer_image_custom'];

			if ( ! empty( $custom_image ) ) {
				$image_class .= ' wpbr-u-hidden';
				$placeholder_class .= ' wpbr-u-hidden';
			} else {
				$set_button_class .= ' wpbr-image-selector__button--rounded';
				$reset_button_class .= ' wpbr-u-hidden';
			}
		}
		?>

		<div class="<?php echo esc_attr( $image_selector_class ); ?>">
			<?php if( ! empty( $image ) ) : ?>
				<img class="<?php echo esc_attr( $image_class ); ?>" src="<?php echo esc_attr( $image ); ?>">
			<?php else : ?>
				<div class="<?php echo esc_attr( $placeholder_class ); ?>"><i class="fas wpbr-icon wpbr-fw wpbr-user-circle"></i></div>
			<?php endif; ?>

			<?php if ( ! empty( $custom_image ) ) : ?>
				<?php echo wp_get_attachment_image( $custom_image, array( 120, 120 ), false, array( 'class' => $custom_image_class ) ); ?>
			<?php endif; ?>

			<div class="wpbr-image-selector__button-group button-group">
				<button class="<?php echo esc_attr( $set_button_class ); ?>" type="button">
					<i class="fas wpbr-icon wpbr-fw wpbr-file-image"></i>
					<?php echo esc_html( $set_button_text ); ?>
				</button>
				<button class="<?php echo esc_attr( $reset_button_class ); ?>" type="button">
					<i class="fas wpbr-icon wpbr-fw wpbr-undo-alt"></i>
					<?php echo esc_html( $reset_button_text ); ?>
				</button>
			</div>

			<input id="wpbr-control-reviewer-image" type="hidden" name="wpbr_review[components][reviewer_image]" value="<?php echo esc_attr( $image ); ?>">
			<input id="wpbr-control-reviewer-image-custom" type="hidden" name="wpbr_review[components][reviewer_image_custom]" value="<?php echo esc_attr( $custom_image ); ?>">
		</div>
	<?php
	}

	/**
	 * Saves the review data.
	 *
	 * @since 0.1.0
	 *
	 * @param int $post_id Post ID.
	 * @return null|int
	 */
	public function save( $post_id ) {
		// Bail early if revision or autosave.
		if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) {
			return;
		}

		// Ensure review data has been set and is an array.
		if ( ! isset( $_POST['wpbr_review'] ) || ! is_array( $_POST['wpbr_review'] ) ) {
			return;
		}

		// Unhook this function to prevent infinite loop.
		remove_action( 'save_post_wpbr_review', array( $this, 'save' ) );

		$this->review_serializer->save_from_post_array();

		// Re-hook this function.
		add_action( 'save_post_wpbr_review', array( $this, 'save' ) );

		return $post_id;
	}
}
