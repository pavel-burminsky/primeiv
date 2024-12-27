<?php
/**
 * Defines the core plugin class
 *
 * @link https://wpbusinessreviews.com
 *
 * @package WP_Business_Reviews\Includes
 * @since 0.1.0
 */

namespace WP_Business_Reviews\Includes;

use WP_Business_Reviews\Includes\Admin\Admin_Review_Editor;
use WP_Business_Reviews\Includes\Field\Parser\Plugin_Settings_Field_Parser;
use WP_Business_Reviews\Includes\Serializer\Option_Serializer;
use WP_Business_Reviews\Includes\Deserializer\Option_Deserializer;
use WP_Business_Reviews\Includes\Admin\Admin_Menu;
use WP_Business_Reviews\Includes\Admin\Admin_Banner;
use WP_Business_Reviews\Includes\Admin\Activation_Banner;
use WP_Business_Reviews\Includes\Admin\Admin_Collection_Columns;
use WP_Business_Reviews\Includes\Admin\Admin_Review_Columns;
use WP_Business_Reviews\Includes\Admin\Admin_Footer;
use WP_Business_Reviews\Includes\Admin\Blank_Slate;
use WP_Business_Reviews\Includes\Admin\System_Info;
use WP_Business_Reviews\Includes\Admin\Database_Updater;
use WP_Business_Reviews\Includes\Admin\License;
use WP_Business_Reviews\Includes\Config;
use WP_Business_Reviews\Includes\Request\Request_Factory;
use WP_Business_Reviews\Includes\Facebook_Page_Manager;
use WP_Business_Reviews\Includes\Platform_Manager;
use WP_Business_Reviews\Includes\Platform_Selector;
use WP_Business_Reviews\Includes\Field\Parser\Builder_Field_Parser;
use WP_Business_Reviews\Includes\Request\Request_Delegator;
use WP_Business_Reviews\Includes\Request\Response_Normalizer\Response_Normalizer_Factory;
use WP_Business_Reviews\Includes\Serializer\Review_Serializer;
use WP_Business_Reviews\Includes\Serializer\Review_Source_Serializer;
use WP_Business_Reviews\Includes\Serializer\Collection_Serializer;
use WP_Business_Reviews\Includes\Deserializer\Review_Deserializer;
use WP_Business_Reviews\Includes\Deserializer\Collection_Deserializer;
use WP_Business_Reviews\Includes\Shortcode\Collection_Shortcode;
use WP_Business_Reviews\Includes\Shortcode\Review_Shortcode;
use WP_Business_Reviews\Includes\Widget\Collection_Widget;
use WP_Business_Reviews\Includes\Builder\Builder;
use WP_Business_Reviews\Includes\Builder\Builder_Inspector;
use WP_Business_Reviews\Includes\Builder\Builder_Preview;
use WP_Business_Reviews\Includes\Builder\Builder_Table;
use WP_Business_Reviews\Includes\Deserializer\Review_Source_Deserializer;
use WP_Business_Reviews\Includes\Admin\Admin_Help;
use WP_Business_Reviews\Includes\Admin\Admin_Notices;
use WP_Business_Reviews\Includes\Refresher\Review_Refresher;
use WP_Business_Reviews\Includes\Refresher\Auto_Review_Refresher;
use WP_Business_Reviews\Includes\Refresher\Facebook_Image_Refresher;
use WP_Business_Reviews\Includes\Blocks\Collection\Block as Collection_Block;
use WP_Business_Reviews\Includes\Blocks\Review\Block as Review_Block;
use WP_Business_Reviews\Includes\Routes\Get_Collection_Route;
use WP_Business_Reviews\Includes\Routes\Get_Review_Route;

/**
 * Loads and registers plugin functionality through WordPress hooks.
 *
 * @since 0.1.0
 */
final class Plugin {
	/**
	 * Registers functionality with WordPress hooks.
	 *
	 * @since 0.1.0
	 */
	public function register() {
		// Handle plugin activation and deactivation.
		register_activation_hook( WPBR_PLUGIN_FILE, array( $this, 'activate' ) );
		register_deactivation_hook( WPBR_PLUGIN_FILE, array( $this, 'deactivate' ) );

		// Register services used throughout the plugin.
		add_action( 'plugins_loaded', array( $this, 'register_services') );

		// Load text domain.
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

        // Gutenberg blocks
        if (function_exists('register_block_type')) {
            add_action('init', [ $this, 'register_blocks' ], 999);
            // Register wpbr block category
            add_action( 'block_categories_all', function($categories){
                return array_merge(
                    $categories,
                    [
                        [
                            'slug'  => 'wpbr',
                            'title' => __( 'WPBR Blocks', 'wp-business-reviews' ),
                        ],
                    ]
                );
            }, 10, 2 );
        }

        // Register REST routes
        add_action('rest_api_init', [ $this, 'register_rest_routes' ]);
	}

