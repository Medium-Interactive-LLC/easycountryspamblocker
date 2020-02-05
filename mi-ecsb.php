<?php

/**
 * The plugin bootstrap file.
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://mediuminteractive.com/
 * @since             1.0.0
 * @package           EasyCountrySpamBlocker
 *
 * @mi-ecsb
 * Plugin Name:       Easy Country Spam Blocker
 * Description:       Easily block non-US visitors out of your site with a custom redirect URL.
 * Version:           1.1.1
 * Author:            Medium Interactive, LLC
 * Author URI:        https://mediuminteractive.com/
 * Text Domain:       mi-ecsb
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( !defined( 'WPINC' ) )
{
    die;
}

/**
 * Current plugin version. Use SemVer - https://semver.org
 */
define( 'EASYCOUNTRYSPAMBLOCKER_VERSION', '1.1.1' );
define( 'EASYCOUNTRYSPAMBLOCKER_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
define( 'EASYCOUNTRYSPAMBLOCKER_BASE_PATH', plugin_dir_path( __FILE__ ) );

// Load the helper functions.
/**
 * The class responsible for offering various different helper methods throughout our codebase.
 */
require_once plugin_dir_path( __FILE__ ) . 'includes/class-mi-ecsb-helper.php';

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-mi-ecsb-activator.php
 */
function activate_ecsb()
{
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-mi-ecsb-activator.php';
	EasyCountrySpamBlocker_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-mi-ecsb-deactivator.php
 */
function deactivate_ecsb()
{
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-mi-ecsb-deactivator.php';
	EasyCountrySpamBlocker_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_ecsb' );
register_deactivation_hook( __FILE__, 'deactivate_ecsb' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-mi-ecsb.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_ecsb()
{
	$plugin = new EasyCountrySpamBlocker();
	$plugin->run();

}

run_ecsb();
