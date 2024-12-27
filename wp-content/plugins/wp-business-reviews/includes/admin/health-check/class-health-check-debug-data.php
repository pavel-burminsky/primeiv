<?php
/**
 * Defines the Health_Check_Debug_Data class
 *
 * @link https://github.com/WordPress/health-check
 *
 * @package WP_Business_Reviews\Includes\Admin\Health_Check
 * @since   1.2.0
 */

namespace WP_Business_Reviews\Includes\Admin\Health_Check;

use \RecursiveIteratorIterator;
use \RecursiveDirectoryIterator;

/**
 * Class Health_Check_Debug_Data
 */
class Health_Check_Debug_Data {

	/**
	 * Calls all core funtions to check for updates
	 *
	 * @uses wp_version_check()
	 * @uses wp_update_plugins()
	 * @uses wp_update_themes()
	 *
	 * @return void
	 */
	static function check_for_updates() {

		wp_version_check();
		wp_update_plugins();
		wp_update_themes();

	}

	static function debug_data( $locale = null ) {
		if ( ! empty( $locale ) ) {
			// Change the language used for translations
			if ( function_exists( 'switch_to_locale' ) ) {
				$original_locale = get_locale();
				$switched_locale = switch_to_locale( $locale );
			}
		}
		global $wpdb;

		$upload_dir = wp_upload_dir();
		if ( file_exists( ABSPATH . 'wp-config.php' ) ) {
			$wp_config_path = ABSPATH . 'wp-config.php';
			// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
		} elseif ( @file_exists( dirname( ABSPATH ) . '/wp-config.php' ) && ! @file_exists( dirname( ABSPATH ) . '/wp-settings.php' ) ) {
			$wp_config_path = dirname( ABSPATH ) . '/wp-config.php';
		}

		$core_current_version = get_bloginfo( 'version' );
		$core_updates         = get_core_updates();
		$core_update_needed   = '';

		foreach ( $core_updates as $core => $update ) {
			if ( 'upgrade' === $update->response ) {
				// translators: %s: Latest WordPress version number.
				$core_update_needed = ' ' . sprintf( __( '( Latest version: %s )', 'wp-business-reviews' ), $update->version );
			} else {
				$core_update_needed = '';
			}
		}

		$info = array(
			'wp-core'             => array(
				'label'  => __( 'WordPress', 'wp-business-reviews' ),
				'fields' => array(
					array(
						'label' => __( 'Version', 'wp-business-reviews' ),
						'value' => $core_current_version . $core_update_needed,
					),
					array(
						'label' => __( 'Language', 'wp-business-reviews' ),
						'value' => ( ! empty( $locale ) ? $original_locale : get_locale() ),
					),
					array(
						'label'   => __( 'Home URL', 'wp-business-reviews' ),
						'value'   => get_bloginfo( 'url' ),
					),
					array(
						'label'   => __( 'Site URL', 'wp-business-reviews' ),
						'value'   => get_bloginfo( 'wpurl' ),
					),
					array(
						'label' => __( 'Permalink Structure', 'wp-business-reviews' ),
						'value' => get_option( 'permalink_structure' ),
					),
					array(
						'label' => __( 'Is this site using HTTPS?', 'wp-business-reviews' ),
						'value' => ( is_ssl() ? __( 'Yes', 'wp-business-reviews' ) : __( 'No', 'wp-business-reviews' ) ),
					),
					array(
						'label' => __( 'Can anyone register on this site?', 'wp-business-reviews' ),
						'value' => ( get_option( 'users_can_register' ) ? __( 'Yes', 'wp-business-reviews' ) : __( 'No', 'wp-business-reviews' ) ),
					),
					array(
						'label' => __( 'Default Comment Status', 'wp-business-reviews' ),
						'value' => get_option( 'default_comment_status' ),
					),
					array(
						'label' => __( 'Is this a multisite?', 'wp-business-reviews' ),
						'value' => ( is_multisite() ? __( 'Yes', 'wp-business-reviews' ) : __( 'No', 'wp-business-reviews' ) ),
					),
				),
			),
			'wp-server'           => array(
				'label'       => __( 'Server', 'wp-business-reviews' ),
				'description' => __( 'The options shown below relate to your server setup. If changes are required, you may need your web host\'s assistance.', 'wp-business-reviews' ),
				'fields'      => array(),
			),
			'wp-active-theme'     => array(
				'label'  => __( 'Active Theme', 'wp-business-reviews' ),
				'fields' => array(),
			),
			'wp-themes'           => array(
				'label'      => __( 'Other Themes', 'wp-business-reviews' ),
				'show_count' => true,
				'fields'     => array(),
			),
			'wp-plugins-active'   => array(
				'label'      => __( 'Active Plugins', 'wp-business-reviews' ),
				'show_count' => true,
				'fields'     => array(),
			),
			'wp-plugins-inactive' => array(
				'label'      => __( 'Inactive Plugins', 'wp-business-reviews' ),
				'show_count' => true,
				'fields'     => array(),
			),
			'wp-mu-plugins'       => array(
				'label'      => __( 'Must-Use Plugins', 'wp-business-reviews' ),
				'show_count' => true,
				'fields'     => array(),
			),
			'wp-dropins'          => array(
				'label'       => __( 'Drop-Ins', 'wp-business-reviews' ),
				'description' => __( 'Drop-ins are single files that replace or enhance WordPress features in ways that are not possible for traditional plugins', 'wp-business-reviews' ),
				'fields'      => array(),
			),
			'wp-database'         => array(
				'label'  => __( 'Database', 'wp-business-reviews' ),
				'fields' => array(),
			),
			'wp-media'            => array(
				'label'  => __( 'Media Handling', 'wp-business-reviews' ),
				'fields' => array(),
			),
			'wp-constants'        => array(
				'label'       => __( 'WordPress Constants', 'wp-business-reviews' ),
				'description' => __( 'These values represent values set in your websites code which affect WordPress in various ways that may be of importance when seeking help with your site.', 'wp-business-reviews' ),
				'fields'      => array(
					array(
						'label' => 'ABSPATH',
						'value' => ( ! defined( 'ABSPATH' ) ? __( 'Undefined', 'wp-business-reviews' ) : ABSPATH ),
					),
					array(
						'label' => 'WP_HOME',
						'value' => ( ! defined( 'WP_HOME' ) ? __( 'Undefined', 'wp-business-reviews' ) : WP_HOME ),
					),
					array(
						'label' => 'WP_SITEURL',
						'value' => ( ! defined( 'WP_SITEURL' ) ? __( 'Undefined', 'wp-business-reviews' ) : WP_SITEURL ),
					),
					array(
						'label' => 'WP_DEBUG',
						'value' => ( ! defined( 'WP_DEBUG' ) ? __( 'Undefined', 'wp-business-reviews' ) : ( WP_DEBUG ? __( 'Enabled', 'wp-business-reviews' ) : __( 'Disabled', 'wp-business-reviews' ) ) ),
					),
					array(
						'label' => 'WP_MAX_MEMORY_LIMIT',
						'value' => ( ! defined( 'WP_MAX_MEMORY_LIMIT' ) ? __( 'Undefined', 'wp-business-reviews' ) : WP_MAX_MEMORY_LIMIT ),
					),
					array(
						'label' => 'WP_DEBUG_DISPLAY',
						'value' => ( ! defined( 'WP_DEBUG_DISPLAY' ) ? __( 'Undefined', 'wp-business-reviews' ) : ( WP_DEBUG_DISPLAY ? __( 'Enabled', 'wp-business-reviews' ) : __( 'Disabled', 'wp-business-reviews' ) ) ),
					),
					array(
						'label' => 'WP_DEBUG_LOG',
						'value' => ( ! defined( 'WP_DEBUG_LOG' ) ? __( 'Undefined', 'wp-business-reviews' ) : ( WP_DEBUG_LOG ? __( 'Enabled', 'wp-business-reviews' ) : __( 'Disabled', 'wp-business-reviews' ) ) ),
					),
					array(
						'label' => 'SCRIPT_DEBUG',
						'value' => ( ! defined( 'SCRIPT_DEBUG' ) ? __( 'Undefined', 'wp-business-reviews' ) : ( SCRIPT_DEBUG ? __( 'Enabled', 'wp-business-reviews' ) : __( 'Disabled', 'wp-business-reviews' ) ) ),
					),
					array(
						'label' => 'WP_CACHE',
						'value' => ( ! defined( 'WP_CACHE' ) ? __( 'Undefined', 'wp-business-reviews' ) : ( WP_CACHE ? __( 'Enabled', 'wp-business-reviews' ) : __( 'Disabled', 'wp-business-reviews' ) ) ),
					),
					array(
						'label' => 'CONCATENATE_SCRIPTS',
						'value' => ( ! defined( 'CONCATENATE_SCRIPTS' ) ? __( 'Undefined', 'wp-business-reviews' ) : ( CONCATENATE_SCRIPTS ? __( 'Enabled', 'wp-business-reviews' ) : __( 'Disabled', 'wp-business-reviews' ) ) ),
					),
					array(
						'label' => 'COMPRESS_SCRIPTS',
						'value' => ( ! defined( 'COMPRESS_SCRIPTS' ) ? __( 'Undefined', 'wp-business-reviews' ) : ( COMPRESS_SCRIPTS ? __( 'Enabled', 'wp-business-reviews' ) : __( 'Disabled', 'wp-business-reviews' ) ) ),
					),
					array(
						'label' => 'COMPRESS_CSS',
						'value' => ( ! defined( 'COMPRESS_CSS' ) ? __( 'Undefined', 'wp-business-reviews' ) : ( COMPRESS_CSS ? __( 'Enabled', 'wp-business-reviews' ) : __( 'Disabled', 'wp-business-reviews' ) ) ),
					),
					array(
						'label' => 'WP_LOCAL_DEV',
						'value' => ( ! defined( 'WP_LOCAL_DEV' ) ? __( 'Undefined', 'wp-business-reviews' ) : ( WP_LOCAL_DEV ? __( 'Enabled', 'wp-business-reviews' ) : __( 'Disabled', 'wp-business-reviews' ) ) ),
					),
					array(
						'label' => 'DISABLE_WP_CRON',
						'value' => ( ! defined( 'DISABLE_WP_CRON' ) ? __( 'Undefined', 'wp-business-reviews' ) : ( DISABLE_WP_CRON ? __( 'Enabled', 'wp-business-reviews' ) : __( 'Disabled', 'wp-business-reviews' ) ) ),
					),
				),
			),
			'wp-filesystem'       => array(
				'label'       => __( 'Filesystem Permissions', 'wp-business-reviews' ),
				'description' => __( 'The status of various locations WordPress needs to write files in various scenarios.', 'wp-business-reviews' ),
				'fields'      => array(
					array(
						'label' => __( 'Main WordPress Directory', 'wp-business-reviews' ),
						'value' => ( wp_is_writable( ABSPATH ) ? __( 'Writable', 'wp-business-reviews' ) : __( 'Not writable', 'wp-business-reviews' ) ),
					),
					array(
						'label' => __( 'Content Directory', 'wp-business-reviews' ),
						'value' => ( wp_is_writable( WP_CONTENT_DIR ) ? __( 'Writable', 'wp-business-reviews' ) : __( 'Not writable', 'wp-business-reviews' ) ),
					),
					array(
						'label' => __( 'Uploads Directory', 'wp-business-reviews' ),
						'value' => ( wp_is_writable( $upload_dir['basedir'] ) ? __( 'Writable', 'wp-business-reviews' ) : __( 'Not writable', 'wp-business-reviews' ) ),
					),
					array(
						'label' => __( 'Plugins Directory', 'wp-business-reviews' ),
						'value' => ( wp_is_writable( WP_PLUGIN_DIR ) ? __( 'Writable', 'wp-business-reviews' ) : __( 'Not writable', 'wp-business-reviews' ) ),
					),
					array(
						'label' => __( 'Themes Directory', 'wp-business-reviews' ),
						'value' => ( wp_is_writable( get_template_directory() . '/..' ) ? __( 'Writable', 'wp-business-reviews' ) : __( 'Not writable', 'wp-business-reviews' ) ),
					),
				),
			),
		);

		if ( is_multisite() ) {
			$network_query = new \WP_Network_Query();
			$network_ids   = $network_query->query( array(
				'fields'        => 'ids',
				'number'        => 100,
				'no_found_rows' => false,
			) );

			$site_count = 0;
			foreach ( $network_ids as $network_id ) {
				$site_count += get_blog_count( $network_id );
			}

			$info['wp-core']['fields'][] = array(
				'label' => __( 'User Count', 'wp-business-reviews' ),
				'value' => get_user_count(),
			);
			$info['wp-core']['fields'][] = array(
				'label' => __( 'Site Count', 'wp-business-reviews' ),
				'value' => $site_count,
			);
			$info['wp-core']['fields'][] = array(
				'label' => __( 'Network Count', 'wp-business-reviews' ),
				'value' => $network_query->found_networks,
			);
		} else {
			$user_count = count_users();

			$info['wp-core']['fields'][] = array(
				'label' => __( 'User Count', 'wp-business-reviews' ),
				'value' => $user_count['total_users'],
			);
		}

		// WordPress features requiring processing.
		$wp_dotorg = wp_remote_get( 'https://wordpress.org', array(
			'timeout' => 10,
		) );
		if ( ! is_wp_error( $wp_dotorg ) ) {
			$info['wp-core']['fields'][] = array(
				'label' => __( 'Communication with WordPress.org', 'wp-business-reviews' ),
				'value' => sprintf(
					__( 'WordPress.org is reachable', 'wp-business-reviews' )
				),
			);
		} else {
			$info['wp-core']['fields'][] = array(
				'label' => __( 'Communication with WordPress.org', 'wp-business-reviews' ),
				'value' => sprintf(
					// translators: %1$s: The IP address WordPress.org resolves to. %2$s: The error returned by the lookup.
					__( 'Unable to reach WordPress.org at %1$s: %2$s', 'wp-business-reviews' ),
					gethostbyname( 'wordpress.org' ),
					$wp_dotorg->get_error_message()
				),
			);
		}

		$loopback                    = Health_Check_Loopback::can_perform_loopback();
		$info['wp-core']['fields'][] = array(
			'label' => __( 'Create Loopback Requests', 'wp-business-reviews' ),
			'value' => $loopback->message,
		);

		// Get drop-ins.
		$dropins            = get_dropins();
		$dropin_description = _get_dropins();
		foreach ( $dropins as $dropin_key => $dropin ) {
			$info['wp-dropins']['fields'][] = array(
				'label' => $dropin_key,
				'value' => $dropin_description[ $dropin_key ][0],
			);
		}

		// Populate the media fields.
		$info['wp-media']['fields'][] = array(
			'label' => __( 'Active Editor', 'wp-business-reviews' ),
			'value' => _wp_image_editor_choose(),
		);

		// Get ImageMagic information, if available.
		if ( class_exists( '\Imagick' ) ) {
			// Save the Imagick instance for later use.
			$imagick         = new \Imagick();
			$imagick_version = $imagick->getVersion();
		} else {
			$imagick_version = 'Imagick not available';
		}
		$info['wp-media']['fields'][] = array(
			'label' => __( 'Imagick Module Version', 'wp-business-reviews' ),
			'value' => ( is_array( $imagick_version ) ? $imagick_version['versionNumber'] : $imagick_version ),
		);
		$info['wp-media']['fields'][] = array(
			'label' => __( 'ImageMagick Version', 'wp-business-reviews' ),
			'value' => ( is_array( $imagick_version ) ? $imagick_version['versionString'] : $imagick_version ),
		);

		// If Imagick is used as our editor, provide some more information about its limitations.
		if ( 'WP_Image_Editor_Imagick' === _wp_image_editor_choose() && isset( $imagick ) && $imagick instanceof Imagick ) {
			$limits = array(
				'area'   => ( defined( 'imagick::RESOURCETYPE_AREA' ) ? size_format( $imagick->getResourceLimit( imagick::RESOURCETYPE_AREA ) ) : 'Not Available' ),
				'disk'   => ( defined( 'imagick::RESOURCETYPE_DISK' ) ? $imagick->getResourceLimit( imagick::RESOURCETYPE_DISK ) : 'Not Available' ),
				'file'   => ( defined( 'imagick::RESOURCETYPE_FILE' ) ? $imagick->getResourceLimit( imagick::RESOURCETYPE_FILE ) : 'Not Available' ),
				'map'    => ( defined( 'imagick::RESOURCETYPE_MAP' ) ? size_format( $imagick->getResourceLimit( imagick::RESOURCETYPE_MAP ) ) : 'Not Available' ),
				'memory' => ( defined( 'imagick::RESOURCETYPE_MEMORY' ) ? size_format( $imagick->getResourceLimit( imagick::RESOURCETYPE_MEMORY ) ) : 'Not Available' ),
				'thread' => ( defined( 'imagick::RESOURCETYPE_THREAD' ) ? $imagick->getResourceLimit( imagick::RESOURCETYPE_THREAD ) : 'Not Available' ),
			);

			$info['wp-media']['fields'][] = array(
				'label' => __( 'Imagick Resource Limits', 'wp-business-reviews' ),
				'value' => $limits,
			);
		}

		// Get GD information, if available.
		if ( function_exists( 'gd_info' ) ) {
			$gd = gd_info();
		} else {
			$gd = false;
		}
		$info['wp-media']['fields'][] = array(
			'label' => __( 'GD Version', 'wp-business-reviews' ),
			'value' => ( is_array( $gd ) ? $gd['GD Version'] : __( 'GD not available', 'wp-business-reviews' ) ),
		);

		// Get Ghostscript information, if available.
		if ( function_exists( 'exec' ) ) {
			$gs = exec( 'gs --version' );
			$gs = ( ! empty( $gs ) ? $gs : __( 'Not available', 'wp-business-reviews' ) );
		} else {
			$gs = __( 'Unable to determine if Ghostscript is installed', 'wp-business-reviews' );
		}
		$info['wp-media']['fields'][] = array(
			'label' => __( 'Ghostscript Version', 'wp-business-reviews' ),
			'value' => $gs,
		);

		// Populate the server debug fields.
		$info['wp-server']['fields'][] = array(
			'label' => __( 'Server Architecture', 'wp-business-reviews' ),
			'value' => ( ! function_exists( 'php_uname' ) ? __( 'Unable to determine server architecture', 'wp-business-reviews' ) : sprintf( '%s %s %s', php_uname( 's' ), php_uname( 'r' ), php_uname( 'm' ) ) ),
		);
		$info['wp-server']['fields'][] = array(
			'label' => __( 'Web Server Software', 'wp-business-reviews' ),
			'value' => ( isset( $_SERVER['SERVER_SOFTWARE'] ) ? $_SERVER['SERVER_SOFTWARE'] : __( 'Unable to determine what web server software is used', 'wp-business-reviews' ) ),
		);
		$info['wp-server']['fields'][] = array(
			'label' => __( 'PHP Version', 'wp-business-reviews' ),
			'value' => ( ! function_exists( 'phpversion' ) ? __( 'Unable to determine PHP version', 'wp-business-reviews' ) : sprintf(
				'%s %s',
				phpversion(),
				( 64 === PHP_INT_SIZE * 8 ? __( '(Supports 64bit values)', 'wp-business-reviews' ) : __( '(Does not support 64bit values)', 'wp-business-reviews' ) )
			)
			),
		);
		$info['wp-server']['fields'][] = array(
			'label' => __( 'PHP SAPI', 'wp-business-reviews' ),
			'value' => ( ! function_exists( 'php_sapi_name' ) ? __( 'Unable to determine PHP SAPI', 'wp-business-reviews' ) : php_sapi_name() ),
		);

		if ( ! function_exists( 'ini_get' ) ) {
			$info['wp-server']['fields'][] = array(
				'label' => __( 'Server Settings', 'wp-business-reviews' ),
				'value' => __( 'Unable to determine some settings as the ini_get() function has been disabled', 'wp-business-reviews' ),
			);
		} else {
			$info['wp-server']['fields'][] = array(
				'label' => __( 'PHP Max Input Variables', 'wp-business-reviews' ),
				'value' => ini_get( 'max_input_vars' ),
			);
			$info['wp-server']['fields'][] = array(
				'label' => __( 'PHP Time Limit', 'wp-business-reviews' ),
				'value' => ini_get( 'max_execution_time' ),
			);
			$info['wp-server']['fields'][] = array(
				'label' => __( 'PHP Memory Limit', 'wp-business-reviews' ),
				'value' => ini_get( 'memory_limit' ),
			);
			$info['wp-server']['fields'][] = array(
				'label' => __( 'Max Input Time', 'wp-business-reviews' ),
				'value' => ini_get( 'max_input_time' ),
			);
			$info['wp-server']['fields'][] = array(
				'label' => __( 'Upload Max Filesize', 'wp-business-reviews' ),
				'value' => ini_get( 'upload_max_filesize' ),
			);
			$info['wp-server']['fields'][] = array(
				'label' => __( 'PHP Post Max Size', 'wp-business-reviews' ),
				'value' => ini_get( 'post_max_size' ),
			);
		}

		if ( function_exists( 'curl_version' ) ) {
			$curl                          = curl_version();
			$info['wp-server']['fields'][] = array(
				'label' => __( 'cURL Version', 'wp-business-reviews' ),
				'value' => sprintf( '%s %s', $curl['version'], $curl['ssl_version'] ),
			);
		} else {
			$info['wp-server']['fields'][] = array(
				'label' => __( 'cURL Version', 'wp-business-reviews' ),
				'value' => __( 'Your server does not support cURL', 'wp-business-reviews' ),
			);
		}

		$info['wp-server']['fields'][] = array(
			'label' => __( 'SUHOSIN Installed', 'wp-business-reviews' ),
			'value' => ( ( extension_loaded( 'suhosin' ) || ( defined( 'SUHOSIN_PATCH' ) && constant( 'SUHOSIN_PATCH' ) ) ) ? __( 'Yes', 'wp-business-reviews' ) : __( 'No', 'wp-business-reviews' ) ),
		);

		$info['wp-server']['fields'][] = array(
			'label' => __( 'Is the Imagick library available?', 'wp-business-reviews' ),
			'value' => ( extension_loaded( 'imagick' ) ? __( 'Yes', 'wp-business-reviews' ) : __( 'No', 'wp-business-reviews' ) ),
		);

		// Check if a .htaccess file exists.
		if ( is_file( ABSPATH . '/.htaccess' ) ) {
			// If the file exists, grab the content of it.
			$htaccess_content = file_get_contents( ABSPATH . '/.htaccess' );

			// Filter away the core WordPress rules.
			$filtered_htaccess_content = trim( preg_replace( '/\# BEGIN WordPress[\s\S]+?# END WordPress/si', '', $htaccess_content ) );

			$info['wp-server']['fields'][] = array(
				'label' => __( 'htaccess rules', 'wp-business-reviews' ),
				'value' => ( ! empty( $filtered_htaccess_content ) ? __( 'Custom rules have been added to your htaccess file', 'wp-business-reviews' ) : __( 'Your htaccess file only contains core WordPress features', 'wp-business-reviews' ) ),
			);
		}

		// Populate the database debug fields.
        if ( is_object( $wpdb->dbh ) ) {
			// mysqli or PDO.
			$extension = get_class( $wpdb->dbh );
		} else {
			// Unknown sql extension.
			$extension = null;
		}

		if ( method_exists( $wpdb, 'db_version' ) ) {
            $server = mysqli_get_server_info( $wpdb->dbh );
		} else {
			$server = null;
		}

        $client_version = $wpdb->dbh->client_info;

		$info['wp-database']['fields'][] = array(
			'label' => __( 'Extension', 'wp-business-reviews' ),
			'value' => $extension,
		);
		$info['wp-database']['fields'][] = array(
			'label' => __( 'Server Version', 'wp-business-reviews' ),
			'value' => $server,
		);
		$info['wp-database']['fields'][] = array(
			'label' => __( 'Client Version', 'wp-business-reviews' ),
			'value' => $client_version,
		);
		$info['wp-database']['fields'][] = array(
			'label'   => __( 'Database User', 'wp-business-reviews' ),
			'value'   => $wpdb->dbuser,
			'private' => true,
		);
		$info['wp-database']['fields'][] = array(
			'label'   => __( 'Database Host', 'wp-business-reviews' ),
			'value'   => $wpdb->dbhost,
			'private' => true,
		);
		$info['wp-database']['fields'][] = array(
			'label'   => __( 'Database Name', 'wp-business-reviews' ),
			'value'   => $wpdb->dbname,
			'private' => true,
		);
		$info['wp-database']['fields'][] = array(
			'label' => __( 'Database Prefix', 'wp-business-reviews' ),
			'value' => $wpdb->prefix,
		);

		// List must use plugins if there are any.
		$mu_plugins = get_mu_plugins();

		foreach ( $mu_plugins as $plugin_path => $plugin ) {
			$plugin_version = $plugin['Version'];
			$plugin_author  = $plugin['Author'];

			$plugin_version_string = __( 'No version or author information available', 'wp-business-reviews' );

			if ( ! empty( $plugin_version ) && ! empty( $plugin_author ) ) {
				// translators: %1$s: Plugin version number. %2$s: Plugin author name.
				$plugin_version_string = sprintf( __( 'Version %1$s by %2$s', 'wp-business-reviews' ), $plugin_version, $plugin_author );
			}
			if ( empty( $plugin_version ) && ! empty( $plugin_author ) ) {
				// translators: %s: Plugin author name.
				$plugin_version_string = sprintf( __( 'By %s', 'wp-business-reviews' ), $plugin_author );
			}
			if ( ! empty( $plugin_version ) && empty( $plugin_author ) ) {
				// translators: %s: Plugin version number.
				$plugin_version_string = sprintf( __( 'Version %s', 'wp-business-reviews' ), $plugin_version );
			}

			$info['wp-mu-plugins']['fields'][] = array(
				'label' => $plugin['Name'],
				'value' => $plugin_version_string,
			);
		}

		// List all available plugins.
		$plugins        = get_plugins();
		$plugin_updates = get_plugin_updates();

		foreach ( $plugins as $plugin_path => $plugin ) {
			$plugin_part = ( is_plugin_active( $plugin_path ) ) ? 'wp-plugins-active' : 'wp-plugins-inactive';

			$plugin_version = $plugin['Version'];
			$plugin_author  = $plugin['Author'];

			$plugin_version_string = __( 'No version or author information available', 'wp-business-reviews' );

			if ( ! empty( $plugin_version ) && ! empty( $plugin_author ) ) {
				// translators: %1$s: Plugin version number. %2$s: Plugin author name.
				$plugin_version_string = sprintf( __( 'Version %1$s by %2$s', 'wp-business-reviews' ), $plugin_version, $plugin_author );
			}
			if ( empty( $plugin_version ) && ! empty( $plugin_author ) ) {
				// translators: %s: Plugin author name.
				$plugin_version_string = sprintf( __( 'By %s', 'wp-business-reviews' ), $plugin_author );
			}
			if ( ! empty( $plugin_version ) && empty( $plugin_author ) ) {
				// translators: %s: Plugin version number.
				$plugin_version_string = sprintf( __( 'Version %s', 'wp-business-reviews' ), $plugin_version );
			}

			if ( array_key_exists( $plugin_path, $plugin_updates ) ) {
				// translators: %s: Latest plugin version number.
				$plugin_update_needed = ' ' . sprintf( __( '( Latest version: %s )', 'wp-business-reviews' ), $plugin_updates[ $plugin_path ]->update->new_version );
			} else {
				$plugin_update_needed = '';
			}

			$info[ $plugin_part ]['fields'][] = array(
				'label' => $plugin['Name'],
				'value' => $plugin_version_string . $plugin_update_needed,
			);
		}

		// Populate the section for the currently active theme.
		global $_wp_theme_features;
		$theme_features = array();
		if ( ! empty( $_wp_theme_features ) ) {
			foreach ( $_wp_theme_features as $feature => $options ) {
				$theme_features[] = $feature;
			}
		}

		$active_theme  = wp_get_theme();
		$theme_updates = get_theme_updates();

		if ( array_key_exists( $active_theme->stylesheet, $theme_updates ) ) {
			// translators: %s: Latest theme version number.
			$theme_update_needed_active = ' ' . sprintf( __( '( Latest version: %s )', 'wp-business-reviews' ), $theme_updates[ $active_theme->stylesheet ]->update['new_version'] );
		} else {
			$theme_update_needed_active = '';
		}

		$info['wp-active-theme']['fields'] = array(
			array(
				'label' => __( 'Name', 'wp-business-reviews' ),
				// phpcs:ignore WordPress.NamingConventions.ValidVariableName.NotSnakeCaseMemberVar
				'value' => $active_theme->Name,
			),
			array(
				'label' => __( 'Version', 'wp-business-reviews' ),
				// phpcs:ignore WordPress.NamingConventions.ValidVariableName.NotSnakeCaseMemberVar
				'value' => $active_theme->Version . $theme_update_needed_active,
			),
			array(
				'label' => __( 'Author', 'wp-business-reviews' ),
				// phpcs:ignore WordPress.NamingConventions.ValidVariableName.NotSnakeCaseMemberVar
				'value' => wp_kses( $active_theme->Author, array() ),
			),
			array(
				'label' => __( 'Author Website', 'wp-business-reviews' ),
				'value' => ( $active_theme->offsetGet( 'Author URI' ) ? $active_theme->offsetGet( 'Author URI' ) : __( 'Undefined', 'wp-business-reviews' ) ),
			),
			array(
				'label' => __( 'Parent Theme', 'wp-business-reviews' ),
				'value' => ( $active_theme->parent_theme ? $active_theme->parent_theme : __( 'Not a child theme', 'wp-business-reviews' ) ),
			),
			array(
				'label' => __( 'Supported Theme Features', 'wp-business-reviews' ),
				'value' => implode( ', ', $theme_features ),
			),
		);

		// Populate a list of all themes available in the install.
		$all_themes = wp_get_themes();

		foreach ( $all_themes as $theme_slug => $theme ) {
			// Ignore the currently active theme from the list of all themes.
			if ( $active_theme->stylesheet == $theme_slug ) {
				continue;
			}
			// phpcs:ignore WordPress.NamingConventions.ValidVariableName.NotSnakeCaseMemberVar
			$theme_version = $theme->Version;
			// phpcs:ignore WordPress.NamingConventions.ValidVariableName.NotSnakeCaseMemberVar
			$theme_author = $theme->Author;

			$theme_version_string = __( 'No version or author information available', 'wp-business-reviews' );

			if ( ! empty( $theme_version ) && ! empty( $theme_author ) ) {
				// translators: %1$s: Theme version number. %2$s: Theme author name.
				$theme_version_string = sprintf( __( 'Version %1$s by %2$s', 'wp-business-reviews' ), $theme_version, wp_kses( $theme_author, array() ) );
			}
			if ( empty( $theme_version ) && ! empty( $theme_author ) ) {
				// translators: %s: Theme author name.
				$theme_version_string = sprintf( __( 'By %s', 'wp-business-reviews' ), wp_kses( $theme_author, array() ) );
			}
			if ( ! empty( $theme_version ) && empty( $theme_author ) ) {
				// translators: %s: Theme version number.
				$theme_version_string = sprintf( __( 'Version %s', 'wp-business-reviews' ), $theme_version );
			}

			if ( array_key_exists( $theme_slug, $theme_updates ) ) {
				// translators: %s: Latest theme version number.
				$theme_update_needed = ' ' . sprintf( __( '( Latest version: %s )', 'wp-business-reviews' ), $theme_updates[ $theme_slug ]->update['new_version'] );
			} else {
				$theme_update_needed = '';
			}

			$info['wp-themes']['fields'][] = array(
				'label' => sprintf(
					// translators: %1$s: Theme name. %2$s: Theme slug.
					__( '%1$s (%2$s)', 'wp-business-reviews' ),
					// phpcs:ignore WordPress.NamingConventions.ValidVariableName.NotSnakeCaseMemberVar
					$theme->Name,
					$theme_slug
				),
				'value' => $theme_version_string . $theme_update_needed,
			);
		}

		// Add more filesystem checks
		if ( defined( 'WPMU_PLUGIN_DIR' ) && is_dir( WPMU_PLUGIN_DIR ) ) {
			$info['wp-filesystem']['fields'][] = array(
				'label' => __( 'Must-Use Plugins Directory', 'wp-business-reviews' ),
				'value' => ( wp_is_writable( WPMU_PLUGIN_DIR ) ? __( 'Writable', 'wp-business-reviews' ) : __( 'Not writable', 'wp-business-reviews' ) ),
			);
		}


		/**
		 * Add or modify new debug sections.
		 *
		 * Plugin or themes may wish to introduce their own debug information without creating additional admin pages for this
		 * kind of information as it is rarely needed, they can then utilize this filter to introduce their own sections.
		 *
		 * This filter intentionally does not include the fields introduced by core as those should always be un-modified
		 * and reliable for support related scenarios, take note that the core fields will take priority if a filtered value
		 * is trying to use the same array keys.
		 *
		 * Array keys added by core are all prefixed with `wp-`, plugins and themes are encouraged to use their own slug as
		 * a prefix, both for consistency as well as avoiding key collisions.
		 *
		 * @since 4.9.0
		 *
		 * @param array $args {
		 *     The debug information to be added to the core information page.
		 *
		 *     @type string  $label        The title for this section of the debug output.
		 *     @type string  $description  Optional. A description for your information section which may contain basic HTML
		 *                                 markup: `em`, `strong` and `a` for linking to documentation or putting emphasis.
		 *     @type boolean $show_count   Optional. If set to `true` the amount of fields will be included in the title for
		 *                                 this section.
		 *     @type boolean $private      Optional. If set to `true` the section and all associated fields will be excluded
		 *                                 from the copy-paste text area.
		 *     @type array   $fields {
		 *         An associative array containing the data to be displayed.
		 *
		 *         @type string  $label    The label for this piece of information.
		 *         @type string  $value    The output that is of interest for this field.
		 *         @type boolean $private  Optional. If set to `true` the field will not be included in the copy-paste text area
		 *                                 on top of the page, allowing you to show, for example, API keys here.
		 *     }
		 * }
		 */
		$external_info = apply_filters( 'debug_information', array() );

		// Merge the core and external debug fields.
		$info = array_replace_recursive( $info, array_replace_recursive( $external_info, $info ) );

		if ( ! empty( $locale ) ) {
			// Change the language used for translations
			if ( function_exists( 'restore_previous_locale' ) && $switched_locale ) {
				restore_previous_locale();
			}
		}

		return $info;
	}

	/**
	 * Print the formatted variation of the information gathered for debugging, in a manner
	 * suitable for a text area that can be instantly copied to a forum or support ticket.
	 *
	 * @param array $info_array
	 *
	 * @return void
	 */
	public static function textarea_format( $info_array ) {
		foreach ( $info_array as $section => $details ) {
			// Skip this section if there are no fields, or the section has been declared as private.
			if ( empty( $details['fields'] ) || ( isset( $details['private'] ) && $details['private'] ) ) {
				continue;
			}

			printf(
				"### %s%s ###\n\n",
				$details['label'],
				( isset( $details['show_count'] ) && $details['show_count'] ? sprintf( ' (%d)', count( $details['fields'] ) ) : '' )
			);

			foreach ( $details['fields'] as $field ) {
				if ( isset( $field['private'] ) && true === $field['private'] ) {
					continue;
				}

				$values = $field['value'];
				if ( is_array( $field['value'] ) ) {
					$values = '';

					foreach ( $field['value'] as $name => $value ) {
						$values .= sprintf(
							"\n\t%s: %s",
							$name,
							$value
						);
					}
				}

				printf(
					"%s: %s\n",
					$field['label'],
					$values
				);
			}
			echo "\n";
		}
	}
}
