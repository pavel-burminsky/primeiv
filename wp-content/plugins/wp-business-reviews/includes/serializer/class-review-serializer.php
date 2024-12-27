<?php
/**
 * Defines the Review_Serializer class
 *
 * @link https://wpbusinessreviews.com
 *
 * @package WP_Business_Reviews\Includes\Serializer
 * @since 0.1.0
 */

namespace WP_Business_Reviews\Includes\Serializer;

use \DateTime;

/**
 * Saves reviews to the database.
 *
 * @since 0.1.0
 */
class Review_Serializer extends Post_Serializer {
	/**
	 * The post type being saved.
	 *
	 * @since 0.1.0
	 * @var string $post_type
	 */
	protected $post_type = 'wpbr_review';

	/**
	 * The WordPress date format.
	 *
	 * @since 1.1.0
	 * @var string $date_format
	 */
	protected $date_format;

	/**
	 * Instantiates the Review_Serializer object.
	 *
	 * @since 1.1.0
	 *
	 * @param Deserializer $deserializer Retriever of collections.
	 */
	public function __construct( $date_format ) {
		$this->date_format = $date_format;
	}

	/**
	* Registers functionality with WordPress hooks.
	 *
	 * @since 0.1.0
	 */
	public function register() {
		add_action( 'wpbr_review_source_determine_post_id', array( $this, 'set_post_parent' ) );
		add_action( 'admin_post_wpbr_collection_save', array( $this, 'save_from_post_json' ), 20 );
	}

	/**
	 * Prepares the post data in a ready-to-save format.
	 *
	 * @since 0.1.0
	 *
	 * @param array $raw_data Raw, unstructured post data.
	 * @return array Array of elements that make up a post.
	 */
	public function prepare_post_array( array $raw_data ) {
		$platform      = '';
		$rating        = 0;
		$rating_normal = 0;

		// Define the raw data ($r) from which a post will be created.
		$r = $raw_data;

		// Define the post array ($p) that will hold all post elements.
		$p = array(
			'post_type'   => $this->post_type,
			'post_status' => 'publish',
		);

		// Ensure attributes are re-evaluated during each save.
		$p['tax_input']['wpbr_attribute'] = array();

		// Set post ID.
		if ( isset( $r['post_id'] ) ) {
			$p['ID'] = absint( $r['post_id'] );

			// Do not update existing review unless manually editing single review.
			if ( 'save_post_wpbr_review' !== current_action() && 0 < $p['ID'] ) {
				return array();
			}
		}

		// Set post parent.
		if ( ! empty( $this->post_parent ) ) {
			$p['post_parent'] = $this->post_parent;
		} elseif ( isset( $r['post_parent'] ) ) {
			$p['post_parent'] = $this->clean( $r['post_parent'] );
		}

		// Set timestamp.
		if ( ! empty( $r['components']['timestamp'] ) ) {
			$timestamp = $this->clean( $r['components']['timestamp'] );
			$p['meta_input']["{$this->prefix}timestamp"] = $timestamp;
			unset( $r['components']['timestamp'] );
		} elseif ( ! empty( $r['components']['custom_timestamp'] ) ) {
			// Timestamps from custom reviews need normalized using WP date format.
			$date_time = DateTime::createFromFormat(
				'Y-m-d',
				$this->clean( $r['components']['custom_timestamp'] )
			);
			$timestamp = $date_time->format( 'Y-m-d H:i:s' );
			$p['meta_input']["{$this->prefix}timestamp"] = $timestamp;
			unset( $r['components']['custom_timestamp'] );
		}

		// Process content before title in case it's needed to generate title.
		if ( ! empty( $r['components']['content'] ) ) {
			$p['post_content'] = $this->clean_multiline( $r['components']['content'] );
		} else {
			// Content is empty, so add 'blank' attribute for filtering.
			$p['tax_input']['wpbr_attribute'][] = 'blank';
		}

		unset( $r['components']['content'] );

		// Set title from raw data, post title field, or content.
		if ( isset( $r['title'] ) ) {
			$p['post_title'] = $this->clean( $r['title'] );
		} else if ( ! empty( $_POST['post_title'] ) ) {
			// Use title from title field
			$p['post_title'] = $this->clean( $_POST['post_title'] );
		} else if ( isset( $p['post_content'] ) ) {
			// Generate post title from content.
			$p['post_title'] = $this->generate_title_from_excerpt( $p['post_content'] );
		}

		// Set platform.
		if ( isset( $r['platform'] ) ) {
			$platform = $this->clean( $r['platform'] );

			// Convert platform to slug if ID provided.
			if ( absint( $platform ) ) {
				$term_obj = get_term_by( 'id', $platform, 'wpbr_platform' );
				$platform = $term_obj ? $term_obj->slug : '';
			}

			$p['tax_input']['wpbr_platform'] = $platform;
		}

		// Use float rating or fall back to star rating or recommendation.
		if (
			isset( $r['wpbr_review_type'], $r['float_rating'] )
			&& 'float_rating' === $r['wpbr_review_type']
		) {
			$rating = sprintf( '%.1f', floatval( $r['float_rating'] ) );
		} elseif ( isset( $r['components']['rating'] ) ) {
			$rating = $this->clean( $r['components']['rating'] );
		}

		unset( $r['components']['rating'] );

		// Normalize rating.
		$rating_normal = $this->normalize_rating(
			$rating,
			$platform
		);

		// Set rating and normalized rating.
		$p['meta_input']["{$this->prefix}rating"]        = $rating;
		$p['meta_input']["{$this->prefix}rating_normal"] = $rating_normal;

		// Denote recommendation as a taxonomy term for filtering.
		if ( ! is_numeric( $rating ) ) {
			$p['tax_input']['wpbr_attribute'][] = 'recommendation';
		}

		// Store review source ID as post meta.
		if ( isset( $r['review_source_id'] ) ) {
			$p['meta_input']["{$this->prefix}review_source_id"] = $this->clean( $r['review_source_id'] );
		}

		// Do not store formatted date as it is generated dynamically.
		unset( $r['components']['formatted_date'] );

		// Store all remaining components as post meta.
		if ( isset( $r['components'] ) ) {
			foreach ( $r['components'] as $key => $value ) {
				if ( null !== $value ) {
					$p['meta_input']["{$this->prefix}{$key}"] = $this->clean( $value );
				}
			}
		}

		return $p;
	}

	/**
	 * Generates a truncated title from a string of content.
	 *
	 * @since 0.1.0
	 *
	 * @param string  $content The review content to trim.
	 * @param integer $length  Maximum number of characters in the review title.
	 * @return string The truncated title.
	 */
	protected function generate_title_from_excerpt( $content, $length = 60 ) {
		/**
		 * Filters the number of characters in the review title.
		 *
		 * @since 0.1.0
		 *
		 * @param int $length Maximum number of characters in the review title.
		 */
		$length = apply_filters( 'wpbr_review_title_length', $length );

		if ( $length >= strlen( $content ) ) {
			return $content;
		}

		$title = mb_substr( $content, 0, strrpos( mb_substr( $content, 0, $length - 3 ), ' ' ) );
		$last_char = mb_substr( $title, -1 );

		if ( '.' === $last_char ) {
			$title .= '..';
		} else {
			$title .= '...';
		}

		return $title;
	}
}
