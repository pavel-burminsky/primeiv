<?php
/**
 * Defines the Admin_Review_Columns class
 *
 * @package WP_Business_Reviews\Includes\Admin
 * @since   0.1.0
 */

namespace WP_Business_Reviews\Includes\Admin;

use WP_Business_Reviews\Includes\Deserializer\Review_Deserializer as Deserializer;
use WP_Business_Reviews\Includes\Platform_Manager;

/**
 * Customizes the WP_List_Table columns for reviews.
 *
 * @since 0.1.0
 */
class Admin_Review_Columns {
	/**
	 * Review deserializer.
	 *
	 * @since 0.1.0
	 * @var Deserializer $deserializer
	 */
	private $deserializer;

	/**
	 * Instantiates the Admin_Review_Columns object.
	 *
	 * @since 0.1.0
	 *
	 * @param Deserializer $deserializer Retriever of collections.
	 */
	public function __construct( Deserializer $deserializer ) {
		$this->deserializer = $deserializer;
	}

	/**
	 * Registers functionality with WordPress hooks.
	 *
	 * @since 0.1.0
	 */
	public function register() {
		add_filter( 'disable_months_dropdown', array( $this, 'disable_months_dropdown' ), 10, 2 );
		add_filter( 'manage_edit-wpbr_review_columns', array( $this, 'get_columns' ) );
		add_filter( 'manage_edit-wpbr_review_sortable_columns', array( $this, 'get_sortable_columns' ) );
		add_filter( 'request', array( $this, 'sort' ) );
		add_filter( 'get_the_excerpt', array( $this, 'get_review_excerpt' ) );
		add_filter( 'post_row_actions', array( $this, 'modify_post_row_actions' ), 10, 2 );
		add_action( 'manage_wpbr_review_posts_custom_column', array( $this, 'render' ), 10, 2 );
		add_action( 'restrict_manage_posts', array( $this, 'render_platform_filter' ) );
	}

	/**
	 * Disables the date dropdown for reviews.
	 *
	 * @param bool   $disable   Whether to disable the drop-down. Default false.
	 * @param string $post_type The post type.
	 * @return bool Whether to disable the drop-down.
	 */
	public function disable_months_dropdown( $disable, $post_type ) {
		if ( 'wpbr_review' === $post_type ) {
			$disable = true;
		}

		return $disable;
	}

	/**
	 * Retrieves the custom columns in the order in which they appear.
	 *
	 * @since 0.1.0
	 *
	 * @param array $columns An array of column headers. Default empty.
	 * @return array Filtered array of columns.
	 */
	public function get_columns( $columns ) {
		$columns                  =  array(
			'cb'                       => '<input type = "checkbox"/>',
			'wpbr_reviewer_image'      => __( 'Image', 'wp-business-reviews' ),
			'title'                    => __( 'Title', 'wp-business-reviews' ),
			'wpbr_reviewer_name'       => __( 'Reviewer', 'wp-business-reviews' ),
			'wpbr_rating'              => __( 'Rating', 'wp-business-reviews' ),
			'taxonomy-wpbr_platform'   => __( 'Platform', 'wp-business-reviews' ),
			'taxonomy-wpbr_review_tag' => __( 'Tags', 'wp-business-reviews' ),
			'wpbr_timestamp'           => __( 'Date', 'wp-business-reviews' ),
			'wpbr_shortcode'           => __( 'Shortcode', 'wp-business-reviews' ),
		);

		return $columns;
	}

	/**
	 * Retrieves the sortable columns for the list table.
	 *
	 * @since 0.1.0
	 *
	 * @param array $columns Array of sortable columns.
	 * @return array $columns Array of sortable columns.
	 */
	public function get_sortable_columns( $columns ) {
		$columns['wpbr_reviewer_name'] = 'wpbr_reviewer_name';
		$columns['wpbr_rating']        = array( 'wpbr_rating', true );
		$columns['wpbr_timestamp']     = array( 'wpbr_timestamp', true );

		return $columns;
	}

