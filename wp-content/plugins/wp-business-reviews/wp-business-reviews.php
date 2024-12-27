<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and starts the plugin.
 *
 * @link              https://wpbusinessreviews.com
 * @package           WP_Business_Reviews
 * @since             0.1.0
 *
 * @wordpress-plugin
 * Plugin Name:       WP Business Reviews
 * Plugin URI:        https://wpbusinessreviews.com
 * Description:       A WordPress plugin for showcasing your best reviews in style.
 * Version:           1.9.0
 * Requires at least: 4.9
 * Requires PHP:      5.6
 * Author:            Impress.org
 * Author URI:        https://impress.org
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wp-business-reviews
 * Domain Path:       /languages/
 */

namespace WP_Business_Reviews;

// Define plugin version in SemVer format.
define('WPBR_VERSION', '1.9.0');
// Define plugin environment ('local' or 'production').
define('WPBR_ENV', 'production');
// Define plugin root File.
define('WPBR_PLUGIN_FILE', __FILE__);
// Define plugin directory Path.
define('WPBR_PLUGIN_DIR', plugin_dir_path(WPBR_PLUGIN_FILE));
// Define plugin directory URL.
define('WPBR_PLUGIN_URL', plugin_dir_url(WPBR_PLUGIN_FILE));
// Define assets directory URL.
define('WPBR_ASSETS_URL', plugin_dir_url(WPBR_PLUGIN_FILE) . 'assets/dist/');

/**
 * Automatically loads files used throughout the plugin.
 */
require_once __DIR__ . '/vendor/autoload.php';

// Initialize the plugin.
(new Includes\Plugin())->register();