	/**
	 * Registers the individual services of the plugin.
	 *
	 * @since 0.1.0
	 */
	public function register_services() {
		// Register cron scheduler.
		$cron_scheduler = new Cron_Scheduler();
		$cron_scheduler->register();

		// Register post types.
		$post_types = new Post_Types();
		$post_types->register();

		// Register activator to handle updates of existing installs.
		$activator = new Activator( $cron_scheduler, $post_types );
		$activator->register();

		// Register database updater.
		$database_updater = new Database_Updater();
		$database_updater->register();

		// Register assets.
		$assets = new Assets( WPBR_ASSETS_URL, WPBR_VERSION );
		$assets->register();

		// Register deserializers to retrieve data.
		$option_deserializer        = new Option_Deserializer();
		$review_deserializer        = new Review_Deserializer( new \WP_Query() );
		$review_source_deserializer = new Review_Source_Deserializer( new \WP_Query() );
		$collection_deserializer    = new Collection_Deserializer(
			new \WP_Query(),
			$review_source_deserializer,
			$review_deserializer
		);
		$review_deserializer->register();

		// Register serializers to save posts.
		$review_serializer = new Review_Serializer( get_option( 'date_format' ) );
		$review_serializer->register();

		$review_source_serializer = new Review_Source_Serializer();
		$review_source_serializer->register();

		$collection_serializer = new Collection_Serializer();
		$collection_serializer->register();

		// Register factories for handling remote API requests.
		$request_factory             = new Request_Factory( $option_deserializer );
		$response_normalizer_factory = new Response_Normalizer_Factory();

		// Register request delegator to handle review requests from any platform.
		$request_delegator = new Request_Delegator(
			$request_factory,
			$response_normalizer_factory,
			$review_deserializer
		);
		$request_delegator->register();

		// Register widgets.
		$collection_widget = new Collection_Widget( $collection_deserializer );
		$collection_widget->register();

		// Register shortcodes.
		$collection_shortcode = new Collection_Shortcode( $collection_deserializer );
		$review_shortcode     = new Review_Shortcode( $review_deserializer );
		$collection_shortcode->register();
		$review_shortcode->register();

		// Register review refresher.
		$review_refresher = new Review_Refresher(
			$request_delegator,
			$review_serializer
		);
		$review_refresher->register();

		// Register Facebook image refresher.
		$facebook_image_refresher = new Facebook_Image_Refresher(
			$option_deserializer->get( 'facebook_pages', array() )
		);
		$facebook_image_refresher->register();

		// Define how often reviews should be auto refreshed.
		$auto_refresh = $option_deserializer->get( 'auto_refresh', 'weekly' );

		// Register background refreshers if auto refresh is enabled.
		if ( 'disabled' !== $auto_refresh ) {
			$failed_platforms = $option_deserializer->get( 'failed_platforms' ) ?: array();

			// Register auto review refresher.
			$auto_review_refresher = new Auto_Review_Refresher(
				$auto_refresh,
				$review_refresher,
				$review_source_deserializer,
				$failed_platforms
			);
			$auto_review_refresher->register();
		}

		if ( is_admin() ) {
			// Register admin notices.
			$admin_notices = new Admin_Notices();
			$admin_notices->register();

			// Register admin help.
			$admin_help_config = new Config( WPBR_PLUGIN_DIR . 'config/config-admin-help.php' );
			$admin_help        = new Admin_Help( $admin_help_config );
			$admin_help->register();

			// Register the licensing class.
			$licensing = new License();
			$licensing->register();

			// Register serializers.
			$option_serializer   = new Option_Serializer();
			$option_serializer->register();

			// Register platform manager to manage active and connected platforms.
			$platform_manager = new Platform_Manager(
				$option_deserializer,
				$option_serializer,
				$request_factory
			);
			$platform_manager->register();

			// Register plugin settings.
			$plugin_settings_config       = new Config( WPBR_PLUGIN_DIR . 'config/config-plugin-settings.php' );
			$plugin_settings_field_parser = new Plugin_Settings_Field_Parser( $option_deserializer );
			$plugin_settings              = new Plugin_Settings(
				$plugin_settings_config,
				$plugin_settings_field_parser,
				$platform_manager
			);
			$plugin_settings->register();

			// Register platform selector.
			$platform_selector = new Platform_Selector( $platform_manager );
			$platform_selector->register();

			// Register Builder.
			$builder_table        = new Builder_Table( $collection_deserializer );
			$collection_config    = new Config( WPBR_PLUGIN_DIR . 'config/config-builder-settings.php' );
			$builder_field_parser = new Builder_Field_Parser();
			$builder_inspector    = new Builder_Inspector(
				$collection_config,
				$builder_field_parser
			);
			$builder_preview      = new Builder_Preview();
			$builder              = new Builder(
				$builder_inspector,
				$builder_preview,
				$collection_deserializer
			);
			$builder->register();

			// Register Facebook page manager to retrieve and update authenticated pages.
			$facebook_page_manager = new Facebook_Page_Manager(
				$option_deserializer->get( 'facebook_pages' ) ?: array(),
				$option_serializer,
				$request_factory->create( 'facebook' )
			);
			$facebook_page_manager->register();

			// Register admin pages.
			$admin_pages_config = new Config( WPBR_PLUGIN_DIR . 'config/config-admin-pages.php' );
			$admin_menu         = new Admin_Menu( $admin_pages_config );
			$admin_menu->register();

			// Register the admin banner that appears at the top of each plugin page.
			$admin_header = new Admin_Banner();
			$admin_header->register();

			// Register the activation banner that appears on the Plugins page.
			$admin_header = new Activation_Banner();
			$admin_header->register();

			// Register admin collection columns.
			$admin_collection_columns = new Admin_Collection_Columns( $collection_deserializer );
			$admin_collection_columns->register();

			// Register admin review columns.
			$admin_reviews_columns = new Admin_Review_Columns( $review_deserializer );
			$admin_reviews_columns->register();

			// Register admin review editor for single reviews.
			$admin_review_editor = new Admin_Review_Editor(
				$review_deserializer,
				$review_source_deserializer,
				$review_serializer,
				$platform_manager
			);
			$admin_review_editor->register();

			// Register admin footer customizations.
			$admin_footer = new Admin_Footer();
			$admin_footer->register();

			// Register blank slate that appears when no posts exist.
			$blank_slate = new Blank_Slate(
				array(
					'wpbr_collection',
					'wpbr_review'
				)
			);
			$blank_slate->register();

			// Register system info.
			$system_info = new System_Info( $platform_manager );
			$system_info->register();
		}
	}