	/**
	 * Renders the columns.
	 *
	 * @since 1.0
	 *
	 * @param string $column_name Column name.
	 * @param int    $post_id     Unique ID of the review post.
	 */
	public function render( $column_name, $post_id ) {
		$args = array();

		if ( isset( $_GET['post_status'] ) ) {
			$post_status = sanitize_text_field( wp_unslash( $_GET['post_status'] ) );

			if ( 'trash' === $post_status ) {
				$args['post_status'] = array( 'trash' );
			}
		}

		$review                = $this->deserializer->get_review( $post_id, $args );
		$reviewer_image        = $review->get_component( 'reviewer_image' );
		$reviewer_image_custom = $review->get_component( 'reviewer_image_custom' );
		$reviewer_name         = $review->get_component( 'reviewer_name' );
		$timestamp             = $review->get_component( 'timestamp' );

		switch ( $column_name ) {
			case 'wpbr_reviewer_image':
				if (
					empty( $reviewer_image )
					&& empty( $reviewer_image_custom )
				) {
					echo '<div class="wpbr-admin-column-thumbnail"><i class="fas wpbr-icon wpbr-fw wpbr-user-circle"></i></div>';
					break;
				}

				$image_url   = $reviewer_image;
				$image_class = 'wpbr-admin-column-thumbnail wpbr-theme-' . esc_attr( str_replace( '_', '-', $review->platform ) );

				if ( ! empty( $reviewer_image_custom ) ) {
					$image_url = $review->get_custom_reviewer_image_url();
					$image_class .= ' wpbr-admin-column-thumbnail--custom';
				}

				echo '<img class="' . esc_attr( $image_class ) . '" src="' . esc_attr( $image_url ) . '" alt="">';
				break;

			case 'wpbr_reviewer_name':
				if ( isset( $reviewer_name ) ) {
					echo esc_html( $reviewer_name );
				}
				break;

			case 'wpbr_rating':
				$this->render_rating( $review );
				break;

			case 'wpbr_timestamp':
				echo esc_html__( 'Reviewed', 'wp-business-reviews' );
				echo '<br>';
				echo '<abbr title="' . date_i18n( 'Y/m/d g:i:s a' , strtotime( $timestamp ) ) . '">' . date_i18n( 'Y/m/d' , strtotime( $timestamp ) ) . '</abbr>';
				break;

			case 'wpbr_shortcode':
                $shortcode = '[wpbr_review id="%s" style="light" format="review_gallery" max_columns="1" max_characters="280" line_breaks="disabled" reviewer_image="enabled" reviewer_name="enabled" rating="enabled" recommendation="enabled" timestamp="enabled" content="enabled" platform_icon="enabled"]';
				$shortcode = sprintf( $shortcode, $review->post_id );
				printf(
					'<button type="button" class="%1$s" aria-label="%2$s" data-wpbr-shortcode="%3$s"><i class="%4$s"></i> %5$s</button>',
					'button wpbr-tooltip wpbr-tooltip--top js-wpbr-shortcode-button',
					esc_attr( $shortcode ),
					esc_attr( $shortcode ),
					'fas wpbr-icon wpbr-fw wpbr-copy',
					esc_html__( 'Copy Shortcode', 'wp-business-reviews' )
				);
				break;
		}
	}

	/**
	 * Sorts columns in the review list table
	 *
	 * @since 0.1.0
	 *
	 * @param array $query_vars The array of requested query variables.
	 * @return array $query_vars The updated array of requested query variables.
	 */
	function sort( $query_vars ) {
		if (
			! isset( $query_vars['post_type'] )
			|| 'wpbr_review' !== $query_vars['post_type']
		) {
			return $query_vars;
		}

		// Sort reviews by review date by default.
		if ( ! isset( $query_vars['orderby'] ) ) {
			$query_vars['orderby'] = 'wpbr_timestamp';
			$query_vars['order']   = 'desc';
			$_GET['orderby']       = 'wpbr_timestamp';
			$_GET['order']         = 'desc';
		}

		$sort_vars = array();

		switch ( $query_vars['orderby'] ) {
			case 'wpbr_reviewer_name':
				$sort_vars = array(
					'meta_key' => 'wpbr_reviewer_name',
					'orderby'  => 'meta_value',
				);
				break;

			case 'wpbr_rating':
				$sort_vars = array(
					'orderby' => array(
						'rating'      => $query_vars['order'],
						'review_date' => 'desc',
					),
					'meta_query' => array(
						'rating' => array(
							'key'     => 'wpbr_rating_normal',
							'compare' => 'EXISTS',
							'type'    => 'NUMERIC'
						),
						'review_date' => array(
							'key'     => 'wpbr_timestamp',
							'compare' => 'EXISTS',
						)
					),
				);
				break;

			case 'wpbr_timestamp':
				$sort_vars = array(
					'meta_key' => 'wpbr_timestamp',
					'orderby'  => 'meta_value',
				);
				break;
		}

		if ( ! empty( $sort_vars ) ) {
			$query_vars = array_merge( $query_vars, $sort_vars );
		}

		return $query_vars;
	}

