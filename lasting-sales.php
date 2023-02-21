<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://lastingsales.com
 * @since             1.0.0
 * @package           Lasting_Sales
 *
 * @wordpress-plugin
 * Plugin Name:       LastingSales CRM
 * Plugin URI:        https://wordpress.org/plugins/lasting-sales
 * Description:       LastingSales is a CRM that simplifies the business by providing a centralized platform to manage Leads from Facebook, Website and Sales Calls and helps to track and measure the Sales Team Performance.
 * Version:           1.0.0
 * Author:            Lasting Sales
 * Author URI:        https://lastingsales.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       lasting-sales
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'LASTING_SALES_VERSION', '1.0.0' );

global $wpdb;
/** defining global constants */
define('LASTING_SALES_USER_CONFIG_TABLE_NAME', $wpdb->prefix.'lasting_sales_config');
define('LASTING_SALES_BASE_URL', 'https://api.beta.lastingsales.com');

define('LASTING_SALES_PLUGIN_WEBHOOK_SUBSCRIPTION_URL', LASTING_SALES_BASE_URL.'/api/v1/auth/check_token');
define('LASTING_SALES_PLUGIN_WEBHOOK_URL', LASTING_SALES_BASE_URL.'/api/v1/public/lead');
define('LASTING_SALES_PLUGIN_WEBHOOK_DEAL_URL', LASTING_SALES_BASE_URL.'/api/v1/public/deal');

define('LASTING_SALES_PLUGIN_WEBHOOK_LEADS_URL', LASTING_SALES_BASE_URL.'/api/v1/public/leads?token=');
define('LASTING_SALES_PLUGIN_WEBHOOK_WORKFLOW_URL', LASTING_SALES_BASE_URL.'/api/v1/public/workflows?token=');

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-lasting-sales-activator.php
 */
function activate_lasting_sales() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-lasting-sales-activator.php';
	Lasting_Sales_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-lasting-sales-deactivator.php
 */
function deactivate_lasting_sales() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-lasting-sales-deactivator.php';
	Lasting_Sales_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_lasting_sales' );
register_deactivation_hook( __FILE__, 'deactivate_lasting_sales' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-lasting-sales.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_lasting_sales() {

	$plugin = new Lasting_Sales();
	$plugin->run();

}
run_lasting_sales();