    /**
     * @since 1.6.0
     */
    public function register_blocks() {
        $collection_block = new Collection_Block();
        $collection_block->register();

        $review_block = new Review_Block();
        $review_block->register();
    }

    /**
     * @since 1.6.0
     */
    public function register_rest_routes() {
        $collection_route = new Get_Collection_Route();
        $collection_route->registerRoute();

        $review_route = new Get_Review_Route();
        $review_route->registerRoute();
    }

	/**
	 * Loads the plugin's translated strings.
	 *
	 * @since 1.2.1
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain(
			'wp-business-reviews',
			false,
			dirname( plugin_basename( WPBR_PLUGIN_FILE ) ) . '/languages/'
		);
	}

	/**
	 * Handles activation procedures during installation and updates.
	 *
	 * @since 1.2.0 Maybe update database version.
	 * @since 0.1.0
	 *
	 * @param bool $network_wide Optional. Whether the plugin is being enabled on
	 *                           all network sites or a single site. Default false.
	 */
	public function activate( $network_wide = false ) {
		$cron_scheduler   = new Cron_Scheduler();
		$post_types       = new Post_Types();
		$activator        = new Activator( $cron_scheduler, $post_types );
		$database_updater = new Database_Updater();

		$activator->activate( $network_wide );
		$database_updater->check_version();
	}

	/**
	 * Handles deactivation procedures.
	 *
	 * @since 0.1.0
	 */
	public function deactivate() {
		$cron_scheduler = new Cron_Scheduler();
		$deactivator    = new Deactivator( $cron_scheduler );
		$deactivator->deactivate();
	}
}