	/**
	 * Generates a truncated excerpt from a string of content.
	 *
	 * @since 0.1.0
	 *
	 * @param string  $content The review content to trim.
	 * @param integer $length  Maximum number of characters in the excerpt.
	 * @return string The truncated excerpt.
	 */
	public function get_review_excerpt( $content, $length = 240 ) {
		/**
		 * Filters the number of characters in the excerpt.
		 *
		 * @since 0.1.0
		 *
		 * @param int $length Maximum number of characters in the excerpt.
		 */
		$length = apply_filters( 'wpbr_admin_review_excerpt_length', $length );

		if ( $length >= strlen( $content ) ) {
			return $content;
		}

		$length++;

		$excerpt = mb_substr( $content, 0, strrpos( mb_substr( $content, 0, $length - 3 ), ' ' ) );
		$last_char = mb_substr( $excerpt, -1 );

		if ( '.' === $last_char ) {
			$excerpt .= '..';
		} else {
			$excerpt .= '...';
		}

		return $excerpt;
	}

	/**
	 * Modifies the row of actions in the title column.
	 *
	 * @since 0.1.0
	 *
	 * @param array   $actions An array of row action links.
	 * @param WP_Post $post    The post object.
	 * @return array An updated array of row action links.
	 */
	public function modify_post_row_actions( $actions, $post ) {
		if ( $post->post_type === "wpbr_review" ) {
			$modified_actions = array(
				'post-id' => '<span class="wpbr-admin-column-action">ID: ' . $post->ID . '</span>',
			);
			$actions = $modified_actions + $actions;
		}

		return $actions;
	}

	/**
	 * Displays a platforms drop-down for filtering the list table.
	 *
	 * @since 0.1.0
	 *
	 * @param string $post_type Post type slug.
	 */
	public function render_platform_filter( $post_type ) {
		if ( 'wpbr_review' !== $post_type ) {
			return;
		}

		$current_platform = '';

		if ( isset( $_GET['wpbr_platform'] ) ) {
			$current_platform = sanitize_text_field( wp_unslash( $_GET['wpbr_platform'] ) );
		}

		$platform = get_taxonomy( 'wpbr_platform' );

		$terms = get_terms( array(
			'taxonomy'   => 'wpbr_platform',
		) );

		echo '<label class="screen-reader-text" for="wpbr-platform-filter">' . __( 'Filter by platform', 'wp-business-reviews' ) . '</label>';
		echo '<select id="wpbr-platform-filter" name="wpbr_platform">';
			echo '<option value="">' . esc_html( $platform->labels->all_items ) . '</option>';
			foreach( $terms as $term ) {
				echo '<option '
					. 'value="' . esc_html( $term->slug ) . '"'
					. selected( $term->slug, $current_platform ) . '>'
					. esc_html( $term->name )
					. '</option>';
			}
		echo '</select>';
	}

	/**
	 * Renders the rating of a review.
	 *
	 * Usually reviews are rendered by JavaScript, however this method is
	 * helpful for the specific use case of displaying ratings within the WP
	 * List Table.
	 *
	 * @since 1.3.0
	 *
	 * @param Review $review A review object.
	 */
	protected function render_rating( $review ) {
		$platform_slug = $review->get_platform_slug();
		$rating        = $review->get_component( 'rating' );

		echo '<div class="wpbr-theme-' . esc_attr( $platform_slug ) . '">';
			if ( empty( $rating ) ) {
				esc_html_e( 'Unrated', 'wp-business-reviews' );
			} elseif ( 'zomato' === $platform_slug ) {
				// Render Zomato rating.
				printf(
					'<span class="wpbr-zomato-rating wpbr-zomato-rating--large"><span class="wpbr-zomato-rating__number wpbr-zomato-rating__number--level-%2$s">%1$s</span></span>',
					esc_html( $rating ),
					$this->calculate_zomato_level( $rating )
				);
			} elseif ( is_numeric( $rating ) ) {
				 if ( is_float( $rating + 0 ) ) {
					// Render float rating.
					echo esc_html( $rating );
				 } else {
					 // Render star rating.
					 printf(
						 '<span class="wpbr-stars wpbr-stars--%1$s"></span>',
						 esc_attr( $rating )
					 );
				 }
			} elseif ( 'positive' === $rating || 'negative' === $rating ) {
				// Render recommendation.
				printf(
					'<div class="wpbr-reco"><i class="wpbr-reco__icon wpbr-reco__icon--%1$s"></i><span class="wpbr-reco__text">%2$s</span></div>',
					esc_attr( $rating ),
					'positive' === $rating ? __( 'Positive', 'wp-business-reviews' ) : __( 'Negative', 'wp-business-reviews' )
				);
			}
		echo '</div>';
	}

	/**
	 * Calculates the Zomato level used to color ratings.
	 *
	 * Zomato provides 9 color levels where each level applies to half of a
	 * rating point. For example, 5.0 is level 9, 4.5 is level 8, and so on.
	 *
	 * @since 1.3.0
	 *
	 * @param float $rating Numerical rating with a single decimal place.
	 */
	protected function calculate_zomato_level( $rating ) {
		return ( floor( $rating * 2 ) / 2 ) * 2 - 1;
	}
}
