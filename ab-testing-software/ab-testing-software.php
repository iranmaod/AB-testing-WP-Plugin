<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://themartechstack.com
 * @since             1.0.0
 * @package           Ab_Testing_Software
 *
 * @wordpress-plugin
 * Plugin Name:       AB Testing Software
 * Plugin URI:        https://themartechstack.com
 * Description:       This is a description of the plugin.
 * Version:           1.0.0
 * Author:            themartechstack.com
 * Author URI:        https://themartechstack.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       ab-testing-software
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
define( 'AB_TESTING_SOFTWARE_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-ab-testing-software-activator.php
 */
function activate_ab_testing_software() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-ab-testing-software-activator.php';
	Ab_Testing_Software_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-ab-testing-software-deactivator.php
 */
function deactivate_ab_testing_software() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-ab-testing-software-deactivator.php';
	Ab_Testing_Software_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_ab_testing_software' );
register_deactivation_hook( __FILE__, 'deactivate_ab_testing_software' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-ab-testing-software.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_ab_testing_software() {

	$plugin = new Ab_Testing_Software();
	$plugin->run();

}
run_ab_testing_software();
